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
    $planId = $_POST['plan'] ?? null;
    $source = $_POST['source'] ?? 'Website'; // Default to website
    $batchId = ($source === 'API') ? uniqid('batch_') : null;

    if ($phoneNumber && $planId) {
        // In a real app, you would get this from the services table
        $dataPlans = [
            'mtn-1gb-300' => ['price' => 300, 'description' => 'MTN 1GB Data'],
            // Add other plans here
        ];
        $plan = $dataPlans[$planId] ?? null;

        if (!$plan) {
            echo json_encode(['success' => false, 'message' => 'Invalid data plan.']);
            exit();
        }

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ? FOR UPDATE");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            $balance_before = $user['wallet_balance'];

            if ($balance_before < $plan['price']) {
                echo json_encode(['success' => false, 'message' => 'Insufficient balance.']);
                $pdo->rollBack();
                exit();
            }

            $balance_after = $balance_before - $plan['price'];
            $stmt = $pdo->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
            $stmt->execute([$balance_after, $userId]);

            $description = "{$plan['description']} for {$phoneNumber}";
            $serviceDetails = json_encode(['phoneNumber' => $phoneNumber, 'plan' => $planId]);
            $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, description, amount, status, service_details, source, balance_before, balance_after, batch_id) VALUES (?, 'Data', ?, ?, 'Completed', ?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $description, -$plan['price'], $serviceDetails, $source, $balance_before, $balance_after, $batchId]);

            $pdo->commit();

            echo json_encode([
                'success' => true,
                'message' => "Successfully purchased {$plan['description']} for {$phoneNumber}."
            ]);

        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request. Phone number and plan are required.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
