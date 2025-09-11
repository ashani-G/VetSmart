<?php
session_start();
require_once 'php/db_connect.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: marketplace.php');
    exit();
}
$product_id = intval($_GET['id']);

// Fetch the product from the database
$product = null;
$sql = "SELECT product_id, name, description, price, stock_quantity, image_url FROM products WHERE product_id = ? AND stock_quantity > 0";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$product) {
    header('Location: marketplace.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - VetSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-light-blue: #5AC8FA;
            --ios-green: #34C759;
            --ios-orange: #FF9500;
            --ios-red: #FF3B30;
            --ios-purple: #AF52DE;
            --ios-pink: #FF2D92;
            --ios-yellow: #FFCC00;
            --ios-gray: #8E8E93;
            --ios-light-gray: #F2F2F7;
            --ios-dark-gray: #1C1C1E;
            --ios-white: #FFFFFF;
            --ios-black: #000000;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-medium: 0 4px 20px rgba(0,0,0,0.15);
            --shadow-heavy: 0 8px 30px rgba(0,0,0,0.2);
            --blur-bg: rgba(255,255,255,0.8);
            --border-radius: 12px;
            --border-radius-lg: 20px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background: var(--ios-light-gray);
            color: var(--ios-dark-gray);
            line-height: 1.6;
        }

        .navbar {
            background: var(--blur-bg) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 0.5px solid rgba(0,0,0,0.1);
            box-shadow: var(--shadow-light);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--ios-blue) !important;
        }

        .nav-link {
            color: var(--ios-dark-gray) !important;
            font-weight: 500;
            border-radius: 8px;
            padding: 8px 16px !important;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--ios-blue) !important;
            background: rgba(0,122,255,0.1);
        }

        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 12px 24px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-light);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--ios-blue), var(--ios-light-blue));
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--ios-green), #30D158);
            color: white;
        }

        .btn-outline-success {
            border: 2px solid var(--ios-green);
            color: var(--ios-green);
        }

        .btn-outline-success:hover {
            background: var(--ios-green);
            color: white;
        }

        .btn-outline-danger {
            border: 2px solid var(--ios-red);
            color: var(--ios-red);
        }

        .btn-outline-danger:hover {
            background: var(--ios-red);
            color: white;
        }

        .product-image {
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-medium);
            overflow: hidden;
        }

        .product-details-card {
            background: var(--ios-white);
            border-radius: var(--border-radius-lg);
            padding: 2.5rem;
            box-shadow: var(--shadow-light);
        }

        .form-control {
            border-radius: var(--border-radius);
            padding: 12px 16px;
            border: 2px solid var(--ios-light-gray);
        }

        .form-control:focus {
            border-color: var(--ios-blue);
            box-shadow: 0 0 0 3px rgba(0,122,255,0.1);
        }

        footer {
            background: var(--ios-dark-gray) !important;
            color: var(--ios-white) !important;
            padding: 40px 0 !important;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-dog me-2"></i>VetSmart</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart"></i> Cart 
                            <span class="badge rounded-pill bg-success"><?php echo count($_SESSION['cart'] ?? []); ?></span>
                        </a>
                    </li>
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

    <!-- Product Detail Content -->
    <main class="container py-5">
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $_SESSION['message']; unset($_SESSION['message']); unset($_SESSION['msg_type']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="product-image">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="product-details-card">
                    <h1 class="display-5 fw-bold"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="fs-2 fw-bold" style="color: var(--ios-green);"><i class="fas fa-rupee-sign fa-xs me-1"></i><?php echo number_format($product['price'], 2); ?></p>
                    <p class="lead text-muted"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p><strong>Availability:</strong> <span class="badge bg-success bg-opacity-25 text-success-emphasis rounded-pill px-3 py-2"><?php echo $product['stock_quantity']; ?> in stock</span></p>
                    <hr class="my-4">
                    <form action="php/process_cart_actions.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <div class="row align-items-end g-3">
                            <div class="col-sm-5">
                                <label for="quantity" class="form-label fw-bold">Quantity</label>
                                <input type="number" class="form-control form-control-lg" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" required>
                            </div>
                            <div class="col-sm-7">
                                <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
             <a href="marketplace.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Marketplace</a>
        </div>
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
