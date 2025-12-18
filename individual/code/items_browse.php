<?php
require_once 'config_db.php';
require_once 'helpers_security.php';

$is_logged_in = isset($_SESSION['user_id']); 


$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_param = "%" . $search . "%";


$stmt = $conn->prepare("SELECT items.*, user.username, user.profile_image as owner_image FROM items JOIN user ON items.owner_id = user.id WHERE items.title LIKE ? ORDER BY items.created_at DESC");
$stmt->bind_param("s", $search_param);
$stmt->execute();
$result = $stmt->get_result();

require 'layout_header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Search Header -->
    <div class="bg-secondary-500 rounded-2xl p-8 mb-10 text-center shadow-lg bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
        <h1 class="text-3xl font-bold text-white mb-4">Find exactly what you need</h1>
        <form action="items_browse.php" method="GET" class="max-w-2xl mx-auto flex gap-2">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search for tools, cameras, camping gear..." class="flex-grow px-6 py-3 rounded-full border-none focus:ring-2 focus:ring-primary-500 outline-none shadow-sm text-gray-800">
            <button type="submit" class="bg-primary-500 text-white px-8 py-3 rounded-full font-bold hover:bg-primary-600 transition shadow-md">
                Search
            </button>
        </form>
    </div>

    <!-- Items Grid -->
    <?php if ($result->num_rows > 0): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition duration-300 flex flex-col h-full">
                <!-- Image -->
                <a href="items_view.php?id=<?php echo $row['id']; ?>" class="block relative h-48 bg-gray-200">
                    <img src="<?php echo htmlspecialchars($row['image_url']); ?>" class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3 bg-white bg-opacity-90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                        <?php echo htmlspecialchars($row['username']); ?>
                    </div>
                </a>

                <!-- Content -->
                <div class="p-5 flex-grow flex flex-col">
                    <a href="items_view.php?id=<?php echo $row['id']; ?>" class="block">
                        <h3 class="font-bold text-lg text-gray-900 mb-2 hover:text-primary-600 transition"><?php echo htmlspecialchars($row['title']); ?></h3>
                    </a>
                    <p class="text-gray-500 text-sm line-clamp-2 mb-4 flex-grow"><?php echo htmlspecialchars($row['description'] ?? 'No description available.'); ?></p>
                    
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-50">
                        <div>
                            <span class="text-primary-600 font-bold text-xl">GH₵<?php echo $row['price_per_day']; ?></span>
                            <span class="text-gray-400 text-xs">/day</span>
                        </div>

                        <?php if ($is_logged_in): ?>
                            <a href="items_view.php?id=<?php echo $row['id']; ?>" class="bg-gray-900 text-white px-4 py-2 rounded-full hover:bg-primary-600 transition shadow-sm text-sm font-bold">
                                View
                            </a>
                        <?php else: ?>
                            <a href="auth_login.php" class="text-sm font-medium text-gray-500 hover:text-primary-600">Login to Rent</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-20">
            <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <i class='bx bx-search text-4xl text-gray-400'></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700">No items found</h3>
            <p class="text-gray-500 mt-2">Try adjusting your search terms or browse all categories.</p>
            <a href="items_browse.php" class="inline-block mt-4 text-primary-600 font-medium hover:underline">Clear Search</a>
        </div>
    <?php endif; ?>

</div>

<?php require 'layout_footer.php'; ?>