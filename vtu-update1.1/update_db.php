<?php
require_once('includes/db.php');

try {
    $pdo->beginTransaction();

    // Create admins table
    $sql = "CREATE TABLE IF NOT EXISTS `admins` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `email` VARCHAR(255) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);

    // Find and move admin user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = 'admin@example.com'");
    $stmt->execute();
    $admin_user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin_user) {
        $stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$admin_user['name'], $admin_user['email'], $admin_user['password']]);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$admin_user['id']]);
    }

    $pdo->commit();
    echo "Database updated successfully.";

} catch (PDOException $e) {
    $pdo->rollBack();
    die("ERROR: Could not update the database. " . $e->getMessage());
}
?>
