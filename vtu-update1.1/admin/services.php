<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// In a real app, this would be more dynamic, probably from the services table
$services = [
    'data' => [
        ['id' => 1, 'network' => 'MTN', 'plan' => '1GB', 'price' => 300, 'duration' => '1 Day'],
        ['id' => 2, 'network' => 'Glo', 'plan' => '3GB', 'price' => 700, 'duration' => '14 Days'],
    ],
    'airtime' => [
        ['id' => 1, 'network' => 'All', 'discount' => '1%']
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - Admin</title>
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
                <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Users</a>
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Transactions</a>
                <a href="payment_orders.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Payment Orders</a>
                <a href="fund_shares.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Fund Shares</a>
                <a href="bonus_withdrawals.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bonus Withdrawals</a>
                <a href="notifications.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Notifications</a>
                <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 bg-gray-700">Services</a>
                <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bank Settings</a>
                <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Site Settings</a>
                <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-6">Manage Services</h1>

            <!-- Data Plans -->
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h2 class="text-2xl font-bold mb-4">Data Plans</h2>
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Network</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Plan</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Price</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($services['data'] as $plan): ?>
                            <tr>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($plan['network']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($plan['plan']) ?></td>
                                <td class="text-left py-3 px-4">₦<?= htmlspecialchars($plan['price']) ?></td>
                                <td class="text-left py-3 px-4">
                                    <a href="#" class="text-blue-500 hover:text-blue-700">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Airtime Discounts -->
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Airtime Discounts</h2>
                 <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Network</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Discount</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($services['airtime'] as $service): ?>
                            <tr>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($service['network']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($service['discount']) ?></td>
                                <td class="text-left py-3 px-4">
                                    <a href="#" class="text-blue-500 hover:text-blue-700">Edit</a>
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
