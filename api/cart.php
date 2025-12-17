<?php
/**
 * Cart API Endpoint
 * Handles AJAX requests for cart operations
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/cart_functions.php';

// Get the action from POST or GET
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'add':
        $productId = $_POST['product_id'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 1);
        $size = $_POST['size'] ?? '';
        $color = $_POST['color'] ?? '';
        
        if (empty($productId)) {
            $response = ['success' => false, 'message' => 'Product ID is required'];
        } else {
            $response = addToCart($productId, $quantity, $size, $color);
        }
        break;
        
    case 'update':
        $cartKey = $_POST['cart_key'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        if (empty($cartKey)) {
            $response = ['success' => false, 'message' => 'Cart key is required'];
        } else {
            $response = updateCartItem($cartKey, $quantity);
            $response['cartSummary'] = getCartSummary();
        }
        break;
        
    case 'remove':
        $cartKey = $_POST['cart_key'] ?? '';
        
        if (empty($cartKey)) {
            $response = ['success' => false, 'message' => 'Cart key is required'];
        } else {
            $response = removeFromCart($cartKey);
            $response['cartSummary'] = getCartSummary();
        }
        break;
        
    case 'get':
        $response = [
            'success' => true,
            'cartSummary' => getCartSummary()
        ];
        break;
        
    case 'count':
        $response = [
            'success' => true,
            'count' => getCartItemCount()
        ];
        break;
        
    case 'clear':
        $response = clearCart();
        break;
        
    case 'checkout':
        $shippingInfo = [
            'name' => $_POST['shipping_name'] ?? '',
            'address' => $_POST['shipping_address'] ?? '',
            'phone' => $_POST['shipping_phone'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];
        $response = createOrder($shippingInfo);
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Unknown action: ' . $action];
}

echo json_encode($response);
?>
