<?php
require_once('includes/config.php');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
    $pdo->exec("USE `" . DB_NAME . "`");

    // Users table
    $sql = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `phone` VARCHAR(20) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `passcode` VARCHAR(255),
        `tier` INT DEFAULT 1,
        `wallet_balance` DECIMAL(10, 2) DEFAULT 0.00,
        `bonus_balance` DECIMAL(10, 2) DEFAULT 0.00,
        `referral_link` VARCHAR(255),
        `referred_by` INT,
        `status` VARCHAR(20) DEFAULT 'active',
        `api_key` VARCHAR(255),
        `api_enabled` BOOLEAN DEFAULT FALSE,
        `last_login` TIMESTAMP NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Transactions table
    $sql = "CREATE TABLE IF NOT EXISTS `transactions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `type` VARCHAR(50) NOT NULL,
        `description` VARCHAR(255) NOT NULL,
        `amount` DECIMAL(10, 2) NOT NULL,
        `status` VARCHAR(50) NOT NULL,
        `service_details` TEXT,
        `source` VARCHAR(20) DEFAULT 'Website',
        `balance_before` DECIMAL(10, 2),
        `balance_after` DECIMAL(10, 2),
        `batch_id` VARCHAR(255),
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    // Bank Details table
    $sql = "CREATE TABLE IF NOT EXISTS `bank_details` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `bank_name` VARCHAR(255) NOT NULL,
        `account_name` VARCHAR(255) NOT NULL,
        `account_number` VARCHAR(20) NOT NULL,
        `charge` DECIMAL(10, 2) DEFAULT 0.00,
        `instructions` TEXT
    )";
    $pdo->exec($sql);

    // Payment Orders table
    $sql = "CREATE TABLE IF NOT EXISTS `payment_orders` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `bank_id` INT,
        `amount` DECIMAL(10, 2) NOT NULL,
        `payment_proof` TEXT,
        `status` VARCHAR(20) DEFAULT 'pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (bank_id) REFERENCES bank_details(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql);

    // Site Settings table
    $sql = "CREATE TABLE IF NOT EXISTS `site_settings` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `site_name` VARCHAR(255) DEFAULT 'My VTU',
        `site_logo` VARCHAR(255),
        `auth_image` VARCHAR(255),
        `session_timeout` INT DEFAULT 30,
        `cache_control` VARCHAR(255) DEFAULT 'no-cache',
        `referral_bonus_tier1` DECIMAL(5, 2) DEFAULT 0.00,
        `referral_bonus_tier2` DECIMAL(5, 2) DEFAULT 0.00
    )";
    $pdo->exec($sql);
    $stmt = $pdo->query("SELECT id FROM site_settings WHERE id = 1");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("INSERT INTO site_settings (id, site_name) VALUES (1, 'My VTU')");
    }


    // Services table
    $sql = "CREATE TABLE IF NOT EXISTS `services` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `config` TEXT NOT NULL
    )";
    $pdo->exec($sql);

    // Bonus Withdrawals table
    $sql = "CREATE TABLE IF NOT EXISTS `bonus_withdrawals` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `amount` DECIMAL(10, 2) NOT NULL,
        `bank_details` TEXT NOT NULL,
        `status` VARCHAR(20) DEFAULT 'pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql);

    // Add admin user if not exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'admin@example.com'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $admin_pass = password_hash('password', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, phone, password, tier) VALUES ('Admin', 'admin@example.com', '00000000000', '$admin_pass', 99)");
    }


    echo "Database and tables created/updated successfully.";

} catch (PDOException $e) {
    die("ERROR: Could not set up the database. " . $e->getMessage());
}
?>
