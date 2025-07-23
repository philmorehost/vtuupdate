<?php
require_once('config.php');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create visitors table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS visitors (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(255) NOT NULL,
            browser VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
