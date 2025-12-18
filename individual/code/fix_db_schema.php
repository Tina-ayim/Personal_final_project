<?php
require_once 'config_db.php';


try {
    $conn->query("ALTER TABLE rentals DROP FOREIGN KEY rentals_ibfk_1");
} catch (Exception $e) {  }

$sql = "ALTER TABLE rentals ADD CONSTRAINT rentals_ibfk_1 FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE";
if ($conn->query($sql) === TRUE) {
    echo "Successfully updated rentals constraint.\n";
} else {
    echo "Error updating rentals: " . $conn->error . "\n";
}


try {
    $conn->query("ALTER TABLE reviews DROP FOREIGN KEY reviews_ibfk_1");
} catch (Exception $e) {  }

$sql = "ALTER TABLE reviews ADD CONSTRAINT reviews_ibfk_1 FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE CASCADE";
if ($conn->query($sql) === TRUE) {
    echo "Successfully updated reviews constraint.\n";
} else {
    echo "Error updating reviews: " . $conn->error . "\n";
}

echo "Database schema update complete.";
?>
