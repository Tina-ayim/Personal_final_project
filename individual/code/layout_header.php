<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");


require_once 'config_db.php'; 

error_reporting(E_ALL);
ini_set('display_errors', 1);


$is_logged_in = isset($_SESSION['user_id']);


if (!$is_logged_in && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    $stmt = $conn->prepare("SELECT id, username FROM user WHERE remember_token = ? AND remember_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $is_logged_in = true;
    }
}

$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User';
$theme_class = '';

if ($is_logged_in) {
    $uid = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT theme_preference FROM user WHERE id = ?");
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($row['theme_preference'] === 'dark') {
            $theme_class = 'dark';
        }
    }
}


function is_active($page) {
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $page ? 'text-primary-600 font-semibold' : 'text-gray-600 hover:text-primary-600';
}
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo $theme_class; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf9',
                            100: '#ccfbf1',
                            500: '#008f68', // Brand Green
                            600: '#007f5d',
                        },
                        secondary: {
                            500: '#0e3742', // Brand Dark Blue
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* CustomScrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Dark mode overrides if needed */
        .dark body { background-color: #1a202c; color: #e2e8f0; }
        .dark .bg-white { background-color: #2d3748; color: #e2e8f0; border-color: #4a5568; }
        .dark .text-gray-900 { color: #f7fafc; }
        .dark .text-gray-800 { color: #edf2f7; }
        .dark .text-gray-700 { color: #e2e8f0; }
        .dark .text-gray-600 { color: #cbd5e1; }
        .dark .text-gray-500 { color: #a0aec0; }
        .dark .border-gray-100 { border-color: #4a5568; }
        .dark .hover\:bg-gray-50:hover { background-color: #4a5568; }
    </style>
    <!-- Force Reload on Back Button (BFCache Fix) -->
    <script>
        window.addEventListener("pageshow", function(event) {
            var historyTraversal = event.persisted || 
                                 (typeof window.performance != "undefined" && 
                                  window.performance.navigation.type === 2);
            if (historyTraversal) {
                // Page was loaded from cache (back button), force reload to check session
                window.location.reload();
            }
        });
    </script>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="<?php echo $is_logged_in ? 'dashboard.php' : 'index.php'; ?>" class="flex items-center gap-2">
                        <i class='bx bxs-building-house text-3xl text-primary-500'></i>
                        <span class="font-bold text-xl tracking-tight text-secondary-500">Com.Rental</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="items_browse.php" class="<?php echo is_active('items_browse.php'); ?>">Browse</a>
                    
                    <?php if ($is_logged_in): ?>
                        <a href="dashboard.php" class="<?php echo is_active('dashboard.php'); ?>">Dashboard</a>
                        <a href="items_manage.php" class="<?php echo is_active('items_manage.php'); ?>">My Listings</a>
                        <a href="cart_view.php" class="<?php echo is_active('cart_view.php'); ?>">
                            <i class='bx bx-cart text-xl'></i>
                        </a>
                        
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 focus:outline-none">
                                <span class="font-medium text-sm"><?php echo htmlspecialchars($username); ?></span>
                                <i class='bx bx-chevron-down'></i>
                            </button>
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 w-48 bg-white rounded-md shadow-lg py-1 hidden group-hover:block transition-all duration-200 border">
                                <a href="user_profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="rentals_history.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">History</a>
                                <a href="messages_inbox.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Messages</a>
                                <div class="border-t my-1"></div>
                                <a href="auth_logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="auth_login.php" class="text-secondary-500 font-medium hover:text-primary-500 transition">Log In</a>
                        <a href="auth_register.php" class="bg-primary-500 text-white px-5 py-2 rounded-full font-medium hover:bg-primary-600 transition shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Sign Up</a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex md:hidden items-center">
                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-gray-600 hover:text-primary-600 focus:outline-none">
                        <i class='bx bx-menu text-3xl'></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="items_browse.php" class="block px-3 py-2 text-base font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'items_browse.php' ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50'; ?>">Browse</a>
                
                <?php if ($is_logged_in): ?>
                    <a href="dashboard.php" class="block px-3 py-2 text-base font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50'; ?>">Dashboard</a>
                    <a href="items_manage.php" class="block px-3 py-2 text-base font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'items_manage.php' ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50'; ?>">My Listings</a>
                    <a href="cart_view.php" class="block px-3 py-2 text-base font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'cart_view.php' ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50'; ?>">Cart</a>
                    <a href="user_profile.php" class="block px-3 py-2 text-base font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'user_profile.php' ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50'; ?>">Profile</a>
                    <a href="messages_inbox.php" class="block px-3 py-2 text-base font-medium rounded-md <?php echo basename($_SERVER['PHP_SELF']) == 'messages_inbox.php' ? 'bg-primary-50 text-primary-600' : 'text-gray-700 hover:bg-gray-50'; ?>">Messages</a>
                    <div class="border-t my-2"></div>
                    <a href="auth_logout.php" class="block px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-md">Logout</a>
                <?php else: ?>
                    <a href="auth_login.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 rounded-md">Log In</a>
                    <a href="auth_register.php" class="block px-3 py-2 text-base font-medium bg-primary-500 text-white rounded-md hover:bg-primary-600">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <main class="flex-grow">
