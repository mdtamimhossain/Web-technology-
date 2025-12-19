<?php
require_once __DIR__ . '/../database/db.php';

function adminInitSession() {
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
        adminInitSession();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        return true;
    }
    return false;
}

function isAdminLoggedIn() {
    adminInitSession();
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function adminLogout() {
    adminInitSession();
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_name']);
    session_destroy();
}

function getAdminName() {
    adminInitSession();
    return $_SESSION['admin_name'] ?? 'Admin';
}
?>
