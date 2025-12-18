<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function log_security_event($event, $details = "") {
    $log_file = __DIR__ . '/security_events.log';
    $timestamp = date('Y-m-d H:i:s');
    $user_id = $_SESSION['user_id'] ?? 'guest';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $message = "[$timestamp] [User: $user_id] [IP: $ip] [Event: $event] $details" . PHP_EOL;
    
    @file_put_contents($log_file, $message, FILE_APPEND | LOCK_EX);
}


function check_rate_limit($action, $limit = 5, $seconds = 60) {
    $key = 'rate_limit_' . $action . '_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    $current_time = time();
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 1, 'start_time' => $current_time];
    } else {
        $window_start = $_SESSION[$key]['start_time'];
        if (($current_time - $window_start) > $seconds) {
            
            $_SESSION[$key] = ['count' => 1, 'start_time' => $current_time];
        } else {
            $_SESSION[$key]['count']++;
            if ($_SESSION[$key]['count'] > $limit) {
                log_security_event("RATE_LIMIT_EXCEEDED", "Action: $action");
                die("Too many requests. Please try again later.");
            }
        }
    }
}


function validate_url($url) {
    
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    return true;
}


function check_item_ownership($conn, $item_id, $user_id) {
    $stmt = $conn->prepare("SELECT owner_id FROM items WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        return false; 
    }
    $row = $result->fetch_assoc();
    return $row['owner_id'] == $user_id;
}


function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}


function verify_csrf_token($token) {
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    log_security_event("CSRF_FAILURE", "Invalid token provided");
    return false;
}


function h($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}


function require_login() {
    
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    if (!isset($_SESSION['user_id'])) {
        header("Location: auth_login.php");
        exit();
    }
}
?>
