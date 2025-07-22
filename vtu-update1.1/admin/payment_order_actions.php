<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: payment_orders.php');
    exit();
}

$action = $_POST['action'] ?? '';
$order_id = $_POST['order_id'] ?? null;

if (!$order_id || !in_array($action, ['approve', 'reject'])) {
    header('Location: payment_orders.php?error=invalid_action');
    exit();
}

$new_status = $action === 'approve' ? 'approved' : 'rejected';

try {
    $pdo->beginTransaction();

    // Fetch order details
    $stmt = $pdo->prepare("SELECT * FROM payment_orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Order not found.');
    }

    if ($order['status'] !== 'pending') {
        throw new Exception('Order has already been processed.');
    }

    // Update order status
    $stmt = $pdo->prepare("UPDATE payment_orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status, $order_id]);

    // If approved, credit user's wallet and create a transaction record
    if ($new_status === 'approved') {
        $charge = 0;
        if (isset($order['bank_id'])) {
            // Fetch bank charge
            $stmt = $pdo->prepare("SELECT charge FROM bank_details WHERE id = ?");
            $stmt->execute([$order['bank_id']]);
            $bank = $stmt->fetch(PDO::FETCH_ASSOC);
            $charge = $bank ? (float)$bank['charge'] : 0;
        }

        $amount_to_credit = $order['amount'] - $charge;

        $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
        $stmt->execute([$amount_to_credit, $order['user_id']]);

        $description = "Wallet funding of ₦" . number_format($order['amount'], 2) . " (Charge: ₦" . number_format($charge, 2) . ")";
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, description, status) VALUES (?, 'credit', ?, ?, 'completed')");
        $stmt->execute([$order['user_id'], $amount_to_credit, $description]);
    }

    $pdo->commit();
    header('Location: payment_orders.php?success=action_completed');
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Order action failed: " . $e->getMessage());
    header('Location: payment_orders.php?error=db_error');
    exit();
}
