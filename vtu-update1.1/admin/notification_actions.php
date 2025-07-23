<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

$action = $_GET['action'] ?? '';

if ($action === 'post' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: notifications.php?error=csrf');
        exit();
    }

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
} elseif ($action === 'delete_multiple' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('Location: notifications.php?error=csrf');
        exit();
    }

    $notification_ids = $_POST['notification_ids'] ?? [];
    if (!empty($notification_ids)) {
        try {
            $placeholders = implode(',', array_fill(0, count($notification_ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM notifications WHERE id IN ($placeholders)");
            $stmt->execute($notification_ids);
            header('Location: notifications.php?success=deleted_multiple');
            exit();
        } catch (PDOException $e) {
            header('Location: notifications.php?error=db_error');
            exit();
        }
    } else {
        header('Location: notifications.php?error=no_selection');
        exit();
    }
} else {
    header('Location: notifications.php');
    exit();
}
?>
