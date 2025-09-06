<?php
session_start();
require_once 'php/db_connect.php'; 
require_once 'php/config.php'; // Includes Stripe keys

// Security checks: User must be logged in and cart must not be empty.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    $_SESSION['message'] = "You must be logged in to checkout.";
    $_SESSION['msg_type'] = "warning";
    header('Location: login.php');
    exit;
}
if (empty($_SESSION['cart'])) {
    header('Location: marketplace.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_total = 0;
$cart_items_details = [];
$cart_product_ids = array_keys($_SESSION['cart']);

// Fetch user's address to pre-fill the form
$user_address = '';
$sql_user = "SELECT address FROM users WHERE user_id = ?";
$stmt_user = mysqli_prepare($conn, $sql_user);
mysqli_stmt_bind_param($stmt_user, "i", $user_id);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
if ($user_data = mysqli_fetch_assoc($result_user)) {
    $user_address = $user_data['address'];
}
mysqli_stmt_close($stmt_user);


// Fetch full product details for items in the cart
if (!empty($cart_product_ids)) {
    $ids_placeholders = implode(',', array_fill(0, count($cart_product_ids), '?'));
    $sql = "SELECT product_id, name, price, image_url FROM products WHERE product_id IN ($ids_placeholders)";
    $stmt = mysqli_prepare($conn, $sql);
    
    $types = str_repeat('i', count($cart_product_ids));
    mysqli_stmt_bind_param($stmt, $types, ...$cart_product_ids);
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($product = mysqli_fetch_assoc($result)) {
            $product_id = $product['product_id'];
            $cart_items_details[$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url'],
                'quantity' => $_SESSION['cart'][$product_id]['quantity']
            ];
            $cart_total += $product['price'] * $_SESSION['cart'][$product_id]['quantity'];
        }
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - VetSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-light-blue: #5AC8FA;
            --ios-green: #34C759;
            --ios-red: #FF3B30;
            --ios-gray: #8E8E93;
            --ios-light-gray: #F2F2F7;
            --ios-dark-gray: #1C1C1E;
            --ios-white: #FFFFFF;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.08);
            --shadow-medium: 0 4px 20px rgba(0,0,0,0.12);
            --blur-bg: rgba(255,255,255,0.8);
            --border-radius: 12px;
            --border-radius-lg: 20px;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background: var(--ios-light-gray);
            color: var(--ios-dark-gray);
        }
        .navbar {
            background: var(--blur-bg) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 0.5px solid rgba(0,0,0,0.1);
        }
        .navbar-brand { font-weight: 700; font-size: 1.5rem; color: var(--ios-blue) !important; }
        .nav-link { color: var(--ios-dark-gray) !important; font-weight: 500; border-radius: 8px; padding: 8px 16px !important; }
        .nav-link:hover, .nav-link.active { color: var(--ios-blue) !important; background: rgba(0,122,255,0.1); }
        .btn { border-radius: var(--border-radius); font-weight: 600; padding: 12px 24px; border: none; transition: all 0.3s ease; box-shadow: var(--shadow-light); }
        .btn:hover { transform: translateY(-2px); box-shadow: var(--shadow-medium); }
        .btn-primary { background: linear-gradient(135deg, var(--ios-blue), var(--ios-light-blue)); color: white; }
        .btn-success { background: linear-gradient(135deg, var(--ios-green), #30D158); color: white; }
        .page-header { padding: 60px 0; background: var(--ios-white); border-bottom: 1px solid #E5E5EA; }
        .page-header h1 { font-size: 3rem; font-weight: 700; }
        .card { border: none; border-radius: var(--border-radius-lg); background: var(--ios-white); box-shadow: var(--shadow-light); }
        .form-control { border-radius: var(--border-radius); border: 2px solid var(--ios-light-gray); padding: 12px 16px; }
        .form-control:focus { border-color: var(--ios-blue); box-shadow: 0 0 0 3px rgba(0,122,255,0.1); }
        footer { background: var(--ios-dark-gray) !important; color: var(--ios-white) !important; padding: 40px 0 !important; }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-heartbeat me-2"></i>VetSmart</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Cart <span class="badge bg-primary rounded-pill ms-1"><?php echo count($_SESSION['cart'] ?? []); ?></span></a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Page Header -->
    <header class="page-header text-center">
        <div class="container">
            <h1 class="display-4">Secure Checkout</h1>
            <p class="lead text-muted">Complete your purchase by providing your details below.</p>
        </div>
    </header>

    <!-- Main Checkout Content -->
    <main class="container py-5">
        <div class="row g-5">
            <!-- Checkout Form -->
            <div class="col-lg-7">
                <div class="card p-4">
                    <h4 class="fw-bold mb-4">Payment Details</h4>
                    <form id="payment-form">
                        <div class="mb-4">
                            <label for="address" class="form-label fw-bold">Shipping Address</label>
                            <textarea class="form-control" id="address" name="shipping_address" rows="3" required><?php echo htmlspecialchars($user_address); ?></textarea>
                        </div>
                        
                        <label class="form-label fw-bold">Payment Information</label>
                        <div id="payment-element" class="mb-3 p-3 border rounded">
                            <!-- Stripe Payment Element will be inserted here -->
                        </div>

                        <div id="payment-message" class="alert alert-danger" role="alert" style="display: none;"></div>

                        <button id="submit" class="btn btn-success w-100 btn-lg mt-3">
                            <span id="button-text">Pay Rs: <?php echo number_format($cart_total, 2); ?></span>
                            <span id="spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="display: none;"></span>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="card p-4 sticky-top" style="top: 120px;">
                    <h4 class="fw-bold mb-4">Your Order Summary</h4>
                    <?php foreach ($cart_items_details as $item): ?>
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid rounded me-3" style="width: 60px;">
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                        </div>
                        <span class="fw-bold">Rs: <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Subtotal
                            <span>Rs: <?php echo number_format($cart_total, 2); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Shipping
                            <span class="text-success">Free</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold fs-5 border-0">
                            Total
                            <span>Rs: <?php echo number_format($cart_total, 2); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center mt-5">
        <div class="container">
            <p class="mb-0"><i class="fas fa-copyright me-2"></i>2025 VetSmart Hospital. All Rights Reserved.</p>
        </div>
    </footer>
    
<script>
    const stripe = Stripe('<?php echo $stripe_publishable_key; ?>');
    const paymentForm = document.getElementById('payment-form');
    let elements;

    initialize();

    async function initialize() {
        try {
            const response = await fetch("php/create_payment_intent.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
            });
            const data = await response.json();

            if (!response.ok || !data.clientSecret) {
                showMessage(`Error: ${data.error || 'Could not initialize payment.'}`);
                return;
            }
            
            elements = stripe.elements({ clientSecret: data.clientSecret });
            const paymentElement = elements.create("payment");
            paymentElement.mount("#payment-element");

        } catch (error) {
            showMessage("Failed to connect to the payment server. Please try again later.");
        }
    }

    paymentForm.addEventListener("submit", async function(event) {
        event.preventDefault();
        setLoading(true);

        if (!elements) {
            showMessage("Payment form is not ready. Please wait a moment.");
            setLoading(false);
            return;
        }

        const shippingAddress = document.getElementById('address').value;
        const returnUrl = `${window.location.protocol}//${window.location.host}${window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'))}/php/process_order.php?address=${encodeURIComponent(shippingAddress)}`;

        const { error } = await stripe.confirmPayment({
            elements,
            confirmParams: { return_url: returnUrl },
        });

        if (error.type === "card_error" || error.type === "validation_error") {
            showMessage(error.message);
        } else {
            showMessage("An unexpected error occurred.");
        }
        setLoading(false);
    });

    function showMessage(messageText) {
        const messageContainer = document.querySelector("#payment-message");
        messageContainer.textContent = messageText;
        messageContainer.style.display = 'block';
    }

    function setLoading(isLoading) {
        document.querySelector("#submit").disabled = isLoading;
        document.querySelector("#spinner").style.display = isLoading ? "inline-block" : "none";
        document.querySelector("#button-text").style.display = isLoading ? "none" : "inline-block";
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
