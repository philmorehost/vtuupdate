<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Fetch all bonus withdrawal requests
$stmt = $pdo->query("SELECT bw.*, u.name as user_name FROM bonus_withdrawals bw JOIN users u ON bw.user_id = u.id ORDER BY bw.created_at DESC");
$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bonus Withdrawals - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white p-4">
            <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
            <nav>
                <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Dashboard</a>
                <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Users</a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Transactions</a>
                <a href="payment_orders.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Payment Orders</a>
                <a href="bonus_withdrawals.php" class="block py-2.5 px-4 rounded transition duration-200 bg-gray-700">Bonus Withdrawals</a>
                <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Services</a>
                <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bank Settings</a>
                <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Site Settings</a>
                <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-6">Bonus Withdrawals</h1>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">User</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Amount</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Bank Details</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Date</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($withdrawals as $withdrawal): ?>
                            <tr>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($withdrawal['user_name']) ?></td>
                                <td class="text-left py-3 px-4">₦<?= htmlspecialchars(number_format($withdrawal['amount'], 2)) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($withdrawal['bank_details']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($withdrawal['created_at']) ?></td>
                                <td class="text-left py-3 px-4">
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full
                                        <?= $withdrawal['status'] === 'approved' ? 'text-green-700 bg-green-100' : '' ?>
                                        <?= $withdrawal['status'] === 'pending' ? 'text-yellow-700 bg-yellow-100' : '' ?>
                                        <?= $withdrawal['status'] === 'rejected' ? 'text-red-700 bg-red-100' : '' ?>
                                    ">
                                        <?= htmlspecialchars($withdrawal['status']) ?>
                                    </span>
                                </td>
                                <td class="text-left py-3 px-4">
                                    <?php if ($withdrawal['status'] === 'pending'): ?>
                                        <form action="bonus_withdrawal_actions.php" method="POST" class="inline-block">
                                            <input type="hidden" name="withdrawal_id" value="<?= $withdrawal['id'] ?>">
                                            <button type="submit" name="action" value="approve" class="text-green-500 hover:text-green-700">Approve</button>
                                        </form>
                                        <form action="bonus_withdrawal_actions.php" method="POST" class="inline-block">
                                            <input type="hidden" name="withdrawal_id" value="<?= $withdrawal['id'] ?>">
                                            <button type="submit" name="action" value="reject" class="text-red-500 hover:text-red-700">Reject</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
