<?php
header('Content-Type: application/json');
require_once('../includes/session_config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phoneNumber = $_POST['phoneNumber'] ?? null;
    $amount = $_POST['amount'] ?? null;
    $source = $_POST['source'] ?? 'Website'; // Default to website
    $batchId = ($source === 'API') ? uniqid('batch_') : null;

    if ($phoneNumber && $amount && is_numeric($amount) && $amount > 0) {
        $amount = floatval($amount);
        $cost = $amount * 0.99; // Example: 1% discount

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $balance_before = $user['wallet_balance'];

            if ($balance_before < $cost) {
                echo json_encode(['success' => false, 'message' => 'Insufficient balance.']);
                $pdo->rollBack();
                exit();
            }

            $balance_after = $balance_before - $cost;
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
            $stmt->execute([$balance_after, $userId]);

            $description = "Airtime purchase of N{$amount} for {$phoneNumber}";
            $serviceDetails = json_encode(['phoneNumber' => $phoneNumber, 'amount' => $amount, 'cost' => $cost]);
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, description, amount, status, service_details, source, balance_before, balance_after, batch_id) VALUES (?, 'Airtime', ?, ?, 'Completed', ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $description, -$cost, $serviceDetails, $source, $balance_before, $balance_after, $batchId]);

            $pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => "Successfully sent N{$amount} airtime to {$phoneNumber}."
            ]);

        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request. Phone number and amount are required.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
