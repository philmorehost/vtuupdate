<!-- Sidebar -->
<div class="sidebar bg-dark text-white p-4">
    <div class="text-center mb-4">
        <?php if (!empty($settings['site_logo'])): ?>
            <img src="../<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo" class="img-fluid">
        <?php else: ?>
            <h2 class="h4"><?= htmlspecialchars($settings['site_name'] ?? 'VTU Platform') ?></h2>
        <?php endif; ?>
    </div>
    <h3 class="h5 mb-4">Admin Panel</h3>
    <nav class="nav flex-column">
        <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
        <a href="users.php" class="nav-link text-white">Users</a>
        <a href="transactions.php" class="nav-link text-white">Transactions</a>
        <a href="payment_orders.php" class="nav-link text-white">Payment Orders</a>
        <a href="bonus_withdrawals.php" class="nav-link text-white">Bonus Withdrawals</a>
        <a href="fund_shares.php" class="nav-link text-white">Fund Shares</a>
        <a href="notifications.php" class="nav-link text-white">Notifications</a>
        <a href="services.php" class="nav-link text-white">Services</a>
        <a href="bank_settings.php" class="nav-link text-white">Bank Settings</a>
        <a href="site_settings.php" class="nav-link text-white">Site Settings</a>
        <a href="logout.php" class="nav-link text-white">Logout</a>
    </nav>
</div>
