<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch all users
$stmt = $pdo->query("SELECT u.id, u.name, u.email, u.phone, u.wallet_balance, u.status, u.last_login, r.name as referrer_name, COUNT(t.id) as total_transactions FROM users u LEFT JOIN users r ON u.referred_by = r.id LEFT JOIN transactions t ON u.id = t.user_id GROUP BY u.id ORDER BY u.created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen bg-gray-200">
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 bg-gray-800 text-white p-4 fixed md:relative h-full z-20 md:block hidden">
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
                <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 bg-gray-700">Users</a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Transactions</a>
                <a href="payment_orders.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Payment Orders</a>
                <a href="fund_shares.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Fund Shares</a>
                <a href="bonus_withdrawals.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bonus Withdrawals</a>
                <a href="notifications.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Notifications</a>
                <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Services</a>
                <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bank Settings</a>
                <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Site Settings</a>
                <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Manage Users</h1>
                <button id="sidebar-toggle" class="md:hidden p-2 rounded-md bg-gray-800 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
                <a href="user_create.php" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Create New User
                </a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Name</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email / Phone</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Balance</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Total Transactions</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Last Login</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Referred By</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($user['name']) ?></td>
                                <td class="text-left py-3 px-4">
                                    <div><?= htmlspecialchars($user['email']) ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($user['phone']) ?></div>
                                </td>
                                <td class="text-left py-3 px-4">₦<?= htmlspecialchars(number_format($user['wallet_balance'], 2)) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($user['total_transactions']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($user['last_login'] ?? 'N/A') ?></td>
                                <td class="text-left py-3 px-4">
                                    <span class="px-2 py-1 font-semibold leading-tight <?= $user['status'] === 'active' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' ?> rounded-full">
                                        <?= htmlspecialchars($user['status']) ?>
                                    </span>
                                </td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($user['referrer_name'] ?? 'N/A') ?></td>
                                <td class="text-left py-3 px-4">
                                    <a href="user_edit.php?id=<?= $user['id'] ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                                    <a href="user_login.php?id=<?= $user['id'] ?>" class="text-green-500 hover:text-green-700 ml-4">Login as User</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('hidden');
        });
    </script>
</body>
</html>
