<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

$stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4 overflow-auto">
            <h1 class="h3 mb-4">Site Settings</h1>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="site_settings_actions.php?action=update" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" name="site_name" id="site_name" class="form-control" value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="site_logo" class="form-label">Site Logo</label>
                            <input type="file" name="site_logo" id="site_logo" class="form-control">
                            <?php if (!empty($settings['site_logo'])): ?>
                                <img src="../<?= htmlspecialchars($settings['site_logo']) ?>" alt="Site Logo" class="mt-2" style="max-height: 50px;">
                            <?php endif; ?>
                        </div>
                        <div class="mb-3">
                            <label for="auth_image" class="form-label">Auth Image</label>
                            <input type="file" name="auth_image" id="auth_image" class="form-control">
                            <?php if (!empty($settings['auth_image'])): ?>
                                <img src="../<?= htmlspecialchars($settings['auth_image']) ?>" alt="Auth Image" class="mt-2" style="max-height: 100px;">
                            <?php endif; ?>
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
