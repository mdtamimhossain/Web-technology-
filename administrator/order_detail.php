<?php
require_once __DIR__ . '/../auth/admin_auth.php';
require_once __DIR__ . '/../includes/admin_functions.php';
requireAdminLogin();

$orderId = $_GET['id'] ?? 0;
$order = getOrderById($orderId);

if (!$order) {
    header('Location: orders.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order <?php echo $order['order_number']; ?> - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #333; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 20px; }
        .header a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1000px; margin: 30px auto; padding: 0 20px; }
        .back { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
        .card { background: white; border-radius: 10px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 15px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .info-item { margin-bottom: 10px; }
        .info-item label { display: block; color: #666; font-size: 14px; margin-bottom: 3px; }
        .info-item span { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
        .total-row { font-weight: bold; font-size: 16px; }
        .status { padding: 5px 12px; border-radius: 15px; font-size: 12px; display: inline-block; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.processing { background: #cce5ff; color: #004085; }
        .status.shipped { background: #d4edda; color: #155724; }
        .status.delivered { background: #d1ecf1; color: #0c5460; }
        .status.cancelled { background: #f8d7da; color: #721c24; }
        .discount-row { color: green; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel</h1>
        <div>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <a href="orders.php" class="back">&larr; Back to Orders</a>
        
        <div class="card">
            <h2>Order <?php echo $order['order_number']; ?></h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Customer</label>
                    <span><?php echo $order['customer_name']; ?></span>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <span><?php echo $order['customer_email']; ?></span>
                </div>
                <div class="info-item">
                    <label>Date</label>
                    <span><?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Shipping Info</h2>
            <div class="info-grid">
                <div class="info-item">
                    <label>Name</label>
                    <span><?php echo $order['shipping_name'] ?: '-'; ?></span>
                </div>
                <div class="info-item">
                    <label>Phone</label>
                    <span><?php echo $order['shipping_phone'] ?: '-'; ?></span>
                </div>
                <div class="info-item" style="grid-column: span 2;">
                    <label>Address</label>
                    <span><?php echo $order['shipping_address'] ?: '-'; ?></span>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Order Items</h2>
            <table>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?php echo $item['product_name']; ?></td>
                    <td><?php echo $item['size'] ?: '-'; ?></td>
                    <td><?php echo $item['color'] ?: '-'; ?></td>
                    <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['product_price'] * $item['quantity'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" style="text-align:right;">Subtotal:</td>
                    <td>$<?php echo number_format($order['subtotal'], 2); ?></td>
                </tr>
                <?php if ($order['discount_amount'] > 0): ?>
                <tr class="discount-row">
                    <td colspan="5" style="text-align:right;">Discount:</td>
                    <td>-$<?php echo number_format($order['discount_amount'], 2); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="5" style="text-align:right;">Tax:</td>
                    <td>$<?php echo number_format($order['tax_amount'], 2); ?></td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" style="text-align:right;">Total:</td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
