<?php
/**
 * Admin Customers API
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
    case 'block':
        $customerId = (int)($_POST['customer_id'] ?? 0);
        $reason = $_POST['reason'] ?? '';
        
        if (!$customerId) {
            $response = ['success' => false, 'message' => 'Customer ID is required'];
            break;
        }
        
        $result = setCustomerBlockStatus($customerId, true, $reason);
        
        if ($result) {
            $response = ['success' => true, 'message' => 'Customer blocked'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to block customer'];
        }
        break;
        
    case 'unblock':
        $customerId = (int)($_POST['customer_id'] ?? 0);
        
        if (!$customerId) {
            $response = ['success' => false, 'message' => 'Customer ID is required'];
            break;
        }
        
        $result = setCustomerBlockStatus($customerId, false, null);
        
        if ($result) {
            $response = ['success' => true, 'message' => 'Customer unblocked'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to unblock customer'];
        }
        break;
        
    case 'get_customer':
        $customerId = (int)($_GET['customer_id'] ?? 0);
        
        if (!$customerId) {
            $response = ['success' => false, 'message' => 'Customer ID is required'];
            break;
        }
        
        $customer = getCustomerById($customerId);
        
        if ($customer) {
            $response = ['success' => true, 'customer' => $customer];
        } else {
            $response = ['success' => false, 'message' => 'Customer not found'];
        }
        break;
        
    case 'get_customers':
        $search = $_GET['search'] ?? '';
        $blockedOnly = isset($_GET['blocked']);
        $customers = getAllCustomers($search, $blockedOnly);
        $response = ['success' => true, 'customers' => $customers];
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Unknown action'];
}

echo json_encode($response);
?>
