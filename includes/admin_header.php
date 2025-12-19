<?php
/**
 * Admin Header Template
 */
require_once __DIR__ . '/../auth/admin_auth.php';
requireAdminLogin();

$admin = getCurrentAdmin();
$adminName = $admin['name'] ?? 'Admin';

// Get current page for active menu highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel'; ?> - Krist Admin</title>
    <link rel="stylesheet" href="./../CSS/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><i class="fa-solid fa-gem"></i> Krist Admin</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="index.php" class="<?php echo $currentPage === 'index' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-gauge-high"></i> Dashboard
                </a>
                <a href="orders.php" class="<?php echo $currentPage === 'orders' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-box"></i> Orders
                </a>
                <a href="orders.php?status=pending" class="sub-link <?php echo ($currentPage === 'orders' && ($_GET['status'] ?? '') === 'pending') ? 'active' : ''; ?>">
                    <i class="fa-regular fa-clock"></i> New Orders
                </a>
                <a href="orders.php?status=processing" class="sub-link <?php echo ($currentPage === 'orders' && ($_GET['status'] ?? '') === 'processing') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-gears"></i> Processing
                </a>
                <a href="orders.php?status=shipped" class="sub-link <?php echo ($currentPage === 'orders' && ($_GET['status'] ?? '') === 'shipped') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-truck"></i> Shipped
                </a>
                <a href="orders.php?status=delivered" class="sub-link <?php echo ($currentPage === 'orders' && ($_GET['status'] ?? '') === 'delivered') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-circle-check"></i> Delivered
                </a>
                <a href="orders.php?status=rejected" class="sub-link <?php echo ($currentPage === 'orders' && ($_GET['status'] ?? '') === 'rejected') ? 'active' : ''; ?>">
                    <i class="fa-solid fa-ban"></i> Rejected
                </a>
                <a href="customers.php" class="<?php echo $currentPage === 'customers' ? 'active' : ''; ?>">
                    <i class="fa-solid fa-users"></i> Customers
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <a href="../index.php" target="_blank">
                    <i class="fa-solid fa-store"></i> View Store
                </a>
                <a href="logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                </div>
                <div class="header-right">
                    <span class="admin-user">
                        <i class="fa-solid fa-user-shield"></i> <?php echo htmlspecialchars($adminName); ?>
                    </span>
                </div>
            </header>
            
            <div class="admin-content">
