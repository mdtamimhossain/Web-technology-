<?php
require_once __DIR__ . '/../auth/admin_auth.php';
adminLogout();
header('Location: login.php');
exit;
