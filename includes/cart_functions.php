<?php
/**
 * Cart Functions
 * Shopping cart er sob operation handle kore
 */

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../auth/auth.php';
require_once __DIR__ . '/functions.php';

// Customer blocked kina check kore
function isCustomerBlocked($userId) {
    $pdo = getDBConnection();
    if (!$pdo) return ['blocked' => false, 'reason' => null];
    
    $stmt = $pdo->prepare("SELECT is_blocked, blocked_reason FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if ($user && $user['is_blocked']) {
        return ['blocked' => true, 'reason' => $user['blocked_reason']];
    }
    return ['blocked' => false, 'reason' => null];
}

// Session e cart initialize kore
function initCart() {
    initSession();
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

// Cart e product add kore
function addToCart($productId, $quantity = 1, $size = '', $color = '') {
    initCart();
    
    // Product er details ber kore
    $product = getProductById($productId);
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found'];
    }
    
    // Unique key banay (product + size + color combination)
    $cartKey = $productId . '_' . $size . '_' . $color;
    
    if (isset($_SESSION['cart'][$cartKey])) {
        // Item age thakle quantity update kore
        $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
    } else {
        // Notun item add kore
        $_SESSION['cart'][$cartKey] = [
            'product_id' => $productId,
            'name' => $product['name'],
            'price' => $product['price'],
            'image' => $product['image'],
            'size' => $size,
            'color' => $color,
            'quantity' => $quantity
        ];
    }
    
    return [
        'success' => true, 
        'message' => 'Product added to cart',
        'cartCount' => getCartItemCount()
    ];
}

// Cart e item er quantity update kore
function updateCartItem($cartKey, $quantity) {
    initCart();
    
    if (!isset($_SESSION['cart'][$cartKey])) {
        return ['success' => false, 'message' => 'Item not found in cart'];
    }
    
    if ($quantity <= 0) {
        // Quantity 0 hole item remove kore
        unset($_SESSION['cart'][$cartKey]);
        return ['success' => true, 'message' => 'Item removed from cart', 'cartCount' => getCartItemCount()];
    }
    
    $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
    
    return [
        'success' => true, 
        'message' => 'Cart updated',
        'cartCount' => getCartItemCount()
    ];
}

// Cart theke item remove kore
function removeFromCart($cartKey) {
    initCart();
    
    if (isset($_SESSION['cart'][$cartKey])) {
        unset($_SESSION['cart'][$cartKey]);
        return ['success' => true, 'message' => 'Item removed from cart', 'cartCount' => getCartItemCount()];
    }
    
    return ['success' => false, 'message' => 'Item not found in cart'];
}

// Sob cart items return kore
function getCartItems() {
    initCart();
    return $_SESSION['cart'];
}

// Cart e total item count return kore
function getCartItemCount() {
    initCart();
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

// Cart er subtotal (tax chara) return kore
function getCartSubtotal() {
    initCart();
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    return $subtotal;
}

// Tax amount return kore
function getCartTax() {
    return getCartSubtotal() * TAX_RATE;
}

// Cart er total (tax shoho) return kore
function getCartTotal() {
    return getCartSubtotal() + getCartTax();
}

// Pura cart clear kore
function clearCart() {
    initSession();
    $_SESSION['cart'] = [];
    return ['success' => true, 'message' => 'Cart cleared'];
}

// Cart er summary return kore
function getCartSummary() {
    return [
        'items' => getCartItems(),
        'itemCount' => getCartItemCount(),
        'subtotal' => getCartSubtotal(),
        'taxRate' => TAX_RATE * 100,
        'tax' => getCartTax(),
        'total' => getCartTotal()
    ];
}

// Cart theke order create kore
function createOrder($shippingInfo = []) {
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login to complete your order', 'requireLogin' => true];
    }
    
    // Customer blocked kina check kore
    $blockedStatus = isCustomerBlocked(getCurrentUserId());
    if ($blockedStatus['blocked']) {
        $reason = $blockedStatus['reason'] ? ': ' . $blockedStatus['reason'] : '';
        return ['success' => false, 'message' => 'Your account is blocked by the administrator' . $reason, 'blocked' => true];
    }
    
    $cartItems = getCartItems();
    if (empty($cartItems)) {
        return ['success' => false, 'message' => 'Your cart is empty'];
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    try {
        $pdo->beginTransaction();
        
        $userId = getCurrentUserId();
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        $subtotal = getCartSubtotal();
        $tax = getCartTax();
        $total = getCartTotal();
        
        // Order insert kore
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, order_number, subtotal, tax_amount, total_amount, 
                               shipping_name, shipping_address, shipping_phone, notes)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId,
            $orderNumber,
            $subtotal,
            $tax,
            $total,
            $shippingInfo['name'] ?? '',
            $shippingInfo['address'] ?? '',
            $shippingInfo['phone'] ?? '',
            $shippingInfo['notes'] ?? ''
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        // Order items insert kore
        $itemStmt = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, product_name, product_price, 
                                    quantity, size, color, subtotal)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($cartItems as $item) {
            $itemSubtotal = $item['price'] * $item['quantity'];
            $itemStmt->execute([
                $orderId,
                $item['product_id'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $item['size'],
                $item['color'],
                $itemSubtotal
            ]);
        }
        
        $pdo->commit();
        
        // Order hoe gele cart clear kore
        clearCart();
        
        return [
            'success' => true,
            'message' => 'Order placed successfully',
            'orderId' => $orderId,
            'orderNumber' => $orderNumber,
            'total' => $total
        ];
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Order creation failed: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to create order. Please try again.'];
    }
}

// User er order history return kore
function getUserOrders($userId = null) {
    if ($userId === null) {
        $userId = getCurrentUserId();
    }
    
    if (!$userId) {
        return [];
    }
    
    $pdo = getDBConnection();
    if (!$pdo) return [];
    
    $stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// Order cancel kore (shudhu pending hole)
function cancelOrder($orderId) {
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login to cancel order'];
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Order ber kore ar ownership verify kore
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$orderId, getCurrentUserId()]);
    $order = $stmt->fetch();
    
    if (!$order) {
        return ['success' => false, 'message' => 'Order not found'];
    }
    
    // Shudhu pending order cancel kora jay
    if ($order['status'] !== 'pending') {
        return ['success' => false, 'message' => 'Only pending orders can be cancelled. Your order is already ' . $order['status'] . '.'];
    }
    
    // Order cancel kore
    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
    $result = $stmt->execute([$orderId]);
    
    if ($result) {
        return ['success' => true, 'message' => 'Order cancelled successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to cancel order'];
}

// Order er details return kore items shoho
function getOrderDetails($orderId) {
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    // Order ber kore
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if (!$order) return null;
    
    // Current user er order kina check kore
    if ($order['user_id'] !== getCurrentUserId()) {
        return null;
    }
    
    // Order items ber kore
    $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemStmt->execute([$orderId]);
    $order['items'] = $itemStmt->fetchAll();
    
    return $order;
}
?>
