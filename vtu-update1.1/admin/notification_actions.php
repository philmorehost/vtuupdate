<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Log all POST data
error_log(print_r($_POST, true));

$action = $_GET['action'] ?? '';

if ($action === 'post' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $message = $_POST['message'] ?? '';
    $user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : null;

    if ($title && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $title, $message]);
            header('Location: notifications.php?success=posted');
            exit();
        } catch (PDOException $e) {
            header('Location: notifications.php?error=db_error');
            exit();
        }
    } else {
        header('Location: notifications.php?error=missing_fields');
        exit();
    }
} elseif ($action === 'delete' && isset($_GET['id'])) {
    $notification_id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->execute([$notification_id]);
        header('Location: notifications.php?success=deleted');
        exit();
    } catch (PDOException $e) {
        header('Location: notifications.php?error=db_error');
        exit();
    }
} else {
    header('Location: notifications.php');
    exit();
}
?>
