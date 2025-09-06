<?php 
// Include the header, which handles session and security checks.
include 'includes/header.php'; 

// Fetch all orders with client information
$orders = [];
$sql = "SELECT o.order_id, o.order_date, o.total_amount, o.order_status, u.full_name 
        FROM orders o 
        JOIN users u ON o.client_id = u.user_id 
        ORDER BY o.order_date DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}

// Function to determine badge color based on status
function get_order_status_badge($status) {
    switch ($status) {
        case 'Processing': return 'badge bg-primary';
        case 'Shipped': return 'badge bg-info text-dark';
        case 'Delivered': return 'badge bg-success';
        case 'Cancelled': return 'badge bg-danger';
        default: return 'badge bg-secondary';
    }
}
?>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">Order Management</h1>
        <p class="lead text-muted">A complete log of all customer marketplace orders.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fas fa-shopping-cart me-2"></i>All Orders</h5>
            <div class="w-50">
                <input type="text" id="orderSearchInput" class="form-control" placeholder="Search by client name or order ID...">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="6" class="text-center text-muted p-5">No orders found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr class="order-row" data-search-term="<?php echo strtolower(htmlspecialchars($order['full_name'] . ' #' . $order['order_id'])); ?>">
                                <td><strong>#<?php echo $order['order_id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo date('F j, Y', strtotime($order['order_date'])); ?></td>
                                <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="text-center">
                                    <span class="<?php echo get_order_status_badge($order['order_status']); ?>">
                                        <?php echo htmlspecialchars($order['order_status']); ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="../php/process_admin_actions.php" method="POST" class="d-inline-flex align-items-center">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <select name="order_status" class="form-select form-select-sm" style="width: 120px;">
                                            <option value="Processing" <?php if($order['order_status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                            <option value="Shipped" <?php if($order['order_status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                            <option value="Delivered" <?php if($order['order_status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                            <option value="Cancelled" <?php if($order['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                        </select>
                                        <button type="submit" name="update_order_status" class="btn btn-sm btn-primary ms-2">Update</button>
                                        <a href="view_order_detail.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-info ms-2">View Details</a>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('orderSearchInput');
    const orderRows = document.querySelectorAll('#ordersTable tbody .order-row');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();
        orderRows.forEach(function(row) {
            const rowSearchTerm = row.getAttribute('data-search-term');
            if (rowSearchTerm.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>

