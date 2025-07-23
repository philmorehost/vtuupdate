<?php
require_once('../includes/session_config.php');
if (isset($_SESSION['admin_id'])) {
    // Restore admin session
    $_SESSION['admin'] = $_SESSION['admin_id'];
    unset($_SESSION['admin_id']);

    // Unset user session
    unset($_SESSION['user_id']);

    header('Location: users.php');
    exit();
} else {
    header('Location: ../login.php');
    exit();
}
?>
