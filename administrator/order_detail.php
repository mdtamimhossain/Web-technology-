<?php
/**
 * Order Detail Page
 */
$pageTitle = 'Order Details';
require_once __DIR__ . '/../includes/admin_functions.php';
require_once __DIR__ . '/../includes/admin_header.php';

$orderId = (int)($_GET['id'] ?? 0);
$order = getOrderById($orderId);

if (!$order) {
    echo '<div class="alert alert-error">Order not found</div>';
    require_once __DIR__ . '/../includes/admin_footer.php';
    exit;
}

$pageTitle = 'Order #' . $order['order_number'];
?>

<div class="order-detail-header">
    <a href="orders.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Orders</a>
    <span class="status-badge status-<?php echo $order['status']; ?> large">
        <?php echo ucfirst($order['status']); ?>
    </span>
</div>

<div class="order-detail-grid">
    <!-- Order Info -->
    <div class="admin-panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-receipt"></i> Order #<?php echo htmlspecialchars($order['order_number']); ?></h2>
        </div>
        <div class="panel-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Order Date</label>
                    <span><?php echo date('F d, Y H:i', strtotime($order['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
                <?php if ($order['shipped_at']): ?>
                <div class="info-item">
                    <label>Shipped Date</label>
                    <span><?php echo date('F d, Y H:i', strtotime($order['shipped_at'])); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order['delivered_at']): ?>
                <div class="info-item">
                    <label>Delivered Date</label>
                    <span><?php echo date('F d, Y H:i', strtotime($order['delivered_at'])); ?></span>
                </div>
                <?php endif; ?>
                <?php if ($order['rejection_reason']): ?>
                <div class="info-item full-width">
                    <label>Rejection Reason</label>
                    <span class="rejection-reason"><?php echo htmlspecialchars($order['rejection_reason']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Customer Info -->
    <div class="admin-panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-user"></i> Customer</h2>
            <?php if ($order['customer_blocked']): ?>
            <span class="badge badge-danger">Blocked</span>
            <?php endif; ?>
        </div>
        <div class="panel-body">
            <div class="customer-detail">
                <p><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                <p><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                <?php if ($order['customer_phone']): ?>
                <p><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                <?php endif; ?>
            </div>
            <a href="customer_detail.php?id=<?php echo $order['user_id']; ?>" class="btn-sm">View Customer</a>
        </div>
    </div>
    
    <!-- Shipping Info -->
    <div class="admin-panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-truck"></i> Shipping</h2>
        </div>
        <div class="panel-body">
            <div class="shipping-detail">
                <p><strong><?php echo htmlspecialchars($order['shipping_name'] ?: $order['customer_name']); ?></strong></p>
                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'] ?: $order['customer_address'] ?: 'No address provided')); ?></p>
                <?php if ($order['shipping_phone']): ?>
                <p><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Order Actions -->
    <div class="admin-panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-cogs"></i> Actions</h2>
        </div>
        <div class="panel-body">
            <div class="action-buttons">
                <?php if ($order['status'] === 'pending'): ?>
                <button class="btn-primary" onclick="processOrder(<?php echo $order['id']; ?>)">
                    <i class="fa-solid fa-check"></i> Process Order
                </button>
                <button class="btn-danger" onclick="showRejectModal(<?php echo $order['id']; ?>)">
                    <i class="fa-solid fa-ban"></i> Reject Order
                </button>
                <?php endif; ?>
                
                <?php if ($order['status'] === 'processing'): ?>
                <button class="btn-primary" onclick="shipOrder(<?php echo $order['id']; ?>)">
                    <i class="fa-solid fa-truck"></i> Mark as Shipped
                </button>
                <?php endif; ?>
                
                <?php if ($order['status'] === 'shipped'): ?>
                <button class="btn-success" onclick="deliverOrder(<?php echo $order['id']; ?>)">
                    <i class="fa-solid fa-circle-check"></i> Mark as Delivered
                </button>
                <?php endif; ?>
                
                <?php if (in_array($order['status'], ['delivered', 'cancelled', 'rejected'])): ?>
                <p class="text-muted">No actions available for this order.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fa-solid fa-shopping-bag"></i> Order Items</h2>
    </div>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($item['product_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($item['size'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($item['color'] ?: '-'); ?></td>
                    <td>$<?php echo number_format($item['product_price'], 2); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><strong>$<?php echo number_format($item['subtotal'], 2); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">Subtotal:</td>
                    <td><strong>$<?php echo number_format($order['subtotal'], 2); ?></strong></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Tax (19%):</td>
                    <td><strong>$<?php echo number_format($order['tax_amount'], 2); ?></strong></td>
                </tr>
                <tr class="total-row">
                    <td colspan="5" class="text-right">Total:</td>
                    <td><strong>$<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Notes -->
<?php if ($order['notes'] || $order['admin_notes']): ?>
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fa-solid fa-sticky-note"></i> Notes</h2>
    </div>
    <div class="panel-body">
        <?php if ($order['notes']): ?>
        <div class="note-section">
            <h4>Customer Notes:</h4>
            <p><?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
        </div>
        <?php endif; ?>
        <?php if ($order['admin_notes']): ?>
        <div class="note-section">
            <h4>Admin Notes:</h4>
            <p><?php echo nl2br(htmlspecialchars($order['admin_notes'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Reject Modal -->
<div class="modal" id="rejectModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-ban"></i> Reject Order</h3>
            <button class="modal-close" onclick="closeRejectModal()">&times;</button>
        </div>
        <form id="rejectForm" onsubmit="submitReject(event)">
            <input type="hidden" id="rejectOrderId" value="<?php echo $order['id']; ?>">
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
    if (confirm('Process this order?')) {
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

function showRejectModal() {
    document.getElementById('rejectModal').classList.add('show');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.remove('show');
}

function submitReject(e) {
    e.preventDefault();
    const orderId = document.getElementById('rejectOrderId').value;
    const reason = document.getElementById('rejectReason').value;
    updateOrderStatus(orderId, 'rejected', reason);
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
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
