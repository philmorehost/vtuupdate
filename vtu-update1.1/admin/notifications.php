<?php
require_once('../includes/session_config.php');
require_once('auth_check.php');
require_once('../includes/db.php');

// Fetch all notifications
$stmt = $pdo->query("SELECT * FROM notifications ORDER BY created_at DESC");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Notifications - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/34.2.0/classic/ckeditor.js"></script>
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
                <a href="bonus_withdrawals.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bonus Withdrawals</a>
                <a href="notifications.php" class="block py-2.5 px-4 rounded transition duration-200 bg-gray-700">Notifications</a>
                <a href="services.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Services</a>
                <a href="bank_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Bank Settings</a>
                <a href="site_settings.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Site Settings</a>
                <a href="logout.php" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700">Logout</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-10 overflow-y-auto">
            <h1 class="text-3xl font-bold mb-6">Manage Notifications</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-4">Post New Notification</h2>
                    <form action="notification_actions.php?action=post" method="POST">
                        <?php require_once('csrf_token.php'); ?>
                        <div class="mb-4">
                            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
                            <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message</label>
                            <textarea name="message" id="message" rows="5" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">User ID (Optional)</label>
                            <input type="text" name="user_id" id="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" placeholder="Leave blank to send to all users">
                        </div>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Post Notification</button>
                    </form>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-4">Posted Notifications</h2>
                    <form action="notification_actions.php?action=delete_multiple" method="POST" id="delete-notifications-form">
                        <?php require_once('csrf_token.php'); ?>
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm"><input type="checkbox" id="select-all-notifications"></th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Title</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Date</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                <?php foreach ($notifications as $notification): ?>
                                    <tr>
                                        <td class="text-left py-3 px-4"><input type="checkbox" name="notification_ids[]" value="<?= $notification['id'] ?>" class="notification-checkbox"></td>
                                        <td class="text-left py-3 px-4"><?= htmlspecialchars($notification['title']) ?></td>
                                        <td class="text-left py-3 px-4"><?= htmlspecialchars($notification['created_at']) ?></td>
                                        <td class="text-left py-3 px-4">
                                            <a href="notification_actions.php?action=delete&id=<?= $notification['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to delete this notification?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4">Delete Selected</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        let editor;
        ClassicEditor
            .create( document.querySelector( '#message' ) )
            .then( newEditor => {
                editor = newEditor;
            } )
            .catch( error => {
                console.error( error );
            } );

        document.querySelector( 'form[action="notification_actions.php?action=post"]' ).addEventListener( 'submit', event => {
            if ( editor ) {
                const messageTextarea = document.querySelector( '#message' );
                messageTextarea.value = editor.getData();
                if ( !messageTextarea.value ) {
                    alert( 'Please enter a message.' );
                    event.preventDefault();
                }
            }
        } );

        document.getElementById('select-all-notifications').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.notification-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        document.getElementById('delete-notifications-form').addEventListener('submit', function(event) {
            // No longer need to prevent default and show alert
        });
    </script>
</body>
</html>
