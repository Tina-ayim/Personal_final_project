<?php
require_once 'config_db.php';
require_once 'helpers_security.php';

$provider = $_GET['provider'] ?? 'unknown';


sleep(1);




$msg = "";
switch($provider) {
    case 'google':
    case 'facebook':
    case 'linkedin':
    case 'instagram':
        $msg = "We are currently integrating with " . ucfirst($provider) . ". Please log in with your email/password for now.";
        break;
    default:
        $msg = "Unknown login provider.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Login Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md max-w-md w-full text-center">
        <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class='bx bx-error text-3xl text-yellow-600'></i>
        </div>
        <h1 class="text-2xl font-bold mb-2">Integration In Progress</h1>
        <p class="text-gray-600 mb-6"><?php echo htmlspecialchars($msg); ?></p>
        <a href="auth_login.php" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Back to Login</a>
    </div>
    <!-- Load Boxicons for the icon -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</body>
</html>
