<?php
require_once 'config_db.php';

$sql = "CREATE TABLE IF NOT EXISTS item_images (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    item_id INT(11) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table item_images created successfully.\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}
?>
