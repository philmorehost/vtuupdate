<?php
require_once('includes/session_config.php');
require_once('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's referrals
$stmt = $pdo->prepare("SELECT name, email, created_at FROM users WHERE referred_by = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Referral History</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Referral History</h1>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-bold mb-4">Users You've Referred</h2>
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">User</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                        <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Date Joined</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php foreach ($referrals as $referral): ?>
                        <tr>
                            <td class="text-left py-3 px-4"><?= htmlspecialchars($referral['name']) ?></td>
                            <td class="text-left py-3 px-4"><?= htmlspecialchars($referral['email']) ?></td>
                            <td class="text-left py-3 px-4"><?= htmlspecialchars($referral['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
