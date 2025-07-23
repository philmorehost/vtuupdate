<?php
header('Content-Type: application/json');
require_once('../includes/session_config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}
$userId = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT id, type, description, amount, status, service_details, created_at AS date FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // The service_details are stored as JSON, so we need to decode them
    foreach ($transactions as &$transaction) {
        $transaction['serviceDetails'] = json_decode($transaction['service_details'], true);
        unset($transaction['service_details']); // remove the original json string
    }

    echo json_encode($transactions);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
