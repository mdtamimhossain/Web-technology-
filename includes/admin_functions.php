<?php
require_once __DIR__ . '/../database/db.php';

// Get all orders
function getAllOrders($status = null) {
    $pdo = getDBConnection();
    
    if ($status) {
        $stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email 
                               FROM orders o 
                               JOIN users u ON o.user_id = u.id 
                               WHERE o.status = ? 
                               ORDER BY o.created_at DESC");
        $stmt->execute([$status]);
    } else {
        $stmt = $pdo->query("SELECT o.*, u.name as customer_name, u.email as customer_email 
                             FROM orders o 
                             JOIN users u ON o.user_id = u.id 
                             ORDER BY o.created_at DESC");
    }
    return $stmt->fetchAll();
}

// Get single order
function getOrderById($id) {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT o.*, u.name as customer_name, u.email as customer_email 
                           FROM orders o 
                           JOIN users u ON o.user_id = u.id 
                           WHERE o.id = ?");
    $stmt->execute([$id]);
    $order = $stmt->fetch();
    
    if ($order) {
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$id]);
        $order['items'] = $stmt->fetchAll();
    }
    return $order;
}

// Update order status
function updateOrderStatus($orderId, $status) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $orderId]);
}

// Get all customers
function getAllCustomers() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

// Block/Unblock customer
function blockCustomer($userId, $block = true, $reason = '') {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE users SET is_blocked = ?, blocked_reason = ? WHERE id = ?");
    return $stmt->execute([$block ? 1 : 0, $reason, $userId]);
}

// Get dashboard stats
function getDashboardStats() {
    $pdo = getDBConnection();
    
    $stats = [];
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $stats['total_orders'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['total_customers'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled'");
    $stats['total_revenue'] = $stmt->fetchColumn() ?? 0;
    
    return $stats;
}
?>
