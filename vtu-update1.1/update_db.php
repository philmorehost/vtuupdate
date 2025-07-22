<?php
require_once('includes/db.php');

try {
    $pdo->exec("ALTER TABLE `admins` ADD COLUMN `last_login` TIMESTAMP NULL;");
    echo "Database updated successfully.";
} catch (PDOException $e) {
    die("ERROR: Could not update the database. " . $e->getMessage());
}
?>
