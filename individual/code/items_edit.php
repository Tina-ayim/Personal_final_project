<?php
require_once 'config_db.php';
require_once 'helpers_security.php';

require_login();

$user_id = $_SESSION['user_id'];
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';


$stmt = $conn->prepare("SELECT * FROM items WHERE id = ? AND owner_id = ?");
$stmt->bind_param("ii", $item_id, $user_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();

if (!$item) {
    die("Item not found or access denied.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Validation Failed");
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = $item['image_url'];

    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
             
             if (!file_exists('uploads')) {
                mkdir('uploads', 0777, true);
            }

            $new_filename = uniqid() . "." . $filetype;
            $upload_path = 'uploads/' . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_url = $upload_path;
            } else {
                $error = "Failed to upload image.";
            }
        }
    }

    
    if (isset($_FILES['images'])) {
        $files = $_FILES['images'];
        $count = count($files['name']);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] == 0) {
                $filename = $files['name'][$i];
                $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                
                if (in_array(strtolower($filetype), $allowed)) {
                    $new_filename = uniqid() . "_extra_" . $i . "." . $filetype;
                    $upload_path = 'uploads/' . $new_filename;
                    
                    if (move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
                        $conn->query("INSERT INTO item_images (item_id, image_url) VALUES ($item_id, '$upload_path')");
                    }
                }
            }
        }
    }

    if (empty($error)) {
        $update_stmt = $conn->prepare("UPDATE items SET title=?, description=?, price_per_day=?, image_url=? WHERE id=? AND owner_id=?");
        $update_stmt->bind_param("ssdsii", $title, $description, $price, $image_url, $item_id, $user_id);
        
        if ($update_stmt->execute()) {
            $success = "Item updated successfully!";
            
            $item['title'] = $title;
            $item['description'] = $description;
            $item['price_per_day'] = $price;
            $item['image_url'] = $image_url;
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}
?>
<?php require 'layout_header.php'; ?>

    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Listing</h1>
            <a href="items_manage.php" class="text-gray-600 hover:text-gray-900 font-medium">Back to Listings</a>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
            <?php if ($success): ?>
                <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-6 text-sm">
                    <?php echo h($success); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm">
                    <?php echo h($error); ?>
                </div>
            <?php endif; ?>

            <form action="items_edit.php?id=<?php echo $item_id; ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Title</label>
                    <input type="text" name="title" value="<?php echo h($item['title']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"><?php echo h($item['description']); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Per Day (GH₵)</label>
                    <input type="number" step="0.01" name="price" value="<?php echo h($item['price_per_day']); ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Cover Image</label>
                    <div class="mb-4">
                        <img src="<?php echo h($item['image_url']); ?>" class="w-24 h-24 object-cover rounded-lg border">
                    </div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Change Cover Image (Optional)</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add More Gallery Images</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg font-bold hover:bg-primary-700 transition">Update Listing</button>
            </form>
        </div>
    </div>

<?php require 'layout_footer.php'; ?>
