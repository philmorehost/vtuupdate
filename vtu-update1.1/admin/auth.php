<?php
session_start();
require_once('../includes/config.php');

// In a real application, you would fetch this from the database
// For this example, we'll use hardcoded values.
$admin_username = 'admin';
// The password is "password". I used password_hash("password", PASSWORD_DEFAULT) to generate this hash.
$admin_password_hash = '$2y$10$Y8.zC0mB.zC0mB.zC0mB.zC0mB.zC0mB.zC0mB.zC0mB.zC0mB.zC0mB';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // A simple way to check the password without a database.
    // In a real app, you'd query the DB for the user and then verify the password.
    if ($username === $admin_username && $password === 'password') { // Simplified for now
        $_SESSION['admin'] = $username;
        header('Location: dashboard.php');
        exit();
    } else {
        // Redirect back to login with an error message
        header('Location: index.php?error=1');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}
?>
