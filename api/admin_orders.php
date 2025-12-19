<?php
/**
 * Admin Orders API
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../auth/admin_auth.php';
require_once __DIR__ . '/../includes/admin_functions.php';

// Check admin login
if (!isAdminLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'update_status':
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $reason = $_POST['reason'] ?? '';
        
        if (!$orderId || !$status) {
            $response = ['success' => false, 'message' => 'Order ID and status are required'];
            break;
        }
        
        $result = updateOrderStatus($orderId, $status, getCurrentAdminId(), $reason);
        
        if ($result) {
            $response = ['success' => true, 'message' => 'Order status updated'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to update order status'];
        }
        break;
        
    case 'add_notes':
        $orderId = (int)($_POST['order_id'] ?? 0);
        $notes = $_POST['notes'] ?? '';
        
        if (!$orderId) {
            $response = ['success' => false, 'message' => 'Order ID is required'];
            break;
        }
        
        $result = addAdminNotes($orderId, $notes);
        
        if ($result) {
            $response = ['success' => true, 'message' => 'Notes added'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to add notes'];
        }
        break;
        
    case 'get_order':
        $orderId = (int)($_GET['order_id'] ?? 0);
        
        if (!$orderId) {
            $response = ['success' => false, 'message' => 'Order ID is required'];
            break;
        }
        
        $order = getOrderById($orderId);
        
        if ($order) {
            $response = ['success' => true, 'order' => $order];
        } else {
            $response = ['success' => false, 'message' => 'Order not found'];
        }
        break;
        
    case 'get_orders':
        $status = $_GET['status'] ?? null;
        $orders = getOrdersByStatus($status);
        $response = ['success' => true, 'orders' => $orders];
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Unknown action'];
}

echo json_encode($response);
?>
