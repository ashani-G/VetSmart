<?php
session_start();
require_once 'php/db_connect.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart_product_ids = array_keys($_SESSION['cart']);
$cart_items_details = [];
$cart_total = 0;

// Fetch full product details for items in the cart
if (!empty($cart_product_ids)) {
    $ids_placeholders = implode(',', array_fill(0, count($cart_product_ids), '?'));
    $sql = "SELECT product_id, name, price, image_url FROM products WHERE product_id IN ($ids_placeholders)";
    $stmt = mysqli_prepare($conn, $sql);
    
    // Dynamically bind parameters
    $types = str_repeat('i', count($cart_product_ids));
    mysqli_stmt_bind_param($stmt, $types, ...$cart_product_ids);
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        while ($product = mysqli_fetch_assoc($result)) {
            $product_id = $product['product_id'];
            // Combine DB data with session quantity
            $cart_items_details[$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url'],
                'quantity' => $_SESSION['cart'][$product_id]['quantity']
            ];
            // Calculate total
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
    <title>Your Cart - VetSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .btn-danger { background: var(--ios-red); color: white; }
        .btn-outline-danger { border: 2px solid var(--ios-red); color: var(--ios-red); background: transparent; }
        .btn-outline-danger:hover { background: var(--ios-red); color: white; }
        .page-header { padding: 60px 0; background: var(--ios-white); border-bottom: 1px solid #E5E5EA; }
        .page-header h1 { font-size: 3rem; font-weight: 700; }
        .card { border: none; border-radius: var(--border-radius-lg); background: var(--ios-white); box-shadow: var(--shadow-light); }
        footer { background: var(--ios-dark-gray) !important; color: var(--ios-white) !important; padding: 40px 0 !important; }
        .cart-item-row { border-bottom: 1px solid var(--ios-light-gray); }
        .cart-item-row:last-child { border-bottom: none; }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-dog me-2"></i>VetSmart</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link active" href="cart.php">Cart <span class="badge bg-primary rounded-pill ms-1"><?php echo count($_SESSION['cart'] ?? []); ?></span></a></li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <?php $dashboard_link = $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'client/dashboard.php'; ?>
                        <li class="nav-item"><a href="<?php echo $dashboard_link; ?>" class="btn btn-primary ms-lg-3">Dashboard</a></li>
                        <li class="nav-item"><a href="php/logout.php" class="btn btn-outline-danger ms-lg-2">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.php" class="btn btn-outline-success ms-lg-3">Login</a></li>
                        <li class="nav-item"><a href="register.php" class="btn btn-success ms-lg-2">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header text-center">
        <div class="container">
            <h1 class="display-4">Shopping Cart</h1>
            <p class="lead text-muted">Review your items and proceed to checkout.</p>
        </div>
    </header>

    <!-- Main Cart Content -->
    <main class="container py-5">
        <?php if (empty($cart_items_details)): ?>
            <div class="text-center card p-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3 class="fw-bold">Your cart is empty.</h3>
                <p class="text-muted">Looks like you haven't added any products yet.</p>
                <div class="mt-3">
                    <a href="marketplace.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-store me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-5">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card p-4">
                        <h4 class="mb-4 fw-bold">Your Items (<?php echo count($cart_items_details); ?>)</h4>
                        <?php foreach ($cart_items_details as $product_id => $item): ?>
                        <div class="row align-items-center py-3 cart-item-row">
                            <div class="col-md-2 col-3">
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-fluid rounded">
                            </div>
                            <div class="col-md-4 col-9">
                                <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-muted">Price: Rs: <?php echo number_format($item['price'], 2); ?></small>
                            </div>
                            <div class="col-md-3 col-6 mt-3 mt-md-0">
                                <form action="php/process_cart_actions.php" method="POST" class="d-flex">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <input type="number" name="quantity" class="form-control form-control-sm" value="<?php echo $item['quantity']; ?>" min="1" style="width: 70px;">
                                    <button type="submit" name="update_quantity" class="btn btn-sm btn-outline-primary ms-2">Update</button>
                                </form>
                            </div>
                            <div class="col-md-2 col-4 mt-3 mt-md-0 text-end">
                                <h6 class="mb-0">Rs: <?php echo number_format($item['price'] * $item['quantity'], 2); ?></h6>
                            </div>
                            <div class="col-md-1 col-2 mt-3 mt-md-0 text-end">
                                <a href="php/process_cart_actions.php?remove=<?php echo $product_id; ?>" class="btn btn-sm btn-outline-danger">&times;</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="card p-4 sticky-top" style="top: 120px;">
                        <h4 class="fw-bold mb-4">Order Summary</h4>
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
                        <a href="checkout.php" class="btn btn-success w-100 btn-lg mt-4">
                            <i class="fas fa-lock me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="text-center mt-5">
        <div class="container">
            <p class="mb-0"><i class="fas fa-copyright me-2"></i>2025 VetSmart Hospital. All Rights Reserved.</p>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
