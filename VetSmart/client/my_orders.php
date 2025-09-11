<?php 
// Include the header, which also starts the session and checks for login
include 'includes/header.php'; 

// Fetch all orders for the logged-in user
$client_id = $_SESSION['user_id'];
$orders = [];

$sql = "SELECT order_id, order_date, total_amount, order_status FROM orders WHERE client_id = ? ORDER BY order_date DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $client_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
mysqli_stmt_close($stmt);

// Function to determine badge color based on status
function get_order_status_badge($status) {
    switch ($status) {
        case 'Processing': return 'badge rounded-pill bg-primary';
        case 'Shipped': return 'badge rounded-pill bg-warning text-dark';
        case 'Delivered': return 'badge rounded-pill bg-success';
        case 'Cancelled': return 'badge rounded-pill bg-danger';
        default: return 'badge rounded-pill bg-secondary'; // Pending
    }
}
?>

<!-- Main Page Content -->
<h1 class="display-5 fw-bold mb-2">My Orders</h1>
<p class="lead text-muted mb-4">Here is a list of all your past and current marketplace orders.</p>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($orders)): ?>
            <div class="text-center p-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <h4 class="fw-bold">No Orders Yet</h4>
                <p class="text-muted">You haven't placed any orders. When you do, they will appear here.</p>
                <a href="../marketplace.php" class="btn btn-primary mt-3">
                    <i class="fas fa-store me-2"></i>Visit the Marketplace
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="py-3 ps-4">Order ID</th>
                            <th scope="col" class="py-3">Date</th>
                            <th scope="col" class="py-3">Total Amount</th>
                            <th scope="col" class="py-3">Status</th>
                            <th scope="col" class="py-3 pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="py-3 ps-4 fw-bold">#<?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td class="py-3"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></td>
                                <td class="py-3">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="py-3">
                                    <span class="<?php echo get_order_status_badge($order['order_status']); ?>">
                                        <?php echo htmlspecialchars($order['order_status']); ?>
                                    </span>
                                </td>
                                <td class="py-3 pe-4">
                                    <a href="view_order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
