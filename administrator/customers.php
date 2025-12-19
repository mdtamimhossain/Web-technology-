<?php
/**
 * Customer Management Page
 */
$pageTitle = 'Customers';
require_once __DIR__ . '/../includes/admin_functions.php';
require_once __DIR__ . '/../includes/admin_header.php';

$search = $_GET['search'] ?? '';
$blockedOnly = isset($_GET['blocked']);
$customers = getAllCustomers($search, $blockedOnly);
?>

<div class="customers-header">
    <form class="search-form" method="GET">
        <div class="search-input">
            <i class="fa-solid fa-search"></i>
            <input type="text" name="search" placeholder="Search customers..." 
                   value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <button type="submit" class="btn-primary">Search</button>
        <?php if ($search || $blockedOnly): ?>
        <a href="customers.php" class="btn-secondary">Clear</a>
        <?php endif; ?>
    </form>
    
    <div class="filter-buttons">
        <a href="customers.php" class="btn-filter <?php echo !$blockedOnly ? 'active' : ''; ?>">All Customers</a>
        <a href="customers.php?blocked=1" class="btn-filter <?php echo $blockedOnly ? 'active' : ''; ?>">
            <i class="fa-solid fa-user-slash"></i> Blocked Only
        </a>
    </div>
</div>

<div class="admin-panel">
    <div class="panel-header">
        <h2><i class="fa-solid fa-users"></i> Customers</h2>
        <span class="count"><?php echo count($customers); ?> customers</span>
    </div>
    
    <?php if (empty($customers)): ?>
    <div class="empty-state">
        <i class="fa-solid fa-users-slash"></i>
        <p>No customers found</p>
    </div>
    <?php else: ?>
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                <tr class="<?php echo $customer['is_blocked'] ? 'blocked-row' : ''; ?>">
                    <td>#<?php echo $customer['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($customer['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($customer['email']); ?></td>
                    <td><?php echo htmlspecialchars($customer['phone'] ?: '-'); ?></td>
                    <td><?php echo $customer['order_count']; ?></td>
                    <td>$<?php echo number_format($customer['total_spent'] ?? 0, 2); ?></td>
                    <td>
                        <?php if ($customer['is_blocked']): ?>
                        <span class="status-badge status-blocked">
                            <i class="fa-solid fa-ban"></i> Blocked
                        </span>
                        <?php else: ?>
                        <span class="status-badge status-active">
                            <i class="fa-solid fa-check"></i> Active
                        </span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($customer['created_at'])); ?></td>
                    <td class="actions-cell">
                        <a href="customer_detail.php?id=<?php echo $customer['id']; ?>" class="btn-action view" title="View Details">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        <?php if ($customer['is_blocked']): ?>
                        <button class="btn-action unblock" onclick="unblockCustomer(<?php echo $customer['id']; ?>)" title="Unblock Customer">
                            <i class="fa-solid fa-user-check"></i>
                        </button>
                        <?php else: ?>
                        <button class="btn-action block" onclick="showBlockModal(<?php echo $customer['id']; ?>, '<?php echo htmlspecialchars($customer['name'], ENT_QUOTES); ?>')" title="Block Customer">
                            <i class="fa-solid fa-user-slash"></i>
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
            <p class="text-muted">Blocked customers cannot place new orders.</p>
            <div class="form-group">
                <label for="blockReason">Reason (optional, will be shown to customer):</label>
                <textarea id="blockReason" name="reason" rows="2" 
                          placeholder="e.g., Fraudulent activity, Payment issues..."></textarea>
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
    document.getElementById('blockForm').reset();
}

function submitBlock(e) {
    e.preventDefault();
    const customerId = document.getElementById('blockCustomerId').value;
    const reason = document.getElementById('blockReason').value;
    updateCustomerStatus(customerId, true, reason);
    closeBlockModal();
}

function unblockCustomer(customerId) {
    if (confirm('Unblock this customer? They will be able to place orders again.')) {
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
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating customer');
    });
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>
