<?php
require_once('includes/session_config.php');
session_destroy();
header('Location: login.php');
exit();
?>
