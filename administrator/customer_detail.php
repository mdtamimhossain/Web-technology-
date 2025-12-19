<?php
/**
 * Customer Detail Page
 */
$pageTitle = 'Customer Details';
require_once __DIR__ . '/../includes/admin_functions.php';
require_once __DIR__ . '/../includes/admin_header.php';

$customerId = (int)($_GET['id'] ?? 0);
$customer = getCustomerById($customerId);

if (!$customer) {
    echo '<div class="alert alert-error">Customer not found</div>';
    require_once __DIR__ . '/../includes/admin_footer.php';
    exit;
}

$orders = getCustomerOrders($customerId);
?>

<div class="order-detail-header">
    <a href="customers.php" class="back-link"><i class="fa-solid fa-arrow-left"></i> Back to Customers</a>
    <?php if ($customer['is_blocked']): ?>
    <span class="status-badge status-blocked large">
        <i class="fa-solid fa-ban"></i> Blocked
    </span>
    <?php else: ?>
    <span class="status-badge status-active large">
        <i class="fa-solid fa-check"></i> Active
    </span>
    <?php endif; ?>
</div>

<div class="order-detail-grid">
    <!-- Customer Info -->
    <div class="admin-panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($customer['name']); ?></h2>
        </div>
        <div class="panel-body">
            <div class="info-grid">
                <div class="info-item">
                    <label>Email</label>
                    <span><?php echo htmlspecialchars($customer['email']); ?></span>
                </div>
                <div class="info-item">
                    <label>Phone</label>
                    <span><?php echo htmlspecialchars($customer['phone'] ?: 'Not provided'); ?></span>
                </div>
                <div class="info-item full-width">
                    <label>Address</label>
                    <span><?php echo nl2br(htmlspecialchars($customer['address'] ?: 'Not provided')); ?></span>
                </div>
                <div class="info-item">
                    <label>Member Since</label>
                    <span><?php echo date('F d, Y', strtotime($customer['created_at'])); ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats -->
    <div class="admin-panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-chart-bar"></i> Statistics</h2>
        </div>
        <div class="panel-body">
            <div class="customer-stats">
                <div class="stat-item">
                    <span class="stat-value"><?php echo $customer['order_count']; ?></span>
                    <span class="stat-label">Total Orders</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">$<?php echo number_format($customer['total_spent'] ?? 0, 2); ?></span>
                    <span class="stat-label">Total Spent</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Block/Unblock -->
    <div class="admin-panel">
        <div class="panel-header">
            <h2><i class="fa-solid fa-shield"></i> Account Status</h2>
        </div>
        <div class="panel-body">
            <?php if ($customer['is_blocked']): ?>
            <div class="blocked-info">
                <p class="text-danger"><i class="fa-solid fa-ban"></i> This customer is blocked</p>
                <?php if ($customer['blocked_reason']): ?>
                <p class="block-reason"><strong>Reason:</strong> <?php echo htmlspecialchars($customer['blocked_reason']); ?></p>
                <?php endif; ?>
                <button class="btn-success" onclick="unblockCustomer(<?php echo $customer['id']; ?>)">
                    <i class="fa-solid fa-user-check"></i> Unblock Customer
                </button>
            </div>
            <?php else: ?>
            <p class="text-success"><i class="fa-solid fa-check-circle"></i> Customer account is active</p>
            <button class="btn-danger" onclick="showBlockModal(<?php echo $customer['id']; ?>, '<?php echo htmlspecialchars($customer['name'], ENT_QUOTES); ?>')">
                <i class="fa-solid fa-user-slash"></i> Block Customer
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Customer Orders -->
<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fa-solid fa-box"></i> Order History</h2>
        <span class="count"><?php echo count($orders); ?> orders</span>
    </div>
    
    <?php if (empty($orders)): ?>
    <div class="empty-state">
        <i class="fa-solid fa-inbox"></i>
        <p>No orders from this customer</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($order['order_number']); ?></strong></td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-action view">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Block Modal -->
<div class="modal" id="blockModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fa-solid fa-user-slash"></i> Block Customer</h3>
            <button class="modal-close" onclick="closeBlockModal()">&times;</button>
        </div>
        <form id="blockForm" onsubmit="submitBlock(event)">
            <input type="hidden" id="blockCustomerId" name="customer_id">
            <p>Are you sure you want to block <strong id="blockCustomerName"></strong>?</p>
            <div class="form-group">
                <label for="blockReason">Reason (will be shown to customer):</label>
                <textarea id="blockReason" name="reason" rows="2"></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" onclick="closeBlockModal()">Cancel</button>
                <button type="submit" class="btn-danger">Block Customer</button>
            </div>
        </form>
    </div>
</div>

<script>
function showBlockModal(customerId, customerName) {
    document.getElementById('blockCustomerId').value = customerId;
    document.getElementById('blockCustomerName').textContent = customerName;
    document.getElementById('blockModal').classList.add('show');
}

function closeBlockModal() {
    document.getElementById('blockModal').classList.remove('show');
}

function submitBlock(e) {
    e.preventDefault();
    const customerId = document.getElementById('blockCustomerId').value;
    const reason = document.getElementById('blockReason').value;
    updateCustomerStatus(customerId, true, reason);
}

function unblockCustomer(customerId) {
    if (confirm('Unblock this customer?')) {
        updateCustomerStatus(customerId, false);
    }
}

function updateCustomerStatus(customerId, blocked, reason = '') {
    const formData = new FormData();
    formData.append('action', blocked ? 'block' : 'unblock');
    formData.append('customer_id', customerId);
    if (reason) formData.append('reason', reason);
    
    fetch('./../api/admin_customers.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating customer');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
