<?php
require_once './../includes/cart_functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=myorders.php');
    exit;
}

$orders = getUserOrders();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Krist</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/myorders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb container">
    <a href="./../index.php">Home</a> / <span>My Orders</span>
</div>

<section class="orders-page container">
    <h1><i class="fa fa-box"></i> My Orders</h1>
    
    <?php if (empty($orders)): ?>
    <!-- Empty Orders State -->
    <div class="empty-orders">
        <div class="empty-icon">
            <i class="fa fa-shopping-bag"></i>
        </div>
        <h2>No orders yet</h2>
        <p>You haven't placed any orders yet. Start shopping to see your orders here!</p>
        <a href="./list.php" class="btn-primary">
            <i class="fa fa-shopping-cart"></i> Start Shopping
        </a>
    </div>
    <?php else: ?>
    
    <div class="orders-list">
        <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-header">
                <div class="order-info">
                    <span class="order-number"><?php echo htmlspecialchars($order['order_number']); ?></span>
                    <span class="order-date">
                        <i class="fa fa-calendar"></i> 
                        <?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?>
                    </span>
                </div>
                <div class="order-status status-<?php echo $order['status']; ?>">
                    <?php echo ucfirst($order['status']); ?>
                </div>
            </div>
            
            <div class="order-details">
                <div class="order-summary">
                    <div class="summary-item">
                        <span class="label">Subtotal:</span>
                        <span>$<?php echo number_format($order['subtotal'], 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Tax:</span>
                        <span>$<?php echo number_format($order['tax_amount'], 2); ?></span>
                    </div>
                    <div class="summary-item total">
                        <span class="label">Total:</span>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($order['shipping_address'])): ?>
                <div class="shipping-info">
                    <h4><i class="fa fa-truck"></i> Shipping To:</h4>
                    <p><?php echo htmlspecialchars($order['shipping_name']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    <?php if (!empty($order['shipping_phone'])): ?>
                        <p><i class="fa fa-phone"></i> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="order-actions">
                <a href="./order_details.php?id=<?php echo $order['id']; ?>" class="btn-secondary btn-small">
                    <i class="fa fa-eye"></i> View Details
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
<script src="./../JS/cart.js"></script>
</body>
</html>
