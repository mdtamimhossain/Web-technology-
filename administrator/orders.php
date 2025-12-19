<?php
require_once __DIR__ . '/../auth/admin_auth.php';
require_once __DIR__ . '/../includes/admin_functions.php';
requireAdminLogin();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    updateOrderStatus($_POST['order_id'], $_POST['status']);
    header('Location: orders.php');
    exit;
}

$status = $_GET['status'] ?? null;
$orders = getAllOrders($status);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Orders - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #333; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 20px; }
        .header a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .nav { display: flex; gap: 15px; margin-bottom: 30px; }
        .nav a { padding: 10px 20px; background: white; text-decoration: none; color: #333; border-radius: 5px; }
        .nav a:hover, .nav a.active { background: #007bff; color: white; }
        .filters { margin-bottom: 20px; }
        .filters a { margin-right: 10px; padding: 8px 15px; background: #eee; text-decoration: none; color: #333; border-radius: 5px; }
        .filters a.active { background: #007bff; color: white; }
        .card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
        .status { padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.processing { background: #cce5ff; color: #004085; }
        .status.shipped { background: #d4edda; color: #155724; }
        .status.delivered { background: #d1ecf1; color: #0c5460; }
        .status.cancelled { background: #f8d7da; color: #721c24; }
        select { padding: 5px 10px; border-radius: 5px; border: 1px solid #ddd; }
        .btn { padding: 5px 10px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        a.view { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel</h1>
        <div>
            <span>Welcome, <?php echo getAdminName(); ?></span>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="nav">
            <a href="index.php">Dashboard</a>
            <a href="orders.php" class="active">Orders</a>
            <a href="customers.php">Customers</a>
        </div>
        
        <div class="filters">
            <a href="orders.php" class="<?php echo !$status ? 'active' : ''; ?>">All</a>
            <a href="orders.php?status=pending" class="<?php echo $status == 'pending' ? 'active' : ''; ?>">Pending</a>
            <a href="orders.php?status=processing" class="<?php echo $status == 'processing' ? 'active' : ''; ?>">Processing</a>
            <a href="orders.php?status=shipped" class="<?php echo $status == 'shipped' ? 'active' : ''; ?>">Shipped</a>
            <a href="orders.php?status=delivered" class="<?php echo $status == 'delivered' ? 'active' : ''; ?>">Delivered</a>
            <a href="orders.php?status=cancelled" class="<?php echo $status == 'cancelled' ? 'active' : ''; ?>">Cancelled</a>
        </div>
        
        <div class="card">
            <table>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo $order['order_number']; ?></td>
                    <td><?php echo $order['customer_name']; ?><br><small><?php echo $order['customer_email']; ?></small></td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td><span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </form>
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="view">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr><td colspan="6" style="text-align:center; padding:30px;">No orders found</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
