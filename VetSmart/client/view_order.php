<?php
// Include the header for security and connection
include 'includes/header.php';

// Check for order ID
if (!isset($_GET['order_id'])) {
    header('Location: my_orders.php');
    exit;
}

$order_id = intval($_GET['order_id']);
$client_id = $_SESSION['user_id'];

// Fetch main order details, ensuring it belongs to the logged-in user
$sql_order = "SELECT o.order_id, o.total_amount, o.shipping_address, o.order_date, o.order_status, u.full_name, u.email 
              FROM orders o
              JOIN users u ON o.client_id = u.user_id
              WHERE o.order_id = ? AND o.client_id = ?";
$stmt_order = mysqli_prepare($conn, $sql_order);
mysqli_stmt_bind_param($stmt_order, "ii", $order_id, $client_id);
mysqli_stmt_execute($stmt_order);
$result_order = mysqli_stmt_get_result($stmt_order);
$order = mysqli_fetch_assoc($result_order);

// If no order is found, redirect with a message
if (!$order) {
    $_SESSION['message'] = "Order not found or you do not have permission to view it.";
    $_SESSION['msg_type'] = "danger";
    header('Location: my_orders.php');
    exit;
}

// Fetch the items associated with this order
$order_items = [];
$sql_items = "SELECT oi.quantity, oi.price_per_item, p.name 
             FROM order_items oi
             JOIN products p ON oi.product_id = p.product_id
             WHERE oi.order_id = ?";
$stmt_items = mysqli_prepare($conn, $sql_items);
mysqli_stmt_bind_param($stmt_items, "i", $order_id);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);
if($result_items) {
    while($row = mysqli_fetch_assoc($result_items)) {
        $order_items[] = $row;
    }
}

// Function to determine badge color based on status (for consistency)
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
<div class="d-flex justify-content-between align-items-center mb-4 print-hide">
    <div>
        <h1 class="display-5 fw-bold mb-2">Order Details</h1>
        <p class="lead text-muted">A detailed summary of your order.</p>
    </div>
    <div>
        <a href="my_orders.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Orders</a>
        <button onclick="window.print();" class="btn btn-primary"><i class="fas fa-print me-2"></i>Print Receipt</button>
    </div>
</div>

<div class="card" id="receipt">
    <div class="card-body p-5">
        <!-- Receipt Header -->
        <div class="row mb-5">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary mb-2"><i class="fas fa-dog me-2"></i>VetSmart Hospital</h2>
                <p class="text-muted mb-0">123 Pet Street</p>
                <p class="text-muted mb-0">Kurunegala, Sri Lanka</p>
                <p class="text-muted mb-0">contact@vetsmart.com</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h1 class="display-6 fw-bold text-uppercase text-muted">Receipt</h1>
                <p class="mb-0"><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['order_id']); ?></p>
                <p class="mb-0"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
            </div>
        </div>

        <!-- Client & Shipping Info -->
        <div class="row mb-5">
            <div class="col-md-6">
                <h5 class="fw-bold">Billed To:</h5>
                <p class="mb-0"><?php echo htmlspecialchars($order['full_name']); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($order['email']); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="fw-bold">Shipping To:</h5>
                <address class="mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></address>
            </div>
        </div>

        <!-- Order Items Table -->
        <h5 class="fw-bold mb-3">Order Summary</h5>
        <div class="table-responsive">
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="py-2 ps-3">Item Description</th>
                        <th scope="col" class="py-2 text-center">Quantity</th>
                        <th scope="col" class="py-2 text-end">Unit Price</th>
                        <th scope="col" class="py-2 text-end pe-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal = 0;
                    foreach ($order_items as $item): 
                        $item_total = $item['price_per_item'] * $item['quantity'];
                        $subtotal += $item_total;
                    ?>
                        <tr>
                            <td class="py-3 ps-3"><?php echo htmlspecialchars($item['name']); ?></td>
                            <td class="py-3 text-center"><?php echo $item['quantity']; ?></td>
                            <td class="py-3 text-end">Rs. <?php echo number_format($item['price_per_item'], 2); ?></td>
                            <td class="py-3 text-end pe-3 fw-bold">Rs. <?php echo number_format($item_total, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Totals -->
        <div class="row mt-4 justify-content-end">
            <div class="col-md-5">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-2 text-muted">
                        <span>Subtotal</span>
                        <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 pb-2 text-muted">
                        <span>Shipping</span>
                        <span>Rs. <?php echo number_format($order['total_amount'] - $subtotal, 2); ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0 fw-bold fs-5 mt-2">
                        <span>Total Amount</span>
                        <span>Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        
        <hr class="my-5">
        
        <!-- Status & Notes -->
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold">Order Status</h5>
                <span class="<?php echo get_order_status_badge($order['order_status']); ?> fs-6">
                    <?php echo htmlspecialchars($order['order_status']); ?>
                </span>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="fw-bold">Thank you for your purchase!</h5>
                <p class="text-muted">VetSmart Hospital</p>
            </div>
        </div>
    </div>
</div>

<!-- CSS for printing -->
<style>
@media print {
    body {
        background-color: #fff;
    }
    .print-hide, #sidebar-wrapper, .navbar {
        display: none !important;
    }
    #page-content-wrapper {
        padding: 0 !important;
        margin: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
    a[href]:after {
        content: none !important;
    }
}
</style>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
