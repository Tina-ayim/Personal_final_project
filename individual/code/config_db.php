<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rental_platform";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$conn->set_charset("utf8mb4");


date_default_timezone_set('Africa/Accra');


$sql = "CREATE TABLE IF NOT EXISTS user (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    password_hash VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'assets/default_user.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);


$sql = "CREATE TABLE IF NOT EXISTS items (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    owner_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price_per_day DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES user(id) ON DELETE CASCADE
)";
$conn->query($sql);


$sql = "CREATE TABLE IF NOT EXISTS rentals (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    item_id INT(11) NOT NULL,
    renter_id INT(11) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'momo') DEFAULT 'cash',
    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (renter_id) REFERENCES user(id)
)";
$conn->query($sql);


$sql = "CREATE TABLE IF NOT EXISTS cart (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    item_id INT(11) NOT NULL,
    days INT(11) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
)";
$conn->query($sql);


$sql = "CREATE TABLE IF NOT EXISTS messages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    sender_id INT(11) NOT NULL,
    receiver_id INT(11) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES user(id),
    FOREIGN KEY (receiver_id) REFERENCES user(id)
)";
$conn->query($sql);


$sql = "CREATE TABLE IF NOT EXISTS reviews (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    rental_id INT(11) NOT NULL,
    reviewer_id INT(11) NOT NULL,
    rating INT(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rental_id) REFERENCES rentals(id),
    FOREIGN KEY (reviewer_id) REFERENCES user(id)
)";
$conn->query($sql);
?>
