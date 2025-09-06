<?php
// Include the header for security, connection, and admin checks
include 'includes/header.php';

// Check if order_id is provided in the URL
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo "<div class='alert alert-danger'>No order ID specified.</div>";
    include 'includes/footer.php';
    exit();
}
$order_id = intval($_GET['order_id']);

// --- Fetch Order Details ---
// 1. Fetch main order information along with client details
$order_details = null;
$sql_order = "SELECT 
                o.order_id, o.order_date, o.total_amount, o.order_status, o.shipping_address,
                u.full_name, u.email, u.phone_number
              FROM orders o
              JOIN users u ON o.client_id = u.user_id
              WHERE o.order_id = ?";
$stmt_order = mysqli_prepare($conn, $sql_order);
mysqli_stmt_bind_param($stmt_order, "i", $order_id);
mysqli_stmt_execute($stmt_order);
$result_order = mysqli_stmt_get_result($stmt_order);
$order = mysqli_fetch_assoc($result_order);

// If no order is found, show an error
if (!$order) {
    echo "<div class='alert alert-danger'>Order not found.</div>";
    include 'includes/footer.php';
    exit();
}

// 2. Fetch all items for this order
$order_items = [];
$sql_items = "SELECT p.name, oi.quantity, oi.price_per_item
              FROM order_items oi
              JOIN products p ON oi.product_id = p.product_id
              WHERE oi.order_id = ?";
$stmt_items = mysqli_prepare($conn, $sql_items);
mysqli_stmt_bind_param($stmt_items, "i", $order_id);
mysqli_stmt_execute($stmt_items);
$result_items = mysqli_stmt_get_result($stmt_items);
if ($result_items) {
    while ($row = mysqli_fetch_assoc($result_items)) {
        $order_items[] = $row;
    }
}
?>
<!-- Print-friendly styles -->
<style>
    @media print {
        body * { visibility: hidden; }
        .printable-area, .printable-area * { visibility: visible; }
        .printable-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none; }
        #sidebar-wrapper, .navbar, .page-title-header { display: none !important; }
        #page-content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .container-fluid { padding: 0 !important; }
    }
</style>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4 page-title-header">
    <div>
        <h1 class="display-5 fw-bold mb-2">Order Details</h1>
        <p class="lead text-muted">A detailed summary of order #<?php echo htmlspecialchars($order['order_id']); ?>.</p>
    </div>
    <div class="no-print">
        <a href="orders.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Orders</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-2"></i>Print Invoice</button>
    </div>
</div>

<div class="card printable-area">
    <div class="card-body p-5">
        <!-- Header -->
        <div class="row mb-5">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary"><i class="fas fa-dogbeat me-2"></i>VetSmart Clinic</h2>
                <p class="text-muted">123 Pet Street, Kurunegala, Sri Lanka<br>contact@vetsmart.com | (123) 456-7890</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h1 class="display-4 fw-bold text-muted">INVOICE</h1>
                <p class="fw-bold mb-1">Order #<?php echo htmlspecialchars($order['order_id']); ?></p>
                <p class="text-muted">Date: <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
            </div>
        </div>

        <!-- Client & Shipping Details -->
        <div class="row border-top pt-4 mb-5">
            <div class="col-md-6">
                <h5 class="fw-bold">BILLED TO:</h5>
                <p class="mb-1"><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></p>
                <p class="mb-1"><?php echo htmlspecialchars($order['email']); ?></p>
                <p class="mb-1"><?php echo htmlspecialchars($order['phone_number']); ?></p>
            </div>
            <div class="col-md-6 text-md-end">
                <h5 class="fw-bold">SHIPPING TO:</h5>
                <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="table-responsive">
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Product Description</th>
                        <th scope="col" class="text-center">Qty</th>
                        <th scope="col" class="text-end">Unit Price</th>
                        <th scope="col" class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $subtotal = 0;
                    $item_index = 1;
                    foreach ($order_items as $item): 
                        $item_subtotal = $item['price_per_item'] * $item['quantity'];
                        $subtotal += $item_subtotal;
                    ?>
                    <tr>
                        <th scope="row"><?php echo $item_index++; ?></th>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                        <td class="text-end">Rs. <?php echo number_format($item['price_per_item'], 2); ?></td>
                        <td class="text-end">Rs. <?php echo number_format($item_subtotal, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="row mt-4 justify-content-end">
            <div class="col-md-5">
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <th class="border-0">Subtotal</th>
                            <td class="text-end border-0">Rs. <?php echo number_format($subtotal, 2); ?></td>
                        </tr>
                        <tr>
                            <th class="border-0">Shipping & Handling</th>
                            <td class="text-end border-0">Rs. <?php echo number_format($order['total_amount'] - $subtotal, 2); ?></td>
                        </tr>
                        <tr class="bg-light">
                            <th class="fw-bold fs-5">Total Amount</th>
                            <td class="fw-bold fs-5 text-end">Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                        </tr>
                         <tr>
                            <th class="border-0">Payment Status</th>
                            <td class="text-end border-0 fw-bold"><?php echo htmlspecialchars($order['order_status']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Footer Notes -->
        <div class="row border-top mt-5 pt-4">
            <div class="col text-center text-muted">
                <p>Thank you for your business! If you have any questions about this invoice, please contact us.</p>
            </div>
        </div>
    </div>
</div>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
