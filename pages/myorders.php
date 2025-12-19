<?php
require_once './../includes/cart_functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=myorders.php');
    exit;
}

// Handle cancel order request
$cancelMessage = '';
$cancelError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_order'])) {
    $orderId = (int)$_POST['order_id'];
    $result = cancelOrder($orderId);
    if ($result['success']) {
        $cancelMessage = 'Order cancelled successfully';
    } else {
        $cancelError = $result['message'];
    }
}

$orders = getUserOrders();
$currentUser = getCurrentUser();

// Check if user is blocked
$blockedStatus = isCustomerBlocked(getCurrentUserId());
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
    
    <?php if ($blockedStatus['blocked']): ?>
    <div class="blocked-notice">
        <i class="fa fa-exclamation-triangle"></i>
        <strong>Your account is blocked by the administrator.</strong>
        <?php if ($blockedStatus['reason']): ?>
        <p>Reason: <?php echo htmlspecialchars($blockedStatus['reason']); ?></p>
        <?php endif; ?>
        <p>You cannot place new orders while your account is blocked.</p>
    </div>
    <?php endif; ?>
    
    <?php if ($cancelMessage): ?>
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($cancelMessage); ?>
    </div>
    <?php endif; ?>
    
    <?php if ($cancelError): ?>
    <div class="alert alert-error">
        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($cancelError); ?>
    </div>
    <?php endif; ?>
    
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
        <div class="order-card <?php echo in_array($order['status'], ['cancelled', 'rejected']) ? 'order-inactive' : ''; ?>">
            <div class="order-header">
                <div class="order-info">
                    <span class="order-number"><?php echo htmlspecialchars($order['order_number']); ?></span>
                    <span class="order-date">
                        <i class="fa fa-calendar"></i> 
                        <?php echo date('M d, Y - h:i A', strtotime($order['created_at'])); ?>
                    </span>
                </div>
                <div class="order-status status-<?php echo $order['status']; ?>">
                    <?php 
                    $statusLabels = [
                        'pending' => 'Ordered',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'rejected' => 'Rejected'
                    ];
                    echo $statusLabels[$order['status']] ?? ucfirst($order['status']); 
                    ?>
                </div>
            </div>
            
            <?php if ($order['status'] === 'rejected' && !empty($order['rejection_reason'])): ?>
            <div class="rejection-notice">
                <i class="fa fa-info-circle"></i>
                <strong>Order Rejected:</strong> <?php echo htmlspecialchars($order['rejection_reason']); ?>
            </div>
            <?php endif; ?>
            
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
                
                <?php if ($order['status'] === 'shipped' && !empty($order['shipped_at'])): ?>
                <div class="shipping-status">
                    <i class="fa fa-truck"></i> Shipped on <?php echo date('M d, Y', strtotime($order['shipped_at'])); ?>
                </div>
                <?php endif; ?>
                
                <?php if ($order['status'] === 'delivered' && !empty($order['delivered_at'])): ?>
                <div class="delivery-status">
                    <i class="fa fa-check-circle"></i> Delivered on <?php echo date('M d, Y', strtotime($order['delivered_at'])); ?>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="order-actions">
                <a href="./order_details.php?id=<?php echo $order['id']; ?>" class="btn-secondary btn-small">
                    <i class="fa fa-eye"></i> View Details
                </a>
                
                <?php if ($order['status'] === 'pending'): ?>
                <form method="POST" class="cancel-form" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <button type="submit" name="cancel_order" class="btn-danger btn-small">
                        <i class="fa fa-times"></i> Cancel Order
                    </button>
                </form>
                <?php endif; ?>
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
