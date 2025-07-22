<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(amount) FROM transactions WHERE amount < 0")->fetchColumn();

// Fetch recent transactions
$stmt = $pdo->query("SELECT t.*, u.name as user_name FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 5");
$recent_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
                <a href="payment_orders.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Payment Orders</a>
                <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Services</a>
                <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bank Settings</a>
                <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Site Settings</a>
                <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold">Total Users</h3>
                    <p class="text-3xl mt-2"><?= htmlspecialchars($total_users) ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold">Total Transactions</h3>
                    <p class="text-3xl mt-2"><?= htmlspecialchars($total_transactions) ?></p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold">Total Revenue</h3>
                    <p class="text-3xl mt-2">₦<?= htmlspecialchars(number_format(abs($total_revenue), 2)) ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Quick Links -->
                <div class="lg:col-span-1 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-4">Quick Links</h2>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="users.php" class="bg-blue-500 text-white text-center py-4 rounded-lg hover:bg-blue-700">Manage Users</a>
                        <a href="transactions.php" class="bg-blue-500 text-white text-center py-4 rounded-lg hover:bg-blue-700">Transactions</a>
                        <a href="payment_orders.php" class="bg-blue-500 text-white text-center py-4 rounded-lg hover:bg-blue-700">Payment Orders</a>
                        <a href="bank_settings.php" class="bg-blue-500 text-white text-center py-4 rounded-lg hover:bg-blue-700">Bank Settings</a>
                        <a href="services.php" class="bg-blue-500 text-white text-center py-4 rounded-lg hover:bg-blue-700">Services</a>
                        <a href="site_settings.php" class="bg-blue-500 text-white text-center py-4 rounded-lg hover:bg-blue-700">Site Settings</a>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-2xl font-bold mb-4">Recent Transactions</h2>
                    <div>
                        <table class="min-w-full bg-white">
                            <tbody class="text-gray-700">
                                <?php foreach ($recent_transactions as $transaction): ?>
                                    <tr class="border-b">
                                        <td class="py-2 px-4"><?= htmlspecialchars($transaction['user_name']) ?></td>
                                        <td class="py-2 px-4"><?= htmlspecialchars($transaction['description']) ?></td>
                                        <td class="py-2 px-4">
                                            <span class="<?= $transaction['amount'] < 0 ? 'text-red-500' : 'text-green-500' ?>">
                                                ₦<?= htmlspecialchars(number_format(abs($transaction['amount']), 2)) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
