<?php
require_once __DIR__ . '/../auth/admin_auth.php';
require_once __DIR__ . '/../includes/admin_functions.php';
requireAdminLogin();

$stats = getDashboardStats();
$recentOrders = getAllOrders();
$recentOrders = array_slice($recentOrders, 0, 5);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #333; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 20px; }
        .header a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-card h3 { font-size: 32px; color: #007bff; margin-bottom: 10px; }
        .stat-card p { color: #666; }
        .card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 20px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; }
        .status { padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .status.pending { background: #fff3cd; color: #856404; }
        .status.processing { background: #cce5ff; color: #004085; }
        .status.shipped { background: #d4edda; color: #155724; }
        .status.delivered { background: #d1ecf1; color: #0c5460; }
        .status.cancelled { background: #f8d7da; color: #721c24; }
        .nav { display: flex; gap: 15px; margin-bottom: 30px; }
        .nav a { padding: 10px 20px; background: white; text-decoration: none; color: #333; border-radius: 5px; }
        .nav a:hover, .nav a.active { background: #007bff; color: white; }
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
            <a href="index.php" class="active">Dashboard</a>
            <a href="orders.php">Orders</a>
            <a href="customers.php">Customers</a>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $stats['total_orders']; ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['pending_orders']; ?></h3>
                <p>Pending Orders</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $stats['total_customers']; ?></h3>
                <p>Customers</p>
            </div>
            <div class="stat-card">
                <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
        
        <div class="card">
            <h2>Recent Orders</h2>
            <table>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($recentOrders as $order): ?>
                <tr>
                    <td><?php echo $order['order_number']; ?></td>
                    <td><?php echo $order['customer_name']; ?></td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td><span class="status <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recentOrders)): ?>
                <tr><td colspan="5" style="text-align:center; padding:30px;">No orders yet</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
