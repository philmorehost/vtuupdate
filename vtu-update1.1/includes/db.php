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

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `bank_settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `bank_name` VARCHAR(255) NOT NULL,
            `account_name` VARCHAR(255) NOT NULL,
            `account_number` VARCHAR(20) NOT NULL
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admins` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `last_login` TIMESTAMP NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Add columns to users table if they don't exist
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS failed_login_attempts INT(11) NOT NULL DEFAULT 0");
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS suspended_until DATETIME NULL DEFAULT NULL");
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>
