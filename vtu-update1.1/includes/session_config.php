<?php
require_once('db.php');

$stmt = $pdo->query("SELECT session_timeout, cache_control FROM site_settings WHERE id = 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

if ($settings) {
    // Set session timeout
    ini_set('session.gc_maxlifetime', $settings['session_timeout'] * 60);
    session_set_cookie_params($settings['session_timeout'] * 60);

    // Set cache control header
    header("Cache-Control: " . $settings['cache_control']);
}

session_start();
?>
