<?php
/**
 * Admin Orders Management
 */
$pageTitle = 'Orders';
require_once __DIR__ . '/../includes/admin_functions.php';
require_once __DIR__ . '/../includes/admin_header.php';

$status = $_GET['status'] ?? '';
$orders = getOrdersByStatus($status ?: null, 100);

$statusLabels = [
    '' => 'All Orders',
    'pending' => 'New Orders',
    'processing' => 'Processing',
    'shipped' => 'Shipped',
    'delivered' => 'Delivered',
    'rejected' => 'Rejected',
    'cancelled' => 'Cancelled'
];

$pageTitle = $statusLabels[$status] ?? 'Orders';
?>

<div class="orders-filters">
    <div class="filter-tabs">
        <a href="orders.php" class="<?php echo $status === '' ? 'active' : ''; ?>">All</a>
        <a href="orders.php?status=pending" class="<?php echo $status === 'pending' ? 'active' : ''; ?>">
            New <span class="badge"><?php echo count(getOrdersByStatus('pending')); ?></span>
        </a>
        <a href="orders.php?status=processing" class="<?php echo $status === 'processing' ? 'active' : ''; ?>">Processing</a>
        <a href="orders.php?status=shipped" class="<?php echo $status === 'shipped' ? 'active' : ''; ?>">Shipped</a>
        <a href="orders.php?status=delivered" class="<?php echo $status === 'delivered' ? 'active' : ''; ?>">Delivered</a>
        <a href="orders.php?status=rejected" class="<?php echo $status === 'rejected' ? 'active' : ''; ?>">Rejected</a>
    </div>
</div>

<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fa-solid fa-box"></i> <?php echo $statusLabels[$status] ?? 'All Orders'; ?></h2>
        <span class="order-count"><?php echo count($orders); ?> orders</span>
    </div>
    
    <?php if (empty($orders)): ?>
    <div class="empty-state">
        <i class="fa-solid fa-inbox"></i>
        <p>No orders found</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td>
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="order-link">
                            <strong><?php echo htmlspecialchars($order['order_number']); ?></strong>
                        </a>
                    </td>
                    <td>
                        <div class="customer-info">
                            <?php echo htmlspecialchars($order['customer_name']); ?>
                            <small><?php echo htmlspecialchars($order['customer_email']); ?></small>
                        </div>
                    </td>
                    <td>
                        <?php 
                        $pdo = getDBConnection();
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
                        $stmt->execute([$order['id']]);
                        echo $stmt->fetchColumn();
                        ?> items
                    </td>
                    <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                    <td>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td class="actions-cell">
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-action view" title="View Details">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        
                        <?php if ($order['status'] === 'pending'): ?>
                        <button class="btn-action process" onclick="processOrder(<?php echo $order['id']; ?>)" title="Process Order">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        <button class="btn-action reject" onclick="showRejectModal(<?php echo $order['id']; ?>)" title="Reject Order">
                            <i class="fa-solid fa-times"></i>
                        </button>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] === 'processing'): ?>
                        <button class="btn-action ship" onclick="shipOrder(<?php echo $order['id']; ?>)" title="Mark as Shipped">
                            <i class="fa-solid fa-truck"></i>
                        </button>
                        <?php endif; ?>
                        
                        <?php if ($order['status'] === 'shipped'): ?>
                        <button class="btn-action deliver" onclick="deliverOrder(<?php echo $order['id']; ?>)" title="Mark as Delivered">
                            <i class="fa-solid fa-circle-check"></i>
                        </button>
                        <?php endif; ?>
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

function shipOrder(orderId) {
    if (confirm('Mark this order as shipped?')) {
        updateOrderStatus(orderId, 'shipped');
    }
}

function deliverOrder(orderId) {
    if (confirm('Mark this order as delivered?')) {
        updateOrderStatus(orderId, 'delivered');
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
