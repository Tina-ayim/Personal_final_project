<?php
require 'config_db.php';

echo "<h2>Database Repair Tool</h2>";


echo "Checking 'reviews' table...<br>";
$columns = $conn->query("SHOW COLUMNS FROM reviews");
$has_rental_id = false;
while($col = $columns->fetch_assoc()) {
    if ($col['Field'] == 'rental_id') {
        $has_rental_id = true;
    }
}

if (!$has_rental_id) {
    echo "Column 'rental_id' missing. Attempting to add...<br>";
    
    $sql = "ALTER TABLE reviews ADD COLUMN rental_id INT(11) NOT NULL AFTER id";
    if ($conn->query($sql) === TRUE) {
        echo "<b style='color:green'>SUCCESS: 'rental_id' column added.</b><br>";
        
        
        $conn->query("ALTER TABLE reviews ADD FOREIGN KEY (rental_id) REFERENCES rentals(id)");
    } else {
        echo "<b style='color:red'>ERROR adding column: " . $conn->error . "</b><br>";
        echo "Trying alternative: Recreating table...<br>";
        
        
        $conn->query("DROP TABLE reviews");
        $sql = "CREATE TABLE reviews (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            rental_id INT(11) NOT NULL,
            reviewer_id INT(11) NOT NULL,
            rating INT(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (rental_id) REFERENCES rentals(id),
            FOREIGN KEY (reviewer_id) REFERENCES user(id)
        )";
        if ($conn->query($sql) === TRUE) {
            echo "<b style='color:green'>SUCCESS: 'reviews' table recreated with correct schema.</b><br>";
        } else {
            echo "<b style='color:red'>CRITICAL ERROR: Could not recreate table: " . $conn->error . "</b><br>";
        }
    }
} else {
    echo "<b style='color:green'>VERIFIED: 'reviews' table already has 'rental_id'.</b><br>";
}

echo "<br><a href='rentals_history.php'>Go back to Rental History</a>";
?>
