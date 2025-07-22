<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch pending payment orders
$stmt = $pdo->query("SELECT po.*, u.name as user_name FROM payment_orders po JOIN users u ON po.user_id = u.id WHERE po.status = 'pending' ORDER BY po.created_at DESC");
$pending_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($pending_orders as $key => $order) {
    if (isset($order['bank_id'])) {
        $stmt = $pdo->prepare("SELECT bank_name FROM bank_details WHERE id = ?");
        $stmt->execute([$order['bank_id']]);
        $bank = $stmt->fetch(PDO::FETCH_ASSOC);
        $pending_orders[$key]['bank_name'] = $bank ? $bank['bank_name'] : 'N/A';
    } else {
        $pending_orders[$key]['bank_name'] = 'N/A';
    }
}

// Fetch processed payment orders
$stmt = $pdo->query("SELECT po.*, u.name as user_name FROM payment_orders po JOIN users u ON po.user_id = u.id WHERE po.status != 'pending' ORDER BY po.updated_at DESC");
$processed_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Orders - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 text-white p-4">
            <div class="flex justify-center mb-6">
                <?php if (!empty($settings['site_logo'])): ?>
                    <img src="../<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo" class="h-16">
                <?php else: ?>
                    <h2 class="text-2xl font-bold text-center"><?= htmlspecialchars($settings['site_name'] ?? 'VTU Platform') ?></h2>
                <?php endif; ?>
            </div>
            <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
            <nav>
                <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Dashboard</a>
                <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Users</a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Transactions</a>
                <a href="payment_orders.php" class="block py-2.5 px-4 rounded transition duration-200 bg-gray-700">Payment Orders</a>
                <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Services</a>
                <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bank Settings</a>
                <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Site Settings</a>
                <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-6">Payment Orders</h1>

            <!-- Pending Requests -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl font-bold mb-4">Pending Requests</h2>
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">User</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Amount</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Bank</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Proof</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Date</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($pending_orders as $order): ?>
                            <tr>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($order['user_name']) ?></td>
                                <td class="text-left py-3 px-4">₦<?= htmlspecialchars(number_format($order['amount'], 2)) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($order['bank_name']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($order['payment_proof']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($order['created_at']) ?></td>
                                <td class="text-left py-3 px-4">
                                    <form action="payment_order_actions.php" method="POST" class="inline-block">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <?php if (isset($order['bank_id'])): ?>
                                            <input type="hidden" name="bank_id" value="<?= $order['bank_id'] ?>">
                                        <?php endif; ?>
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="text-green-500 hover:text-green-700">Approve</button>
                                    </form>
                                    <form action="payment_order_actions.php" method="POST" class="inline-block ml-4">
                                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="text-red-500 hover:text-red-700">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Processed History -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Processed History</h2>
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">User</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Amount</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Date Processed</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($processed_orders as $order): ?>
                            <tr>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($order['user_name']) ?></td>
                                <td class="text-left py-3 px-4">₦<?= htmlspecialchars(number_format($order['amount'], 2)) ?></td>
                                <td class="text-left py-3 px-4">
                                     <span class="px-2 py-1 font-semibold leading-tight rounded-full
                                        <?= $order['status'] === 'approved' ? 'text-green-700 bg-green-100' : '' ?>
                                        <?= $order['status'] === 'disapproved' ? 'text-red-700 bg-red-100' : '' ?>
                                        <?= $order['status'] === 'cancelled' ? 'text-gray-700 bg-gray-100' : '' ?>
                                    ">
                                        <?= htmlspecialchars($order['status']) ?>
                                    </span>
                                </td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($order['updated_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
