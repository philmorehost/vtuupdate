<?php
header('Content-Type: application/json');
require_once('../includes/session_config.php');
require_once('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'fetch';

if ($action === 'fetch') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? OR user_id IS NULL ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $notifications]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} elseif ($action === 'mark_read' && isset($_POST['id'])) {
    $notification_id = $_POST['id'];
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE id = ? AND (user_id = ? OR user_id IS NULL)");
        $stmt->execute([$notification_id, $user_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} elseif ($action === 'mark_all_read') {
    try {
        $stmt = $pdo->prepare("UPDATE notifications SET is_read = TRUE WHERE user_id = ? OR user_id IS NULL");
        $stmt->execute([$user_id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
?>
