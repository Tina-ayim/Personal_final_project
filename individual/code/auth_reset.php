<?php
require_once 'config_db.php';
$msg = "";
$error = "";
$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $pass = $_POST['password'];
    
    
    $stmt = $conn->prepare("SELECT id FROM user WHERE reset_token = ? AND reset_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $uid = $row['id'];
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        
        $upd = $conn->prepare("UPDATE user SET password_hash = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
        $upd->bind_param("si", $hash, $uid);
        if ($upd->execute()) {
            $msg = "Password reset successfully! <a href='auth_login.php' class='underline'>Login Now</a>";
        } else {
            $error = "Database error.";
        }
    } else {
        $error = "Invalid or expired token.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md max-w-md w-full">
        <h1 class="text-2xl font-bold mb-4">New Password</h1>
        
        <?php if ($msg) echo "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>$msg</div>"; ?>
        <?php if ($error) echo "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>$error</div>"; ?>

        <form method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="password" name="password" required placeholder="New Password" class="w-full border p-3 rounded-lg mb-4">
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold">Reset Password</button>
        </form>
    </div>
</body>
</html>
