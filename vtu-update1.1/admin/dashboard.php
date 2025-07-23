<?php
$title = 'Dashboard';
require_once('includes/header.php');

// Fetch stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(amount) FROM transactions WHERE amount < 0")->fetchColumn();

// Fetch recent transactions
$stmt = $pdo->query("SELECT t.*, u.name as user_name FROM transactions t JOIN users u ON t.user_id = u.id ORDER BY t.created_at DESC LIMIT 5");
$recent_transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
    <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md table-container">
        <h2 class="text-2xl font-bold mb-4">Recent Transactions</h2>
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

<?php require_once('includes/footer.php'); ?>
