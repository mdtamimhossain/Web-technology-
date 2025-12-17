<?php
require_once './../includes/cart_functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order = getOrderDetails($orderId);

if (!$order) {
    header('Location: myorders.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order <?php echo htmlspecialchars($order['order_number']); ?> - Krist</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/myorders.css">
    <link rel="stylesheet" href="./../CSS/shoppingCart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb container">
    <a href="./../index.php">Home</a> / <a href="./myorders.php">My Orders</a> / <span><?php echo htmlspecialchars($order['order_number']); ?></span>
</div>

<section class="order-details-page container">
    <div class="order-details-header">
        <div>
            <h1><i class="fa fa-receipt"></i> Order Details</h1>
            <p class="order-number-large"><?php echo htmlspecialchars($order['order_number']); ?></p>
        </div>
        <div class="order-status status-<?php echo $order['status']; ?>">
            <?php echo ucfirst($order['status']); ?>
        </div>
    </div>
    
    <div class="order-meta">
        <div class="meta-item">
            <i class="fa fa-calendar"></i>
            <span>Order Date: <?php echo date('F d, Y - h:i A', strtotime($order['created_at'])); ?></span>
        </div>
    </div>
    
    <div class="order-sections">
        <!-- Order Items -->
        <div class="order-items-section">
            <h2><i class="fa fa-box"></i> Items Ordered</h2>
            <div class="order-items-list">
                <?php foreach ($order['items'] as $item): ?>
                <div class="order-item-row">
                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                        <?php if (!empty($item['size']) || !empty($item['color'])): ?>
                        <p class="item-variants">
                            <?php if (!empty($item['size'])): ?>Size: <?php echo htmlspecialchars($item['size']); ?><?php endif; ?>
                            <?php if (!empty($item['color'])): ?> | Color: <?php echo htmlspecialchars($item['color']); ?><?php endif; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <div class="item-pricing">
                        <span class="item-qty">Qty: <?php echo $item['quantity']; ?></span>
                        <span class="item-price">$<?php echo number_format($item['product_price'], 2); ?></span>
                        <span class="item-subtotal">$<?php echo number_format($item['subtotal'], 2); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="order-sidebar">
            <!-- Payment Summary -->
            <div class="summary-card">
                <h3><i class="fa fa-credit-card"></i> Payment Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (<?php echo TAX_RATE * 100; ?>%)</span>
                    <span>$<?php echo number_format($order['tax_amount'], 2); ?></span>
                </div>
                <div class="summary-row total-row">
                    <span>Total</span>
                    <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
            
            <!-- Shipping Info -->
            <?php if (!empty($order['shipping_name'])): ?>
            <div class="summary-card">
                <h3><i class="fa fa-truck"></i> Shipping Address</h3>
                <p><strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                <?php if (!empty($order['shipping_phone'])): ?>
                <p><i class="fa fa-phone"></i> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($order['notes'])): ?>
            <div class="summary-card">
                <h3><i class="fa fa-sticky-note"></i> Order Notes</h3>
                <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="order-actions">
        <a href="./myorders.php" class="btn-secondary">
            <i class="fa fa-arrow-left"></i> Back to Orders
        </a>
        <a href="./list.php" class="btn-primary">
            <i class="fa fa-shopping-cart"></i> Continue Shopping
        </a>
    </div>
</section>

<style>
.order-details-page {
    padding: 20px 0 60px;
}

.order-details-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.order-details-header h1 {
    font-size: 24px;
    margin: 0 0 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.order-number-large {
    font-size: 14px;
    color: var(--light-gray);
    margin: 0;
}

.order-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-light);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--medium-gray);
}

.meta-item i {
    color: var(--light-gray);
}

.order-sections {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 30px;
}

@media (max-width: 992px) {
    .order-sections {
        grid-template-columns: 1fr;
    }
}

.order-items-section {
    background: var(--bg);
    border: 1px solid var(--border-light);
    border-radius: 12px;
    padding: 20px;
}

.order-items-section h2 {
    font-size: 18px;
    margin: 0 0 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.order-item-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid var(--border-light);
}

.order-item-row:last-child {
    border-bottom: none;
}

.item-info h4 {
    margin: 0 0 5px;
    font-size: 15px;
}

.item-variants {
    font-size: 12px;
    color: var(--light-gray);
    margin: 0;
}

.item-pricing {
    display: flex;
    align-items: center;
    gap: 20px;
    font-size: 14px;
}

.item-qty {
    color: var(--light-gray);
}

.item-price {
    color: var(--medium-gray);
}

.item-subtotal {
    font-weight: 700;
    color: var(--primary);
}

.order-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.summary-card {
    background: var(--muted);
    border-radius: 12px;
    padding: 20px;
}

.summary-card h3 {
    font-size: 16px;
    margin: 0 0 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.summary-card p {
    margin: 5px 0;
    font-size: 14px;
    color: var(--medium-gray);
}

.order-details-page .order-actions {
    margin-top: 30px;
    display: flex;
    gap: 15px;
}
</style>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
<script src="./../JS/cart.js"></script>
</body>
</html>
