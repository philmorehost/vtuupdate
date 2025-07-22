<?php
require_once('includes/db.php');

try {
    $pdo->exec("ALTER TABLE `site_settings` ADD COLUMN `referral_bonus_tier1` DECIMAL(5, 2) DEFAULT 0.00, ADD COLUMN `referral_bonus_tier2` DECIMAL(5, 2) DEFAULT 0.00;");
    echo "Database updated successfully.";
} catch (PDOException $e) {
    die("ERROR: Could not update the database. " . $e->getMessage());
}
?>
