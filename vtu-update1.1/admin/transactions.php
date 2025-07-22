<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Fetch current settings
$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

// Base query
$sql = "SELECT t.*, u.name AS user_name FROM transactions t JOIN users u ON t.user_id = u.id";
$where = [];
$params = [];

// Filtering logic will go here

$sql .= " ORDER BY t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch batch transactions
$batchSql = "SELECT batch_id, user_id, type,
                    COUNT(CASE WHEN status = 'Completed' THEN 1 END) as success_count,
                    COUNT(CASE WHEN status = 'Failed' THEN 1 END) as failed_count,
                    COUNT(CASE WHEN status = 'Processing' THEN 1 END) as processing_count,
                    MAX(created_at) as date
             FROM transactions
             WHERE batch_id IS NOT NULL
             GROUP BY batch_id, user_id, type
             ORDER BY date DESC";
$batchStmt = $pdo->query($batchSql);
$batches = $batchStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Transactions - Admin</title>
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
                <a href="transactions.php" class="block py-2.5 px-4 rounded transition duration-200 bg-gray-700">Transactions</a>
                <a href="payment_orders.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Payment Orders</a>
                <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Services</a>
                <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bank Settings</a>
                <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Site Settings</a>
                <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-6">Manage Transactions</h1>

            <!-- Filter and Export Controls -->
            <div class="bg-white p-4 rounded-lg shadow-md mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <!-- Search Box -->
                        <input type="text" id="searchInput" placeholder="Search transactions..." class="border rounded py-2 px-4">
                    </div>
                    <div>
                        <!-- Date Filters -->
                        <button class="bg-blue-500 text-white py-2 px-4 rounded">Today</button>
                        <button class="bg-blue-500 text-white py-2 px-4 rounded">This Week</button>
                        <button class="bg-blue-500 text-white py-2 px-4 rounded">This Month</button>
                        <!-- Custom Date Range would go here -->
                    </div>
                    <div>
                        <!-- Export/Import -->
                        <button class="bg-green-500 text-white py-2 px-4 rounded">Export CSV</button>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mb-4">
                <span class="text-sm font-medium text-gray-900 mr-3">Batch View</span>
                <label for="toggle-batch" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" value="" id="toggle-batch" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div id="individual-view" class="bg-white p-6 rounded-lg shadow-md">
                <table class="min-w-full bg-white" id="transactionsTable">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">User</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Type</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Amount</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Balances (Before/After)</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Source</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Date</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Status</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($transaction['user_name']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($transaction['type']) ?></td>
                                <td class="text-left py-3 px-4">
                                    <span class="<?= $transaction['amount'] < 0 ? 'text-red-500' : 'text-green-500' ?>">
                                        ₦<?= htmlspecialchars(number_format(abs($transaction['amount']), 2)) ?>
                                    </span>
                                </td>
                                <td class="text-left py-3 px-4">
                                    ₦<?= htmlspecialchars(number_format($transaction['balance_before'], 2)) ?> /
                                    ₦<?= htmlspecialchars(number_format($transaction['balance_after'], 2)) ?>
                                </td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($transaction['source']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($transaction['created_at']) ?></td>
                                <td class="text-left py-3 px-4">
                                    <span class="px-2 py-1 font-semibold leading-tight rounded-full
                                        <?= $transaction['status'] === 'Completed' ? 'text-green-700 bg-green-100' : '' ?>
                                        <?= $transaction['status'] === 'Processing' ? 'text-yellow-700 bg-yellow-100' : '' ?>
                                        <?= $transaction['status'] === 'Failed' ? 'text-red-700 bg-red-100' : '' ?>
                                        <?= $transaction['status'] === 'Cancelled' ? 'text-gray-700 bg-gray-100' : '' ?>
                                    ">
                                        <?= htmlspecialchars($transaction['status']) ?>
                                    </span>
                                </td>
                                <td class="text-left py-3 px-4">
                                    <?php if ($transaction['status'] === 'Processing'): ?>
                                        <a href="transaction_actions.php?action=cancel&id=<?= $transaction['id'] ?>" class="text-red-500 hover:text-red-700">Cancel</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        // Live search functionality
        const searchInput = document.getElementById('searchInput');
        const table = document.getElementById('transactionsTable');
        const rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            const filter = searchInput.value.toLowerCase();
            for (let i = 1; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j].innerText.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                if (found) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });

        // Toggle between individual and batch view
        const toggleBatch = document.getElementById('toggle-batch');
        const individualView = document.getElementById('individual-view');
        const batchView = document.getElementById('batch-view');

        toggleBatch.addEventListener('change', function() {
            if (this.checked) {
                individualView.style.display = 'none';
                batchView.style.display = 'block';
            } else {
                individualView.style.display = 'block';
                batchView.style.display = 'none';
            }
        });
    </script>
</body>
</html>
            <div id="batch-view" class="hidden bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-bold mb-4">Batch Transactions</h2>
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Batch ID</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">User</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Type</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Counts (Success/Failed/Processing)</th>
                            <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($batches as $batch): ?>
                            <tr>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($batch['batch_id']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($batch['user_id']) ?></td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($batch['type']) ?></td>
                                <td class="text-left py-3 px-4">
                                    <span class="text-green-500"><?= $batch['success_count'] ?></span> /
                                    <span class="text-red-500"><?= $batch['failed_count'] ?></span> /
                                    <span class="text-yellow-500"><?= $batch['processing_count'] ?></span>
                                </td>
                                <td class="text-left py-3 px-4"><?= htmlspecialchars($batch['date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
