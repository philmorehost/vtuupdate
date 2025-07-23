<?php
require_once('../includes/db.php');

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS visitors (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(255) NOT NULL,
            browser VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Migration successful!";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage());
}
