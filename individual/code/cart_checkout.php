<?php
require_once 'config_db.php';
require_once 'helpers_security.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        die("CSRF Token Verification Failed");
    }

    $user_id = $_SESSION['user_id'];
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cash';
    
    
    if (!in_array($payment_method, ['cash', 'momo'])) {
        $payment_method = 'cash';
    }

    
    $cart = $conn->query("SELECT cart.id, cart.item_id, cart.days, items.price_per_day FROM cart JOIN items ON cart.item_id = items.id WHERE cart.user_id = $user_id");

    if ($cart->num_rows == 0) {
        header("Location: cart_view.php"); 
        exit();
    }

    
    $conn->begin_transaction();

    try {
        while($row = $cart->fetch_assoc()) {
            $item_id = $row['item_id'];
            $days = $row['days'];
            $base_cost = $row['price_per_day'] * $row['days'];
            
            $total_cost = $base_cost * 1.15;
            
            $start = date('Y-m-d');
            $end = date('Y-m-d', strtotime("+$days days"));

            
            $stmt = $conn->prepare("INSERT INTO rentals (item_id, renter_id, start_date, end_date, total_cost, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
            $stmt->bind_param("iissds", $item_id, $user_id, $start, $end, $total_cost, $payment_method);
            $stmt->execute();
        }

        
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");

        $conn->commit();
        header("Location: rentals_history.php?success=checkout");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Checkout failed: " . $e->getMessage());
    }
} else {
    header("Location: cart_view.php");
    exit();
}
?>
