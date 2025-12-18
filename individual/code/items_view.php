<?php
require_once 'config_db.php';
require_once 'helpers_security.php';

$is_logged_in = isset($_SESSION['user_id']);
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;


$stmt = $conn->prepare("SELECT items.*, user.id as owner_id, user.username as owner_name, user.profile_image as owner_image, user.phone as owner_phone FROM items JOIN user ON items.owner_id = user.id WHERE items.id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    echo "Item not found.";
    exit;
}


$images_stmt = $conn->prepare("SELECT image_url FROM item_images WHERE item_id = ?");
$images_stmt->bind_param("i", $item_id);
$images_stmt->execute();
$img_res = $images_stmt->get_result();
$gallery = [];
while ($row = $img_res->fetch_assoc()) {
    $gallery[] = $row['image_url'];
}



if (empty($gallery) && !empty($item['image_url'])) {
    $gallery[] = $item['image_url'];
}

require 'layout_header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Image Section -->
        <div class="md:w-1/2 bg-gray-100 p-4">
            <div class="mb-4">
                <img id="mainImage" src="<?php echo htmlspecialchars($item['image_url']); ?>" class="w-full h-96 object-cover rounded-lg shadow-md">
            </div>
            
            <?php if (count($gallery) > 1): ?>
            <div class="grid grid-cols-4 gap-2">
                <?php foreach($gallery as $img): ?>
                <div class="cursor-pointer border-2 border-transparent hover:border-primary-500 rounded-md overflow-hidden" onclick="document.getElementById('mainImage').src='<?php echo htmlspecialchars($img); ?>'">
                    <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-20 object-cover">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Details Section -->
        <div class="md:w-1/2 p-8 md:p-12 flex flex-col">
            <div class="flex-grow">
                <div class="flex items-center gap-4 mb-6">
                    <img src="<?php echo htmlspecialchars($item['owner_image'] ?? 'assets/default_user.png'); ?>" class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm">
                    <div>
                        <p class="text-sm text-gray-500">Listed by</p>
                        <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($item['owner_name']); ?></h4>
                    </div>
                </div>

                <h1 class="text-3xl font-extrabold text-gray-900 mb-4"><?php echo htmlspecialchars($item['title']); ?></h1>
                
                <div class="flex items-baseline gap-2 mb-6">
                    <span class="text-4xl font-bold text-primary-600">GH₵<?php echo $item['price_per_day']; ?></span>
                    <span class="text-gray-500 font-medium">/ day</span>
                </div>

                <p class="text-gray-600 leading-relaxed mb-8 text-lg">
                    <?php echo nl2br(htmlspecialchars($item['description'])); ?>
                </p>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                        <i class='bx bx-shield-quarter text-2xl text-primary-500 mb-2'></i>
                        <p class="text-sm font-bold text-gray-700">Verified Item</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-center">
                        <i class='bx bx-check-shield text-2xl text-primary-500 mb-2'></i>
                        <p class="text-sm font-bold text-gray-700">Insured</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="border-t pt-8 mt-auto">
                <?php if ($is_logged_in): ?>
                    <?php if ($_SESSION['user_id'] != $item['owner_id']): ?>
                        <form action="cart_add.php" method="POST" class="space-y-4">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Duration (Days)</label>
                                <div class="flex items-center gap-4">
                                    <button type="button" onclick="adjustDays(-1)" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center font-bold text-lg">-</button>
                                    <input type="number" id="days" name="days" value="1" min="1" max="30" class="w-16 text-center text-xl font-bold border-none focus:ring-0" readonly>
                                    <button type="button" onclick="adjustDays(1)" class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center font-bold text-lg">+</button>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button type="submit" class="flex-1 bg-black text-white py-4 rounded-xl font-bold text-lg hover:bg-gray-900 transition shadow-lg">
                                    Add to Cart
                                </button>
                                <a href="messages_inbox.php?user_id=<?php echo $item['owner_id']; ?>" class="px-4 py-4 rounded-xl border-2 border-gray-200 font-bold text-gray-700 hover:border-gray-900 hover:text-gray-900 transition" title="Message">
                                    <i class='bx bx-message-rounded-dots text-xl'></i>
                                </a>
                                <?php if (!empty($item['owner_phone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($item['owner_phone']); ?>" class="px-4 py-4 rounded-xl border-2 border-green-200 bg-green-50 text-green-700 hover:bg-green-100 hover:border-green-300 transition" title="Call Owner">
                                        <i class='bx bx-phone-call text-xl'></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="bg-yellow-50 text-yellow-800 p-4 rounded-xl text-center font-medium">
                            This is your listing. <a href="items_edit.php?id=<?php echo $item['id']; ?>" class="underline">Edit Item</a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="auth_login.php" class="block w-full bg-primary-600 text-white text-center py-4 rounded-xl font-bold hover:bg-primary-700 transition">
                        Login to Rent
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function adjustDays(n) {
    var input = document.getElementById('days');
    var val = parseInt(input.value) + n;
    if (val < 1) val = 1;
    if (val > 30) val = 30;
    input.value = val;
}
</script>

<?php require 'layout_footer.php'; ?>
