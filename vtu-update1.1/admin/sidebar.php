<!-- Sidebar -->
<div class="w-64 bg-dark text-white p-4">
    <div class="flex justify-center mb-6">
        <?php if (!empty($settings['site_logo'])): ?>
            <img src="../<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo" class="h-16">
        <?php else: ?>
            <h2 class="text-2xl font-bold text-center"><?= htmlspecialchars($settings['site_name'] ?? 'VTU Platform') ?></h2>
        <?php endif; ?>
    </div>
    <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
    <nav>
        <a href="dashboard.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Dashboard</a>
        <a href="users.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Users</a>
        <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Transactions</a>
        <a href="payment_orders.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Payment Orders</a>
        <a href="bonus_withdrawals.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Bonus Withdrawals</a>
        <a href="fund_shares.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Fund Shares</a>
        <a href="notifications.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Notifications</a>
        <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Services</a>
        <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Bank Settings</a>
        <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Site Settings</a>
        <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-secondary">Logout</a>
    </nav>
</div>
