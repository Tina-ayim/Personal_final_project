<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login();

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Validation Failed");
    }

    
    if (isset($_POST['update_theme'])) {
        $theme = $_POST['theme_preference'];
        if (in_array($theme, ['light', 'dark'])) {
            $stmt = $conn->prepare("UPDATE user SET theme_preference = ? WHERE id = ?");
            $stmt->bind_param("si", $theme, $user_id);
            if ($stmt->execute()) {
                $success = "Theme updated successfully!";
                $active_tab = 'general';
            } else {
                $error = "Failed to update theme.";
            }
        }
    }

    
    if (isset($_POST['update_profile'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        if (empty($username) || empty($email)) {
            $error = "Name and Email are required.";
        } else {
            
            $check = $conn->prepare("SELECT id FROM user WHERE email = ? AND id != ?");
            $check->bind_param("si", $email, $user_id);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error = "Email already in use.";
            } else {
                $stmt = $conn->prepare("UPDATE user SET username = ?, email = ?, phone = ? WHERE id = ?");
                $stmt->bind_param("sssi", $username, $email, $phone, $user_id);
                if ($stmt->execute()) {
                    $success = "Profile updated successfully!";
                    $_SESSION['user_name'] = $username; 
                    $active_tab = 'profile';
                } else {
                    $error = "Failed to update profile.";
                }
            }
        }
    }

    
    if (isset($_POST['change_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        $stmt = $conn->prepare("SELECT password_hash FROM user WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if (!password_verify($current_pass, $user_data['password_hash'])) {
            $error = "Current password is incorrect.";
        } elseif ($new_pass !== $confirm_pass) {
            $error = "New passwords do not match.";
        } elseif (strlen($new_pass) < 8) {
            $error = "New password must be at least 8 characters.";
        } else {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE user SET password_hash = ? WHERE id = ?");
            $update->bind_param("si", $new_hash, $user_id);
            if ($update->execute()) {
                $success = "Password changed successfully!";
                $active_tab = 'security';
            } else {
                $error = "Failed to update password.";
            }
        }
    }
}


$stmt = $conn->prepare("SELECT username, email, phone, theme_preference FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$current_theme = $user['theme_preference'] ?? 'light';

require 'layout_header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="md:grid md:grid-cols-4 md:gap-6">
        
        <!-- Sidebar Navigation -->
        <div class="md:col-span-1">
            <h2 class="text-xl font-bold text-gray-900 mb-6 px-4">Account Settings</h2>
            <nav class="space-y-1">
                <a href="?tab=general" class="<?php echo $active_tab == 'general' ? 'bg-primary-50 text-primary-700 border-l-4 border-primary-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-r-md transition-all duration-200">
                    <i class='bx bx-cog text-xl mr-3'></i> General
                </a>
                <a href="?tab=profile" class="<?php echo $active_tab == 'profile' ? 'bg-primary-50 text-primary-700 border-l-4 border-primary-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-r-md transition-all duration-200">
                    <i class='bx bx-user text-xl mr-3'></i> Profile Information
                </a>
                <a href="?tab=security" class="<?php echo $active_tab == 'security' ? 'bg-primary-50 text-primary-700 border-l-4 border-primary-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'; ?> group flex items-center px-3 py-3 text-sm font-medium rounded-r-md transition-all duration-200">
                    <i class='bx bx-shield-quarter text-xl mr-3'></i> Security & Password
                </a>
            </nav>
        </div>

        <!-- content area -->
        <div class="mt-5 md:mt-0 md:col-span-3">
            
            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $success; ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <!-- GENERAL TAB (THEME) -->
            <?php if ($active_tab == 'general'): ?>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Appearance</h3>
                        <div class="mt-6">
                            <form action="settings.php" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="update_theme" value="1">
                                
                                <label class="text-base font-medium text-gray-900">Theme Preference</label>
                                <p class="text-sm text-gray-500 mb-4">Choose how experience looks on your device.</p>
                                <fieldset class="mt-4">
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input id="theme_light" name="theme_preference" type="radio" value="light" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300" <?php echo ($current_theme == 'light') ? 'checked' : ''; ?>>
                                            <label for="theme_light" class="ml-3 block text-sm font-medium text-gray-700">
                                                Light Mode
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input id="theme_dark" name="theme_preference" type="radio" value="dark" class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300" <?php echo ($current_theme == 'dark') ? 'checked' : ''; ?>>
                                            <label for="theme_dark" class="ml-3 block text-sm font-medium text-gray-700">
                                                Dark Mode
                                            </label>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="mt-6">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Save Preferences
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- PROFILE TAB -->
            <?php if ($active_tab == 'profile'): ?>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Profile Information</h3>
                        <form action="settings.php?tab=profile" method="POST" class="mt-6 space-y-6">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="update_profile" value="1">

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="username" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                    <input type="tel" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Save Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- SECURITY TAB -->
            <?php if ($active_tab == 'security'): ?>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Change Password</h3>
                        <form action="settings.php?tab=security" method="POST" class="mt-6 space-y-6">
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <input type="hidden" name="change_password" value="1">

                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="new_password" id="new_password" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require 'layout_footer.php'; ?>
