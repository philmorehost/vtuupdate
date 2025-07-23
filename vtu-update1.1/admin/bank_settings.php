<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

$stmt = $pdo->query("SELECT * FROM bank_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Settings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content p-4">
            <h1 class="h3 mb-4">Bank Settings</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="bank_actions.php?action=update" method="POST">
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" value="<?= htmlspecialchars($settings['bank_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="account_name" class="form-label">Account Name</label>
                            <input type="text" name="account_name" id="account_name" class="form-control" value="<?= htmlspecialchars($settings['account_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number</label>
                            <input type="text" name="account_number" id="account_number" class="form-control" value="<?= htmlspecialchars($settings['account_number'] ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
