<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

$stmt = $pdo->query("SELECT b.*, u.name as user_name FROM bonus_withdrawals b JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC");
$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonus Withdrawals - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4 overflow-auto">
            <h1 class="h3 mb-4">Bonus Withdrawals</h1>
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
                            <?php foreach ($withdrawals as $withdrawal): ?>
                                <tr>
                                    <td><?= htmlspecialchars($withdrawal['user_name']) ?></td>
                                    <td>₦<?= htmlspecialchars(number_format($withdrawal['amount'], 2)) ?></td>
                                    <td><?= htmlspecialchars($withdrawal['status']) ?></td>
                                    <td><?= htmlspecialchars($withdrawal['created_at']) ?></td>
                                    <td>
                                        <?php if ($withdrawal['status'] === 'pending'): ?>
                                            <a href="bonus_withdrawal_actions.php?action=approve&id=<?= $withdrawal['id'] ?>" class="btn btn-sm btn-success">Approve</a>
                                            <a href="bonus_withdrawal_actions.php?action=decline&id=<?= $withdrawal['id'] ?>" class="btn btn-sm btn-danger">Decline</a>
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
