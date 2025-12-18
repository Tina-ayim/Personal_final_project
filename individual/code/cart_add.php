<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Verification Failed");
    }

    $user_id = $_SESSION['user_id'];
    $item_id = intval($_POST['item_id']);
    $days = isset($_POST['days']) ? intval($_POST['days']) : 1;
    if ($days < 1) $days = 1;

    
    $check_item = $conn->prepare("SELECT owner_id FROM items WHERE id = ?");
    $check_item->bind_param("i", $item_id);
    $check_item->execute();
    $result = $check_item->get_result();

    if ($result->num_rows == 0) {
        die("Item not found");
    }

    $item = $result->fetch_assoc();
    if ($item['owner_id'] == $user_id) {
         
         header("Location: items_browse.php"); 
         exit();
    }

    
    $check_cart = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND item_id = ?");
    $check_cart->bind_param("ii", $user_id, $item_id);
    $check_cart->execute();
    
    if ($check_cart->get_result()->num_rows > 0) {
        
        $update = $conn->prepare("UPDATE cart SET days = ? WHERE user_id = ? AND item_id = ?");
        $update->bind_param("iii", $days, $user_id, $item_id);
        $update->execute();
    } else {
        
        $stmt = $conn->prepare("INSERT INTO cart (user_id, item_id, days) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $user_id, $item_id, $days);
        $stmt->execute();
    }
    
    header("Location: cart_view.php");
    exit();
}
?>
