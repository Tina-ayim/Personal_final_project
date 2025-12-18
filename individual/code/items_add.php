<?php
require_once 'config_db.php';
require_once 'helpers_security.php';

require_login();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Validation Failed");
    }

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $user_id = $_SESSION['user_id'];
    
    
    $image_url = 'assets/default.jpg'; 
    $uploaded_images = [];

    if (isset($_FILES['images'])) {
        $files = $_FILES['images'];
        $count = count($files['name']);
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        
        
        if (!file_exists('uploads')) {
            mkdir('uploads', 0777, true);
        }

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] == 0) {
                $filename = $files['name'][$i];
                $filetype = pathinfo($filename, PATHINFO_EXTENSION);
                
                if (in_array(strtolower($filetype), $allowed)) {
                    $new_filename = uniqid() . "_" . $i . "." . $filetype;
                    $upload_path = 'uploads/' . $new_filename;
                    
                    if (move_uploaded_file($files['tmp_name'][$i], $upload_path)) {
                        $uploaded_images[] = $upload_path;
                        
                        if ($image_url === 'assets/default.jpg') {
                            $image_url = $upload_path;
                        }
                    }
                }
            }
        }
    }
    
    
    if (empty($error)) {
        if (empty($title) || $price <= 0) {
            $error = "Title and valid price are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO items (owner_id, title, description, price_per_day, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issds", $user_id, $title, $description, $price, $image_url);
            
            if ($stmt->execute()) {
                $item_id = $stmt->insert_id;
                
                
                if (!empty($uploaded_images)) {
                    $img_stmt = $conn->prepare("INSERT INTO item_images (item_id, image_url) VALUES (?, ?)");
                    foreach ($uploaded_images as $img_path) {
                        $img_stmt->bind_param("is", $item_id, $img_path);
                        $img_stmt->execute();
                    }
                }

                $success = "Item listed successfully!";
                header("Location: items_manage.php"); 
                exit();
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    }

    if (empty($error)) {
        if (empty($title) || $price <= 0) {
            $error = "Title and valid price are required.";
        } else {
            $stmt = $conn->prepare("INSERT INTO items (owner_id, title, description, price_per_day, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issds", $user_id, $title, $description, $price, $image_url);
            
            if ($stmt->execute()) {
                $success = "Item listed successfully!";
                header("Location: items_manage.php"); 
                exit();
            } else {
                $error = "Database error: " . $conn->error;
            }
        }
    }
}
?>
<?php require 'layout_header.php'; ?>

    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="header mb-6">
            <h1 class="text-2xl font-bold text-gray-900">List a New Item</h1>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">
            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm">
                    <?php echo h($error); ?>
                </div>
            <?php endif; ?>

            <form action="items_add.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Title</label>
                    <input type="text" name="title" required placeholder="e.g. Cordless Drill" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" placeholder="Describe the item condition and features..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price Per Day (GH₵)</label>
                    <input type="number" step="0.01" name="price" required placeholder="0.00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Images (Select Multiple)</label>
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">First image will be the cover.</p>
                </div>

                <button type="submit" class="w-full bg-primary-600 text-white py-3 rounded-lg font-bold hover:bg-primary-700 transition">Post Listing</button>
            </form>
        </div>
    </div>

<?php require 'layout_footer.php'; ?>
