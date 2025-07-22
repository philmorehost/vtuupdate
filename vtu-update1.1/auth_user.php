<?php
session_start();
require_once('includes/db.php');

$action = $_GET['action'] ?? 'login';

if ($action === 'register') {
    // Handle Registration
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $ref = $_POST['ref'] ?? '';

        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            die('Please fill all required fields.');
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $referral_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/register.php?ref=" . uniqid();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, wallet_balance, bonus_balance, referral_link) VALUES (?, ?, ?, ?, 0.00, 0.00, ?)");
            $stmt->execute([$name, $email, $phone, $password_hash, $referral_link]);
            $new_user_id = $pdo->lastInsertId();

            if (!empty($ref)) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_link = ?");
                $stmt->execute([$ref]);
                $referrer = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($referrer) {
                    $stmt = $pdo->prepare("UPDATE users SET referred_by = ? WHERE id = ?");
                    $stmt->execute([$referrer['id'], $new_user_id]);
                }
            }

            $pdo->commit();

            $_SESSION['user_id'] = $new_user_id;
            header('Location: index.php');
            exit();

        } catch (PDOException $e) {
            $pdo->rollBack();
            die('Registration failed: ' . $e->getMessage());
        }
    }
} else {
    // Handle Login
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            die('Please fill all required fields.');
        }

        try {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];

                $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);

                header('Location: index.php');
                exit();
            } else {
                header('Location: login.php?error=1');
                exit();
            }
        } catch (PDOException $e) {
            die('Login failed: ' . $e->getMessage());
        }
    }
}

header('Location: login.php');
exit();
?>