<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login();

$user_id = intval($_SESSION['user_id']);
$result = $conn->query("SELECT * FROM items WHERE owner_id = $user_id ORDER BY created_at DESC");

require 'layout_header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Listings</h1>
            <p class="text-gray-500">Manage the items you're renting out.</p>
        </div>
        <a href="items_add.php" class="bg-primary-500 text-white px-5 py-2.5 rounded-lg font-medium hover:bg-primary-600 transition shadow-md flex items-center gap-2">
            <i class='bx bx-plus'></i> Add Item
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <?php if ($result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4">Item</th>
                            <th class="px-6 py-4">Price</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="w-12 h-12 rounded-lg object-cover bg-gray-100">
                                    <span class="font-medium text-gray-900"><?php echo htmlspecialchars($row['title']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">
                                GH₵<?php echo $row['price_per_day']; ?> <span class="text-xs text-gray-400 font-normal">/day</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-bold">Active</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="items_edit.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-800 font-medium text-sm mr-3">Edit</a>
                                <a href="items_delete.php?id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700 font-medium text-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <p class="text-gray-500">You haven't listed any items yet.</p>
                <a href="items_add.php" class="text-primary-600 font-medium hover:underline mt-2 inline-block">List your first item</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'layout_footer.php'; ?>