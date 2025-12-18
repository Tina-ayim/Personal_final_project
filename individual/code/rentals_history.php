<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login();

$user_id = $_SESSION['user_id'];
$rentals = $conn->query("
    SELECT rentals.*, items.title, items.image_url, 
           (SELECT COUNT(*) FROM reviews WHERE rental_id = rentals.id) as has_review 
    FROM rentals 
    JOIN items ON rentals.item_id = items.id 
    WHERE rentals.renter_id = $user_id 
    ORDER BY rentals.created_at DESC
");

require 'layout_header.php';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Rental History</h1>
        <p class="text-gray-500">Track your past and current rentals.</p>
    </div>

    <?php if ($rentals->num_rows > 0): ?>
        <div class="space-y-6">
            <?php while($row = $rentals->fetch_assoc()): 
                $status_color = match($row['status']) {
                    'active' => 'bg-green-100 text-green-700',
                    'completed' => 'bg-gray-100 text-gray-600',
                    'cancelled' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-600'
                };
            ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col sm:flex-row gap-6 items-center">
                <!-- Image -->
                <div class="flex-shrink-0">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="w-32 h-24 object-cover rounded-lg bg-gray-100">
                </div>

                <!-- Details -->
                <div class="flex-grow text-center sm:text-left">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2">
                        <h3 class="font-bold text-xl text-gray-900"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <span class="<?php echo $status_color; ?> px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide inline-block mt-2 sm:mt-0">
                            <?php echo $row['status']; ?>
                        </span>
                    </div>
                    
                    <div class="text-sm text-gray-600 space-y-1 mb-4">
                        <p><i class='bx bx-calendar'></i> <?php echo date("M d, Y", strtotime($row['start_date'])); ?> - <?php echo date("M d, Y", strtotime($row['end_date'])); ?></p>
                        <p><i class='bx bx-purchase-tag'></i> Total Cost: <strong>GH₵<?php echo $row['total_cost']; ?></strong> <span class="text-xs text-gray-400 border px-1 rounded ml-2 uppercase"><?php echo $row['payment_method']; ?></span></p>
                    </div>

                    <?php if (!$row['has_review']): ?>
                        <a href="rentals_rate.php?rental_id=<?php echo $row['id']; ?>" class="inline-flex items-center gap-2 text-primary-600 font-bold hover:underline text-sm">
                            <i class='bx bx-star'></i> Rate this Item
                        </a>
                    <?php else: ?>
                        <span class="text-yellow-500 font-medium text-sm flex items-center gap-1 justify-center sm:justify-start">
                            <i class='bx bxs-star'></i> Review Submitted
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="bg-blue-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-history text-3xl text-blue-500'></i>
            </div>
            <h3 class="font-bold text-lg text-gray-900">No rental history</h3>
            <p class="text-gray-500 mb-6">You haven't rented any items yet.</p>
            <a href="items_browse.php" class="text-primary-600 font-bold hover:underline">Start Browsing</a>
        </div>
    <?php endif; ?>
</div>

<?php require 'layout_footer.php'; ?>