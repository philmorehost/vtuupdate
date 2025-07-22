<?php
header('Content-Type: application/json');
require_once('../includes/session_config.php');
require_once('../includes/db.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$transactionId = $_GET['id'] ?? null;

if (!$transactionId) {
    echo json_encode(['success' => false, 'message' => 'Transaction ID is missing.']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, type, description, amount, status, service_details, created_at AS date, balance_before, balance_after FROM transactions WHERE id = ? AND user_id = ?");
    $stmt->execute([$transactionId, $_SESSION['user_id']]);
    $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($transaction) {
        $transaction['serviceDetails'] = json_decode($transaction['service_details'], true);
        unset($transaction['service_details']);
        echo json_encode(['success' => true, 'data' => $transaction]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Transaction not found.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
