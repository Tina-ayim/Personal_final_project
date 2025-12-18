<?php
require_once 'config_db.php';
require_once 'helpers_security.php';

require_login();

if (isset($_GET['id'])) {
    $cart_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: cart_view.php");
        exit();
    } else {
        die("Error removing item: " . $conn->error);
    }
} else {
    header("Location: cart_view.php");
    exit();
}
?>
