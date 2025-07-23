<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

$stmt = $pdo->query("SELECT f.*, u.name as sender_name, r.name as receiver_name FROM fund_shares f JOIN users u ON f.sender_id = u.id JOIN users r ON f.recipient_id = r.id ORDER BY f.created_at DESC");
$shares = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fund Shares - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content p-4">
            <h1 class="h3 mb-4">Fund Shares</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sender</th>
                                <th>Receiver</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($shares as $share): ?>
                                <tr>
                                    <td><?= htmlspecialchars($share['sender_name']) ?></td>
                                    <td><?= htmlspecialchars($share['receiver_name']) ?></td>
                                    <td>₦<?= htmlspecialchars(number_format($share['amount'], 2)) ?></td>
                                    <td><?= htmlspecialchars($share['created_at']) ?></td>
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
