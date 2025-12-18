<?php
require_once 'config_db.php';
$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    
    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + 3600); 
        
        $update = $conn->prepare("UPDATE user SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $update->bind_param("sss", $token, $expiry, $email);
        $update->execute();
        
        
        $link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/auth_reset.php?token=" . $token;
        $msg = "Password reset link has been sent to your email (Simulated): <a href='$link' class='underline font-bold'>Click Here</a>";
    } else {
        $error = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md max-w-md w-full">
        <h1 class="text-2xl font-bold mb-4">Reset Password</h1>
        <p class="text-gray-600 mb-6">Enter your email to receive a reset link.</p>
        
        <?php if ($msg) echo "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>$msg</div>"; ?>
        <?php if ($error) echo "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>$error</div>"; ?>

        <form method="POST">
            <input type="email" name="email" required placeholder="Enter your email" class="w-full border p-3 rounded-lg mb-4">
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold">Send Reset Link</button>
        </form>
        <div class="mt-4 text-center">
            <a href="auth_login.php" class="text-blue-600 hover:underline">Back to Login</a>
        </div>
    </div>
</body>
</html>
