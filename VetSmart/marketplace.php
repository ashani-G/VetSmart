<?php
session_start();
require_once 'php/db_connect.php';

// Fetch all products from the database
$products = [];
$sql = "SELECT product_id, name, description, price, image_url FROM products WHERE stock_quantity > 0 ORDER BY name ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace - VetSmart</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background: var(--ios-light-gray);
            color: var(--ios-dark-gray);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* iOS-style Glassmorphism Navigation */
        .navbar {
            background: var(--blur-bg) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 0.5px solid rgba(0,0,0,0.1);
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--ios-blue) !important;
            text-decoration: none;
        }

        .nav-link {
            color: var(--ios-dark-gray) !important;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            border-radius: 8px;
            padding: 8px 16px !important;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--ios-blue) !important;
            background: rgba(0,122,255,0.1);
            transform: translateY(-1px);
        }
        
        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 10px 20px;
            border: none;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-light);
            position: relative;
            overflow: hidden;
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
            background: transparent;
        }

        .btn-outline-success:hover {
            background: var(--ios-green);
            color: white;
        }

        .btn-outline-danger {
            border: 2px solid var(--ios-red);
            color: var(--ios-red);
            background: transparent;
        }

        .btn-outline-danger:hover {
            background: var(--ios-red);
            color: white;
        }

        /* Page Header */
        .page-header {
            padding: 60px 0;
            background: var(--ios-white);
            border-bottom: 1px solid #E5E5EA;
        }
        
        .page-header h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--ios-dark-gray);
        }

        .page-header p {
            font-size: 1.2rem;
            color: var(--ios-gray);
        }

        /* iOS-style Cards */
        .card {
            border: none;
            border-radius: var(--border-radius-lg);
            background: var(--ios-white);
            box-shadow: var(--shadow-light);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .card-img-top {
            transition: transform 0.3s ease;
            height: 200px;
            object-fit: cover;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-weight: 600;
            color: var(--ios-dark-gray);
            margin-bottom: 0.5rem;
        }
        
        /* Footer */
        footer {
            background: var(--ios-dark-gray) !important;
            color: var(--ios-white) !important;
            padding: 40px 0 !important;
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .card {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-dog me-2"></i>VetSmart
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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

    <!-- Page Header -->
    <header class="page-header text-center">
        <div class="container">
            <h1>Our Marketplace</h1>
            <p class="lead">Quality products for your beloved pets.</p>
        </div>
    </header>

    <!-- Main Marketplace Content -->
    <main class="container py-5">
        <div class="row">
            <?php if (empty($products)): ?>
                <div class="col">
                    <div class="text-center p-5 bg-white rounded-3 shadow-sm">
                        <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                        <p class="lead mb-0">No products are available at the moment.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($products as $index => $product): ?>
                    <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                        <div class="card h-100" style="animation-delay: <?php echo $index * 0.05; ?>s;">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted small flex-grow-1"><?php echo substr(htmlspecialchars($product['description']), 0, 80); ?>...</p>
                                <p class="card-text fs-5 fw-bold text-success mb-3">
                                    <i class="fas fa-rupee-sign"></i> <?php echo number_format($product['price'], 2); ?>
                                </p>
                                <a href="product_detail.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary mt-auto">
                                    <i class="fas fa-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0">
                <i class="fas fa-copyright me-2"></i>
                2025 VetSmart Hospital. All Rights Reserved.
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
