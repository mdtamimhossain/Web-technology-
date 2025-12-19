<?php
/**
 * Admin Dashboard
 */
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/admin_functions.php';
require_once __DIR__ . '/../includes/admin_header.php';

$stats = getOrderStats();
$newOrders = getOrdersByStatus('pending', 10);
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card pending">
        <div class="stat-icon"><i class="fa-regular fa-clock"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['pending']; ?></h3>
            <p>New Orders</p>
        </div>
        <a href="orders.php?status=pending" class="stat-link">View <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <div class="stat-card processing">
        <div class="stat-icon"><i class="fa-solid fa-gears"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['processing']; ?></h3>
            <p>Processing</p>
        </div>
        <a href="orders.php?status=processing" class="stat-link">View <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <div class="stat-card shipped">
        <div class="stat-icon"><i class="fa-solid fa-truck"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['shipped']; ?></h3>
            <p>Shipped</p>
        </div>
        <a href="orders.php?status=shipped" class="stat-link">View <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <div class="stat-card delivered">
        <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['delivered']; ?></h3>
            <p>Delivered</p>
        </div>
        <a href="orders.php?status=delivered" class="stat-link">View <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <div class="stat-card rejected">
        <div class="stat-icon"><i class="fa-solid fa-ban"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['rejected'] + $stats['cancelled']; ?></h3>
            <p>Rejected/Cancelled</p>
        </div>
        <a href="orders.php?status=rejected" class="stat-link">View <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <div class="stat-card revenue">
        <div class="stat-icon"><i class="fa-solid fa-dollar-sign"></i></div>
        <div class="stat-info">
            <h3>$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
            <p>Total Revenue</p>
        </div>
    </div>
    
    <div class="stat-card customers">
        <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['total_customers']; ?></h3>
            <p>Total Customers</p>
        </div>
        <a href="customers.php" class="stat-link">View <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    
    <div class="stat-card blocked">
        <div class="stat-icon"><i class="fa-solid fa-user-slash"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['blocked_customers']; ?></h3>
            <p>Blocked Users</p>
        </div>
        <a href="customers.php?blocked=1" class="stat-link">View <i class="fa-solid fa-arrow-right"></i></a>
    </div>
</div>

<!-- Recent New Orders -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fa-regular fa-clock"></i> New Orders</h2>
        <a href="orders.php?status=pending" class="btn-sm">View All</a>
    </div>
    
    <?php if (empty($newOrders)): ?>
    <div class="empty-state">
        <i class="fa-solid fa-inbox"></i>
        <p>No new orders at the moment</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($newOrders as $order): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                    <td>
                        <?php echo htmlspecialchars($order['customer_name']); ?>
                        <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                    </td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                    <td>
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-action view">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <button class="btn-action process" onclick="processOrder(<?php echo $order['id']; ?>)">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        <button class="btn-action reject" onclick="showRejectModal(<?php echo $order['id']; ?>)">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Reject Modal -->
<div class="modal" id="rejectModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-ban"></i> Reject Order</h3>
            <button class="modal-close" onclick="closeRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" onsubmit="submitReject(event)">
            <input type="hidden" id="rejectOrderId" name="order_id">
            <div class="form-group">
                <label for="rejectReason">Rejection Reason (will be shown to customer):</label>
                <textarea id="rejectReason" name="reason" required rows="3" 
                          placeholder="e.g., Products not available, Unable to ship to your location..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeRejectModal()">Cancel</button>
                <button type="submit" class="btn-danger">Reject Order</button>
            </div>
        </form>
    </div>
</div>

<script>
function processOrder(orderId) {
    if (confirm('Process this order? It will be marked as "Processing".')) {
        updateOrderStatus(orderId, 'processing');
    }
}

function showRejectModal(orderId) {
    document.getElementById('rejectOrderId').value = orderId;
    document.getElementById('rejectModal').classList.add('show');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('show');
    document.getElementById('rejectForm').reset();
}

function submitReject(e) {
    e.preventDefault();
    const orderId = document.getElementById('rejectOrderId').value;
    const reason = document.getElementById('rejectReason').value;
    updateOrderStatus(orderId, 'rejected', reason);
    closeRejectModal();
}

function updateOrderStatus(orderId, status, reason = '') {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('order_id', orderId);
    formData.append('status', status);
    if (reason) formData.append('reason', reason);
    
    fetch('./../api/admin_orders.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating order');
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
