<?php
/**
 * Cart Functions
 * Handles all shopping cart operations using session storage
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

/**
 * Initialize the cart in session
 */
function initCart() {
    initSession();
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Add item to cart
 * @param string $productId Product ID
 * @param int $quantity Quantity to add
 * @param string $size Selected size
 * @param string $color Selected color
 * @return array Result with success status and message
 */
function addToCart($productId, $quantity = 1, $size = '', $color = '') {
    initCart();
    
    // Get product details
    $product = getProductById($productId);
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found'];
    }
    
    // Create unique key for cart item (product + size + color combination)
    $cartKey = $productId . '_' . $size . '_' . $color;
    
    if (isset($_SESSION['cart'][$cartKey])) {
        // Update quantity if item already exists
        $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
    } else {
        // Add new item
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

/**
 * Update item quantity in cart
 * @param string $cartKey Cart item key
 * @param int $quantity New quantity
 * @return array Result with success status
 */
function updateCartItem($cartKey, $quantity) {
    initCart();
    
    if (!isset($_SESSION['cart'][$cartKey])) {
        return ['success' => false, 'message' => 'Item not found in cart'];
    }
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0 or negative
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

/**
 * Remove item from cart
 * @param string $cartKey Cart item key
 * @return array Result with success status
 */
function removeFromCart($cartKey) {
    initCart();
    
    if (isset($_SESSION['cart'][$cartKey])) {
        unset($_SESSION['cart'][$cartKey]);
        return ['success' => true, 'message' => 'Item removed from cart', 'cartCount' => getCartItemCount()];
    }
    
    return ['success' => false, 'message' => 'Item not found in cart'];
}

/**
 * Get all cart items
 * @return array Cart items
 */
function getCartItems() {
    initCart();
    return $_SESSION['cart'];
}

/**
 * Get cart item count (total number of items)
 * @return int Total item count
 */
function getCartItemCount() {
    initCart();
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

/**
 * Get cart subtotal (before tax)
 * @return float Subtotal amount
 */
function getCartSubtotal() {
    initCart();
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    return $subtotal;
}

/**
 * Get tax amount
 * @return float Tax amount
 */
function getCartTax() {
    return getCartSubtotal() * TAX_RATE;
}

/**
 * Get cart total (including tax)
 * @return float Total amount
 */
function getCartTotal() {
    return getCartSubtotal() + getCartTax();
}

/**
 * Clear the entire cart
 * @return array Result with success status
 */
function clearCart() {
    initSession();
    $_SESSION['cart'] = [];
    return ['success' => true, 'message' => 'Cart cleared'];
}

/**
 * Get cart summary
 * @return array Cart summary with items, subtotal, tax, and total
 */
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

/**
 * Create an order from the current cart
 * @param array $shippingInfo Shipping information
 * @return array Result with success status and order details
 */
function createOrder($shippingInfo = []) {
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login to complete your order', 'requireLogin' => true];
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
        
        // Insert order
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
        
        // Insert order items
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
        
        // Clear the cart after successful order
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

/**
 * Get user's order history
 * @param int $userId User ID
 * @return array List of orders
 */
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

/**
 * Get order details including items
 * @param int $orderId Order ID
 * @return array|null Order details or null if not found
 */
function getOrderDetails($orderId) {
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    // Get order
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();
    
    if (!$order) return null;
    
    // Check if current user owns this order
    if ($order['user_id'] !== getCurrentUserId()) {
        return null;
    }
    
    // Get order items
    $itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $itemStmt->execute([$orderId]);
    $order['items'] = $itemStmt->fetchAll();
    
    return $order;
}
?>
