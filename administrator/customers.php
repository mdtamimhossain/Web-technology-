<?php
require_once __DIR__ . '/../auth/admin_auth.php';
require_once __DIR__ . '/../includes/admin_functions.php';
requireAdminLogin();

if ($_POST) {
    $userId = $_POST['user_id'];
    $block = $_POST['block'];
    blockCustomer($userId, $block);
    header('Location: customers.php');
    exit;
}

$customers = getAllCustomers();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customers - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; }
        .header { background: #333; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 20px; }
        .header a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .card h2 { margin-bottom: 20px; color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; font-weight: bold; }
        tr:hover { background: #f8f9fa; }
        .btn { padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; }
        .btn-block { background: #dc3545; color: white; }
        .btn-unblock { background: #28a745; color: white; }
        .blocked { color: #dc3545; font-weight: bold; }
        .active { color: #28a745; font-weight: bold; }
        .back { display: inline-block; margin-bottom: 20px; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Panel</h1>
        <div>
            <a href="index.php">Dashboard</a>
            <a href="orders.php">Orders</a>
            <a href="customers.php">Customers</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <a href="index.php" class="back">&larr; Back to Dashboard</a>
        
        <div class="card">
            <h2>All Customers</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($customers as $customer): ?>
                <tr>
                    <td><?php echo $customer['id']; ?></td>
                    <td><?php echo $customer['name']; ?></td>
                    <td><?php echo $customer['email']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                    <td>
                        <?php if ($customer['is_blocked']): ?>
                            <span class="blocked">Blocked</span>
                        <?php else: ?>
                            <span class="active">Active</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $customer['id']; ?>">
                            <?php if ($customer['is_blocked']): ?>
                                <input type="hidden" name="block" value="0">
                                <button type="submit" class="btn btn-unblock">Unblock</button>
                            <?php else: ?>
                                <input type="hidden" name="block" value="1">
                                <button type="submit" class="btn btn-block">Block</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
</body>
</html>
