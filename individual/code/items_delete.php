<?php
require_once 'config_db.php';
require_once 'helpers_security.php';

require_login();

if (isset($_GET['id'])) {
    $item_id = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $item_id, $user_id);
    
    if ($stmt->execute()) {
        header("Location: items_manage.php?msg=deleted");
    } else {
        die("Error deleting item or access denied.");
    }
} else {
    header("Location: items_manage.php");
}
?>
