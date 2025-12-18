<?php
require_once 'config_db.php';


$conn->query("ALTER TABLE user ADD COLUMN IF NOT EXISTS remember_token VARCHAR(64) NULL");
$conn->query("ALTER TABLE user ADD COLUMN IF NOT EXISTS remember_expiry DATETIME NULL");


$conn->query("ALTER TABLE user ADD COLUMN IF NOT EXISTS reset_token VARCHAR(64) NULL");
$conn->query("ALTER TABLE user ADD COLUMN IF NOT EXISTS reset_expiry DATETIME NULL");

echo "Auth columns added to users table.";
?>
