<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Validation Failed");
    }

    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetype = $_FILES['profile_image']['type'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        
        $check = getimagesize($_FILES['profile_image']['tmp_name']);
        if($check === false) {
             $error = "File is not a valid image.";
        } elseif (!in_array($ext, $allowed)) {
             $error = "Only JPG, PNG and GIF allowed.";
        } else {
             $new_filename = "profile_" . $user_id . "_" . time() . "." . $ext;
             $target = "assets/profiles/" . $new_filename;
             
             if (!file_exists('assets/profiles')) {
                 mkdir('assets/profiles', 0777, true);
             }

             if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                 $stmt = $conn->prepare("UPDATE user SET profile_image = ? WHERE id = ?");
                 $stmt->bind_param("si", $target, $user_id);
                 $stmt->execute();
                 $success = "Profile image updated!";
             } else {
                 $error = "Failed to upload image.";
             }
        }
    }
}


$stmt = $conn->prepare("SELECT username, email, phone, profile_image, created_at FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();


if (!$user) {
    die("User not found.");
}


$borrowed_res = $conn->query("SELECT COUNT(*) as c FROM rentals WHERE renter_id = $user_id");
$borrowed = ($borrowed_res) ? $borrowed_res->fetch_assoc()['c'] : 0;

$listed_res = $conn->query("SELECT COUNT(*) as c FROM items WHERE owner_id = $user_id");
$listed = ($listed_res) ? $listed_res->fetch_assoc()['c'] : 0;

require 'layout_header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-12">
    
    <!-- Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border p-8 flex flex-col md:flex-row items-center gap-8 mb-8">
        <div class="relative group">
            <img src="<?php echo htmlspecialchars($user['profile_image'] ?? 'assets/default_user.png'); ?>" class="w-32 h-32 rounded-full object-cover border-4 border-gray-100 shadow-md">
            
            <!-- Upload Overlay -->
            <label for="fileInput" class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition duration-200">
                <i class='bx bx-camera text-white text-3xl'></i>
            </label>
        </div>
        
        <div class="text-center md:text-left flex-1">
            <h1 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($user['username']); ?></h1>
            <p class="text-gray-500 mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
            <?php if (!empty($user['phone'])): ?>
                <p class="text-gray-500 mb-4"><?php echo htmlspecialchars($user['phone']); ?></p>
            <?php endif; ?>
            <p class="text-sm text-gray-400">Member since <?php echo date("F Y", strtotime($user['created_at'])); ?></p>
        </div>

        <div class="flex gap-8 text-center bg-gray-50 p-4 rounded-lg">
            <div>
                <p class="text-2xl font-bold text-primary-600"><?php echo $borrowed; ?></p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Borrowed</p>
            </div>
            <div class="w-px bg-gray-200"></div>
            <div>
                <p class="text-2xl font-bold text-secondary-500"><?php echo $listed; ?></p>
                <p class="text-xs text-gray-500 uppercase tracking-wide">Listed</p>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Image Upload -->
    <form id="avatarForm" action="user_profile.php" method="POST" enctype="multipart/form-data" class="hidden">
        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
        <input type="file" name="profile_image" id="fileInput" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
    </form>

    <?php if ($success): ?>
        <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6 border border-green-200 text-center"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6 border border-red-200 text-center"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Recent Activity Section (Placeholder for expansion) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-bold text-lg mb-4 text-gray-800">Account Settings</h3>
            <ul class="space-y-3">
                <li><a href="settings.php?tab=profile" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 text-gray-600 group"><span class="group-hover:text-primary-600 transition">Edit Profile Information</span> <i class='bx bx-user'></i></a></li>
                <li><a href="settings.php?tab=general" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 text-gray-600 group"><span class="group-hover:text-primary-600 transition">Appearance / Theme</span> <i class='bx bx-palette'></i></a></li>
                <li><a href="settings.php?tab=security" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 text-gray-600 group"><span class="group-hover:text-primary-600 transition">Change Password</span> <i class='bx bx-lock-alt'></i></a></li>
            </ul>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-yellow-100 rounded-full opacity-50"></div>
            <h3 class="font-bold text-lg mb-4 text-gray-800">Pro Tip</h3>
            <p class="text-gray-600 text-sm leading-relaxed relative z-10">Adding a verified phone number increases trust and rental approval rates by 40%.</p>
        </div>
    </div>

</div>

<?php require 'layout_footer.php'; ?>