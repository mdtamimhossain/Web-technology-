<?php
/**
 * Admin Functions
 * Order and Customer management functions
 */

require_once __DIR__ . '/../database/db.php';

/**
 * Get orders by status
 */
function getOrdersByStatus($status = null, $limit = 50) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    if ($status) {
        $stmt = $pdo->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.status = ?
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$status, $limit]);
    } else {
        $stmt = $pdo->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
    }
    
    return $stmt->fetchAll();
}

/**
 * Get order by ID with items
 */
function getOrderById($orderId) {
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    $stmt = $pdo->prepare("
        SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone,
               u.address as customer_address, u.is_blocked as customer_blocked
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if ($order) {
        $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $order['items'] = $stmt->fetchAll();
    }
    
    return $order;
}

/**
 * Update order status
 */
function updateOrderStatus($orderId, $status, $adminId, $reason = null) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    $allowedStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'rejected'];
    if (!in_array($status, $allowedStatuses)) {
        return false;
    }
    
    $updates = ['status' => $status, 'processed_by' => $adminId];
    $sql = "UPDATE orders SET status = ?, processed_by = ?";
    $params = [$status, $adminId];
    
    if ($status === 'processing' || $status === 'rejected') {
        $sql .= ", processed_at = NOW()";
    }
    
    if ($status === 'shipped') {
        $sql .= ", shipped_at = NOW()";
    }
    
    if ($status === 'delivered') {
        $sql .= ", delivered_at = NOW()";
    }
    
    if ($status === 'rejected' && $reason) {
        $sql .= ", rejection_reason = ?";
        $params[] = $reason;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $orderId;
    
    $stmt = $pdo->prepare($sql);
    return $stmt->execute($params);
}

/**
 * Add admin notes to order
 */
function addAdminNotes($orderId, $notes) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    $stmt = $pdo->prepare("UPDATE orders SET admin_notes = ? WHERE id = ?");
    return $stmt->execute([$notes, $orderId]);
}

/**
 * Get all customers
 */
function getAllCustomers($search = '', $blockedOnly = false) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    $sql = "SELECT u.*, 
            (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
            (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id AND status NOT IN ('cancelled', 'rejected')) as total_spent
            FROM users u WHERE 1=1";
    $params = [];
    
    if ($search) {
        $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
        $searchTerm = "%$search%";
        $params = [$searchTerm, $searchTerm, $searchTerm];
    }
    
    if ($blockedOnly) {
        $sql .= " AND u.is_blocked = 1";
    }
    
    $sql .= " ORDER BY u.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get customer by ID
 */
function getCustomerById($customerId) {
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    $stmt = $pdo->prepare("
        SELECT u.*, 
        (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count,
        (SELECT SUM(total_amount) FROM orders WHERE user_id = u.id AND status NOT IN ('cancelled', 'rejected')) as total_spent
        FROM users u WHERE u.id = ?
    ");
    $stmt->execute([$customerId]);
    return $stmt->fetch();
}

/**
 * Block/Unblock customer
 */
function setCustomerBlockStatus($customerId, $blocked, $reason = null) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    $stmt = $pdo->prepare("UPDATE users SET is_blocked = ?, blocked_reason = ? WHERE id = ?");
    return $stmt->execute([$blocked ? 1 : 0, $reason, $customerId]);
}

/**
 * Check if customer is blocked
 */
function isCustomerBlocked($userId) {
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    $stmt = $pdo->prepare("SELECT is_blocked, blocked_reason FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user && $user['is_blocked']) {
        return ['blocked' => true, 'reason' => $user['blocked_reason']];
    }
    return ['blocked' => false, 'reason' => null];
}

/**
 * Get order statistics
 */
function getOrderStats() {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    $stats = [];
    
    // Count by status
    $stmt = $pdo->query("SELECT status, COUNT(*) as count FROM orders GROUP BY status");
    $statusCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $stats['pending'] = $statusCounts['pending'] ?? 0;
    $stats['processing'] = $statusCounts['processing'] ?? 0;
    $stats['shipped'] = $statusCounts['shipped'] ?? 0;
    $stats['delivered'] = $statusCounts['delivered'] ?? 0;
    $stats['cancelled'] = $statusCounts['cancelled'] ?? 0;
    $stats['rejected'] = $statusCounts['rejected'] ?? 0;
    
    // Total revenue
    $stmt = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status IN ('delivered', 'shipped', 'processing')");
    $stats['total_revenue'] = $stmt->fetchColumn() ?? 0;
    
    // Total customers
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $stats['total_customers'] = $stmt->fetchColumn() ?? 0;
    
    // Blocked customers
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_blocked = 1");
    $stats['blocked_customers'] = $stmt->fetchColumn() ?? 0;
    
    return $stats;
}

/**
 * Get customer orders
 */
function getCustomerOrders($customerId) {
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    $stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$customerId]);
    return $stmt->fetchAll();
}
?>
