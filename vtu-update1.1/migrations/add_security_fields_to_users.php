<?php
require_once('../includes/db.php');

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN failed_login_attempts INT(11) NOT NULL DEFAULT 0");
    $pdo->exec("ALTER TABLE users ADD COLUMN suspended_until DATETIME NULL DEFAULT NULL");
    echo "Migration successful!";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
