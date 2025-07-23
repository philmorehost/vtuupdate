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

// Fetch data for charts
$payment_data = $pdo->query("SELECT DATE(created_at) as date, SUM(amount) as total FROM payment_orders WHERE status = 'approved' GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 7")->fetchAll(PDO::FETCH_ASSOC);
$wallet_data = $pdo->query("SELECT name, wallet_balance FROM users ORDER BY wallet_balance DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
$browser_data = $pdo->query("SELECT browser, COUNT(*) as count FROM visitors GROUP BY browser")->fetchAll(PDO::FETCH_ASSOC);
$service_earnings = $pdo->query("SELECT s.name, COUNT(t.id) as total_orders, SUM(t.amount) as admin_earnings FROM services s LEFT JOIN transactions t ON s.id = t.service_id WHERE t.amount < 0 GROUP BY s.id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4 overflow-auto">
            <h1 class="h3 mb-4">Dashboard</h1>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text fs-4"><?= htmlspecialchars($total_users) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total Transactions</h5>
                            <p class="card-text fs-4"><?= htmlspecialchars($total_transactions) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total Revenue</h5>
                            <p class="card-text fs-4">₦<?= htmlspecialchars(number_format(abs($total_revenue), 2)) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total User Submit Payment</h5>
                            <canvas id="paymentChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Total User Wallet Balance</h5>
                            <canvas id="walletChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Visitors by Browser</h5>
                            <canvas id="browserChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Earnings by Service</h5>
                            <div id="serviceEarnings"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Quick Links -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Quick Links</h5>
                            <div class="d-grid gap-2">
                                <a href="users.php" class="btn btn-primary">Manage Users</a>
                                <a href="transactions.php" class="btn btn-primary">Transactions</a>
                                <a href="payment_orders.php" class="btn btn-primary">Payment Orders</a>
                                <a href="bank_settings.php" class="btn btn-primary">Bank Settings</a>
                                <a href="services.php" class="btn btn-primary">Services</a>
                                <a href="site_settings.php" class="btn btn-primary">Site Settings</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Recent Transactions</h5>
                            <table class="table table-striped">
                                <tbody>
                                    <?php foreach ($recent_transactions as $transaction): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($transaction['user_name']) ?></td>
                                            <td><?= htmlspecialchars($transaction['description']) ?></td>
                                            <td>
                                                <span class="<?= $transaction['amount'] < 0 ? 'text-danger' : 'text-success' ?>">
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Charts
        const paymentChartCtx = document.getElementById('paymentChart').getContext('2d');
        const paymentChart = new Chart(paymentChartCtx, {
            type: 'bar',
            data: {
                labels: [], // Dates
                datasets: [{
                    label: 'Total Amount Charged',
                    data: [], // Amounts
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const walletChartCtx = document.getElementById('walletChart').getContext('2d');
        const walletChart = new Chart(walletChartCtx, {
            type: 'bar',
            data: {
                labels: [], // Usernames
                datasets: [{
                    label: 'Wallet Balance',
                    data: [], // Balances
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        const browserChartCtx = document.getElementById('browserChart').getContext('2d');
        const browserChart = new Chart(browserChartCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($browser_data, 'browser')); ?>,
                datasets: [{
                    label: 'Visitors by Browser',
                    data: <?php echo json_encode(array_column($browser_data, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)',
                        'rgba(153, 102, 255, 0.5)',
                        'rgba(255, 159, 64, 0.5)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });

        // Populate service earnings
        const serviceEarningsContainer = document.getElementById('serviceEarnings');
        let serviceEarningsHtml = '<ul class="list-group">';
        <?php foreach ($service_earnings as $service): ?>
            serviceEarningsHtml += '<li class="list-group-item d-flex justify-content-between align-items-center">';
            serviceEarningsHtml += '<?= htmlspecialchars($service['name']) ?>';
            serviceEarningsHtml += '<span class="badge bg-primary rounded-pill"><?= htmlspecialchars($service['total_orders']) ?> orders</span>';
            serviceEarningsHtml += '<span class="badge bg-success rounded-pill">₦<?= htmlspecialchars(number_format(abs($service['admin_earnings']), 2)) ?></span>';
            serviceEarningsHtml += '</li>';
        <?php endforeach; ?>
        serviceEarningsHtml += '</ul>';
        serviceEarningsContainer.innerHTML = serviceEarningsHtml;

        // Update payment chart
        paymentChart.data.labels = <?php echo json_encode(array_column($payment_data, 'date')); ?>;
        paymentChart.data.datasets[0].data = <?php echo json_encode(array_column($payment_data, 'total')); ?>;
        paymentChart.update();

        // Update wallet chart
        walletChart.data.labels = <?php echo json_encode(array_column($wallet_data, 'name')); ?>;
        walletChart.data.datasets[0].data = <?php echo json_encode(array_column($wallet_data, 'wallet_balance')); ?>;
        walletChart.update();
    </script>
</body>
</html>
