<?php
session_start();
require_once 'php/db_connect.php';

// Security check: User must be logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Check for order ID, typically from the URL after a successful payment
if (!isset($_GET['order_id'])) {
    // For demonstration, let's try to get the last order ID if one isn't specified
    $last_order_id_sql = "SELECT order_id FROM orders WHERE client_id = ? ORDER BY order_date DESC LIMIT 1";
    $stmt_last = mysqli_prepare($conn, $last_order_id_sql);
    mysqli_stmt_bind_param($stmt_last, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_last);
    $result_last = mysqli_stmt_get_result($stmt_last);
    if ($last_order = mysqli_fetch_assoc($result_last)) {
        $order_id = $last_order['order_id'];
    } else {
        die("No order specified or found.");
    }
} else {
    $order_id = intval($_GET['order_id']);
}

$client_id = $_SESSION['user_id'];

// Fetch main order details, ensuring it belongs to the logged-in user
$sql_order = "SELECT o.order_id, o.total_amount, o.shipping_address, o.order_date, u.full_name, u.email
              FROM orders o
              JOIN users u ON o.client_id = u.user_id
              WHERE o.order_id = ? AND o.client_id = ?";
$stmt_order = mysqli_prepare($conn, $sql_order);
mysqli_stmt_bind_param($stmt_order, "ii", $order_id, $client_id);
mysqli_stmt_execute($stmt_order);
$result_order = mysqli_stmt_get_result($stmt_order);
$order = mysqli_fetch_assoc($result_order);

if (!$order) {
    die("Order not found or you do not have permission to view it.");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful - VetSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jsPDF for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-green: #34C759;
            --ios-light-gray: #F2F2F7;
            --ios-dark-gray: #1C1C1E;
            --ios-white: #FFFFFF;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.08);
            --border-radius-lg: 20px;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background: var(--ios-light-gray);
            color: var(--ios-dark-gray);
        }
        .btn { border-radius: 12px; font-weight: 600; padding: 12px 24px; border: none; transition: all 0.3s ease; }
        .btn-primary { background-color: var(--ios-blue); color: white; }
        .btn-outline-primary { border: 2px solid var(--ios-blue); color: var(--ios-blue); background-color: transparent; }
        .btn-outline-primary:hover { background-color: var(--ios-blue); color: white; }
        .card { border: none; border-radius: var(--border-radius-lg); background: var(--ios-white); box-shadow: var(--shadow-light); }
        .receipt-header { border-bottom: 1px solid var(--ios-light-gray); }

        @media print {
            body { background: var(--ios-white); }
            .no-print { display: none; }
            .card { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-md-11">
                <div class="text-center mb-5 no-print">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h1 class="display-5 fw-bold">Thank You For Your Order!</h1>
                    <p class="lead text-muted">Your payment was successful. Your order receipt is ready below.</p>
                </div>
                
                <div class="card my-4" id="receipt">
                    <div class="card-body p-sm-5 p-4">
                        <!-- Receipt Header -->
                        <div class="receipt-header pb-4 mb-4">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h2 class="fw-bold text-primary mb-1"><i class="fas fa-heartbeat me-2"></i>VetSmart</h2>
                                    <p class="text-muted mb-0">123 Pet Street, Kurunegala, Sri Lanka</p>
                                    <p class="text-muted mb-0">contact@vetsmart.com | (123) 456-7890</p>
                                </div>
                                <div class="text-end">
                                    <h3 class="fw-bold">RECEIPT</h3>
                                    <p class="mb-1"><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['order_id']); ?></p>
                                    <p class="mb-0"><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Customer & Shipping Info -->
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="fw-bold">BILLED TO:</h6>
                                <p class="mb-0"><?php echo htmlspecialchars($order['full_name']); ?></p>
                                <p class="mb-0"><?php echo htmlspecialchars($order['email']); ?></p>
                            </div>
                            <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                                <h6 class="fw-bold">SHIPPING TO:</h6>
                                <p class="mb-0"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">ITEM</th>
                                        <th scope="col" class="text-center">QTY</th>
                                        <th scope="col" class="text-end">UNIT PRICE</th>
                                        <th scope="col" class="text-end">TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                                        <td class="text-end">Rs: <?php echo number_format($item['price_per_item'], 2); ?></td>
                                        <td class="text-end">Rs: <?php echo number_format($item['price_per_item'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Section -->
                        <div class="row justify-content-end mt-4">
                            <div class="col-sm-5">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Subtotal</span>
                                        <span>Rs: <?php echo number_format($order['total_amount'], 2); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Shipping</span>
                                        <span class="text-success">Free</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between fw-bold fs-5 border-0">
                                        <span>TOTAL</span>
                                        <span>Rs: <?php echo number_format($order['total_amount'], 2); ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Footer Note -->
                        <div class="text-center text-muted mt-5">
                            <p>Thank you for choosing VetSmart for your pet's needs. We appreciate your business!</p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4 no-print">
                    <button onclick="downloadPDF()" class="btn btn-primary"><i class="fas fa-download me-2"></i>Download PDF</button>
                    <button onclick="window.print()" class="btn btn-outline-primary"><i class="fas fa-print me-2"></i>Print Receipt</button>
                    <a href="client/dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Go to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

<script>
    // Function to download the receipt as a PDF
    function downloadPDF() {
        const receiptElement = document.getElementById('receipt');
        const { jsPDF } = window.jspdf;
        
        html2canvas(receiptElement, { scale: 2 }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'pt',
                format: 'a4'
            });
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
            pdf.save("VetSmart-Receipt-<?php echo $order_id; ?>.pdf");
        });
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
