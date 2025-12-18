<?php
require 'config_db.php';

echo "<h2>Rentals Table Repair</h2>";


echo "Checking 'rentals' table for 'created_at'...<br>";
$columns = $conn->query("SHOW COLUMNS FROM rentals");
$has_created_at = false;
while($col = $columns->fetch_assoc()) {
    if ($col['Field'] == 'created_at') {
        $has_created_at = true;
    }
}

if (!$has_created_at) {
    echo "Column 'created_at' missing. Adding it now...<br>";
    $sql = "ALTER TABLE rentals ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    if ($conn->query($sql) === TRUE) {
        echo "<b style='color:green'>SUCCESS: 'created_at' column added to rentals.</b><br>";
    } else {
        echo "<b style='color:red'>ERROR adding column: " . $conn->error . "</b><br>";
    }
} else {
    echo "<b style='color:green'>VERIFIED: 'rentals' table already has 'created_at'.</b><br>";
}

echo "<br><a href='rentals_history.php'>Go back to Rental History</a>";
?>
