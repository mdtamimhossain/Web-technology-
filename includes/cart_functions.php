<?php
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../auth/auth.php';
require_once __DIR__ . '/functions.php';

function initCart() {
    initSession();
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

function addToCart($productId, $quantity = 1, $size = '', $color = '') {
    initCart();
    $product = getProductById($productId);
    if (!$product) {
        return ['success' => false, 'message' => 'Product not found'];
    }
    
    $cartKey = $productId . '_' . $size . '_' . $color;
    
    if (isset($_SESSION['cart'][$cartKey])) {
        $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
    } else {
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
    return ['success' => true, 'message' => 'Added to cart', 'cartCount' => getCartItemCount()];
}

function updateCartItem($cartKey, $quantity) {
    initCart();
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$cartKey]);
    } else {
        $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
    }
    return ['success' => true, 'cartCount' => getCartItemCount()];
}

function removeFromCart($cartKey) {
    initCart();
    unset($_SESSION['cart'][$cartKey]);
    return ['success' => true, 'cartCount' => getCartItemCount()];
}

function getCartItems() {
    initCart();
    return $_SESSION['cart'];
}

function getCartItemCount() {
    initCart();
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

function getCartSubtotal() {
    initCart();
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    return $subtotal;
}

function clearCart() {
    initSession();
    $_SESSION['cart'] = [];
}

// Simple discount: every 10th order gets 10%, every 20th gets 20%
function getDiscountPercent() {
    if (!isLoggedIn()) {
        return 0;
    }
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $orderCount = $stmt->fetchColumn() + 1;
    
    if ($orderCount % 20 == 0) {
        return 20;
    } else if ($orderCount % 10 == 0) {
        return 10;
    }
    return 0;
}

function getCartSummary() {
    $subtotal = getCartSubtotal();
    $discountPercent = getDiscountPercent();
    $discountAmount = $subtotal * $discountPercent / 100;
    $afterDiscount = $subtotal - $discountAmount;
    $tax = $afterDiscount * TAX_RATE;
    $total = $afterDiscount + $tax;
    
    return [
        'items' => getCartItems(),
        'itemCount' => getCartItemCount(),
        'subtotal' => $subtotal,
        'discount' => $discountPercent,
        'tax' => $tax,
        'taxRate' => TAX_RATE * 100,
        'total' => $total
    ];
}

function createOrder($shippingInfo = []) {
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Please login first', 'requireLogin' => true];
    }
    
    $cartItems = getCartItems();
    if (empty($cartItems)) {
        return ['success' => false, 'message' => 'Cart is empty'];
    }
    
    $pdo = getDBConnection();
    
    try {
        $pdo->beginTransaction();
        
        $userId = getCurrentUserId();
        $orderNumber = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
        $subtotal = getCartSubtotal();
        $discountPercent = getDiscountPercent();
        $discountAmount = $subtotal * $discountPercent / 100;
        $afterDiscount = $subtotal - $discountAmount;
        $tax = $afterDiscount * TAX_RATE;
        $total = $afterDiscount + $tax;
        
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, subtotal, discount_amount, tax_amount, total_amount, shipping_name, shipping_address, shipping_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $userId,
            $orderNumber,
            $subtotal,
            $discountAmount,
            $tax,
            $total,
            $shippingInfo['name'] ?? '',
            $shippingInfo['address'] ?? '',
            $shippingInfo['phone'] ?? ''
        ]);
        
        $orderId = $pdo->lastInsertId();
        
        foreach ($cartItems as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, size, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $orderId,
                $item['product_id'],
                $item['name'],
                $item['price'],
                $item['quantity'],
                $item['size'],
                $item['color']
            ]);
        }
        
        $pdo->commit();
        clearCart();
        
        return [
            'success' => true, 
            'orderNumber' => $orderNumber, 
            'total' => $total,
            'discount' => $discountPercent,
            'subtotal' => $subtotal
        ];
        
    } catch(PDOException $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Order failed'];
    }
}

function getUserOrders() {
    if (!isLoggedIn()) return [];
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([getCurrentUserId()]);
    return $stmt->fetchAll();
}

function cancelOrder($orderId) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->execute([$orderId, getCurrentUserId()]);
    return ['success' => true];
}
?>
