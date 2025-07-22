<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Admin</title>
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
                <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Services</a>
                <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bank Settings</a>
                <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 bg-gray-700">Site Settings</a>
                <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10">
            <h1 class="text-3xl font-bold mb-6">Site Settings</h1>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <form action="site_settings_actions.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="site_name">Site Name</label>
                        <input type="text" name="site_name" id="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? 'My VTU') ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="site_logo">Site Logo</label>
                        <input type="file" name="site_logo" id="site_logo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <?php if (!empty($settings['site_logo'])): ?>
                            <img src="../<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo" class="mt-2 h-16">
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="auth_image">Auth Image</label>
                        <input type="file" name="auth_image" id="auth_image" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                        <p class="text-xs text-gray-500 mt-1">Recommended size: 400x400 pixels</p>
                        <?php if (!empty($settings['auth_image'])): ?>
                            <img src="../<?= htmlspecialchars($settings['auth_image']) ?>" alt="Auth Image" class="mt-2 h-16 w-auto" style="max-height: 64px;">
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="session_timeout">Session Timeout (minutes)</label>
                        <input type="number" name="session_timeout" id="session_timeout" value="<?= htmlspecialchars($settings['session_timeout'] ?? 30) ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="cache_control">Cache Control</label>
                        <select name="cache_control" id="cache_control" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            <option value="no-cache" <?= ($settings['cache_control'] ?? '') === 'no-cache' ? 'selected' : '' ?>>No Cache</option>
                            <option value="public, max-age=3600" <?= ($settings['cache_control'] ?? '') === 'public, max-age=3600' ? 'selected' : '' ?>>Cache for 1 hour</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 mt-6">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
