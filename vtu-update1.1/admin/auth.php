<?php
session_start();
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header('Location: index.php?error=1');
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id, password, user_level FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['user_level'] == 1 && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin'] = $email;
            header('Location: dashboard.php');
            exit();
        } else {
            header('Location: index.php?error=1');
            exit();
        }
    } catch (PDOException $e) {
        die('Login failed: ' . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit();
}
?>
