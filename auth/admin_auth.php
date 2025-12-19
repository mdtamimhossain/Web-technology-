<?php
require_once __DIR__ . '/../database/db.php';

function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function adminLogin($username, $password) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        initSession();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        return true;
    }
    return false;
}

function isAdminLoggedIn() {
    initSession();
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function adminLogout() {
    initSession();
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    session_destroy();
}

function getAdminName() {
    initSession();
    return $_SESSION['admin_name'] ?? 'Admin';
}
?>
