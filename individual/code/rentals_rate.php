<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login(); 

$rental_id = isset($_GET['rental_id']) ? intval($_GET['rental_id']) : 0;
$user_id = $_SESSION['user_id'];
$success = '';
$error = '';


$check = $conn->prepare("SELECT rentals.id, items.title, items.image_url FROM rentals JOIN items ON rentals.item_id = items.id WHERE rentals.id = ? AND rentals.renter_id = ?");
$check->bind_param("ii", $rental_id, $user_id);
$check->execute();
$rental = $check->get_result()->fetch_assoc();

if (!$rental) {
    die("Invalid rental request.");
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Error");
    }

    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    
    $dup = $conn->query("SELECT id FROM reviews WHERE rental_id = $rental_id");
    if ($dup->num_rows > 0) {
        $error = "You have already reviewed this rental.";
    } else {
        $stmt = $conn->prepare("INSERT INTO reviews (rental_id, reviewer_id, rating, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $rental_id, $user_id, $rating, $comment);
        if ($stmt->execute()) {
            $success = "Review submitted successfully!";
        } else {
            $error = "Failed to submit review.";
        }
    }
}

require 'layout_header.php';
?>

<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        
        <!-- Header -->
        <div class="bg-gray-50 p-8 text-center border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Rate your Experience</h1>
            <p class="text-gray-500">How was renting the <span class="font-bold text-gray-800"><?php echo htmlspecialchars($rental['title']); ?></span>?</p>
            <img src="<?php echo htmlspecialchars($rental['image_url']); ?>" class="w-24 h-24 object-cover rounded-lg shadow-sm mx-auto mt-6 bg-white border p-1">
        </div>

        <div class="p-8">
            <?php if ($success): ?>
                <div class="bg-green-50 text-green-700 p-4 rounded-lg text-center mb-6">
                    <p class="font-bold"><?php echo $success; ?></p>
                    <a href="rentals_history.php" class="text-sm underline mt-2 inline-block">Back to History</a>
                </div>
            <?php elseif ($error): ?>
                <div class="bg-red-50 text-red-700 p-4 rounded-lg text-center mb-6">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form action="rentals_rate.php?rental_id=<?php echo $rental_id; ?>" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <!-- Star Rating -->
                <div class="flex justify-center flex-row-reverse gap-2">
                    <?php for($i=5; $i>=1; $i--): ?>
                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" class="peer hidden" required />
                        <label for="star<?php echo $i; ?>" class="text-4xl text-gray-300 cursor-pointer peer-checked:text-yellow-400 hover:text-yellow-400 transition">
                            <i class='bx bxs-star'></i>
                        </label>
                    <?php endfor; ?>
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Write a Review</label>
                    <textarea name="comment" rows="4" class="w-full border border-gray-300 rounded-xl p-4 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none" placeholder="Share your experience with others..."></textarea>
                </div>

                <button type="submit" class="w-full bg-primary-500 text-white py-3 rounded-xl font-bold hover:bg-primary-600 transition shadow-md">
                    Submit Review
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* Star Hover Magic */
    .peer:checked ~ label,
    .peer:hover ~ label,
    .peer:hover ~ label ~ label {
        color: #fbbf24; /* yellow-400 */
    }
</style>

<?php require 'layout_footer.php'; ?>
