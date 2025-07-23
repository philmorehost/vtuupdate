<?php
session_start();
require_once('includes/db.php');

// Function to check email liveness (placeholder)
function check_email_liveness($email) {
    // In a real application, you would use a service like Kickbox or ZeroBounce
    // For this example, we'll just check the domain's MX records
    $domain = substr(strrchr($email, "@"), 1);
    return checkdnsrr($domain, 'MX');
}

$action = $_GET['action'] ?? 'login';

if ($action === 'register') {
    // Handle Registration
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $password = $_POST['password'] ?? '';
        $ref = $_POST['ref'] ?? '';

        // Sanitize and validate inputs
        $name = filter_var(trim($name), FILTER_SANITIZE_STRING);
        $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $phone = filter_var(trim($phone), FILTER_SANITIZE_STRING);

        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            die('Please fill all required fields.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die('Invalid email format.');
        }

        if (!check_email_liveness($email)) {
            die('Email address does not appear to be active.');
        }

        // Enforce strong password
        if (strlen($password) < 6 || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
            die('Password must be at least 6 characters long and include at least one number and one special character.');
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
            $stmt = $pdo->prepare("SELECT id, password, failed_login_attempts, suspended_until FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                if ($user['suspended_until'] && strtotime($user['suspended_until']) > time()) {
                    die('Your account is temporarily suspended. Please try again later.');
                }

                if (password_verify($password, $user['password'])) {
                    // Reset failed login attempts
                    $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = 0, suspended_until = NULL WHERE id = ?");
                    $stmt->execute([$user['id']]);

                    $_SESSION['user_id'] = $user['id'];

                    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);

                    header('Location: index.php');
                    exit();
                } else {
                    $attempts = $user['failed_login_attempts'] + 1;
                    $sql = "UPDATE users SET failed_login_attempts = ? WHERE id = ?";
                    if ($attempts >= 3) {
                        $sql .= ", suspended_until = DATE_ADD(NOW(), INTERVAL 2 HOUR)";
                    }
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$attempts, $user['id']]);

                    header('Location: login.php?error=invalid_credentials');
                    exit();
                }
            } else {
                // User does not exist, block IP (placeholder)
                error_log("Failed login attempt from IP: " . $_SERVER['REMOTE_ADDR']);
                header('Location: login.php?error=invalid_credentials');
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
