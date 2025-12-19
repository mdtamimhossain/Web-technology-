<?php
/**
 * Admin Logout
 */
require_once __DIR__ . '/../auth/admin_auth.php';

logoutAdmin();
header('Location: login.php');
exit;
?>
