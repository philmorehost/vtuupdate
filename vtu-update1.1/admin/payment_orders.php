<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

$stmt = $pdo->query("SELECT p.*, u.name as user_name FROM payment_orders p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Orders - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content p-4">
            <h1 class="h3 mb-4">Payment Orders</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                                    <td>₦<?= htmlspecialchars(number_format($order['amount'], 2)) ?></td>
                                    <td><?= htmlspecialchars($order['status']) ?></td>
                                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                                    <td>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <a href="payment_order_actions.php?action=approve&id=<?= $order['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                                            <a href="payment_order_actions.php?action=decline&id=<?= $order['id'] ?>" class="btn btn-sm btn-danger">Decline</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
