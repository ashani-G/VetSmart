<?php session_start(); // Start the session to manage login state ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - VetSmart</title>
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

        .page-header {
            padding: 80px 0;
            background: var(--ios-white);
            border-bottom: 1px solid #E5E5EA;
        }
        
        .page-header h1 {
            font-size: 3rem;
            font-weight: 700;
        }

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
            height: 200px;
            object-fit: cover;
        }
        
        .card-body {
             padding: 1.5rem;
        }
        
        .card-title {
            font-weight: 600;
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
                    <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link active" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
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
            <h1 class="display-4">Comprehensive Pet Care</h1>
            <p class="lead text-muted">A wide range of services to meet all of your pet's health needs.</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-5">
        <section class="services-list">
            <div class="row g-4">
                <!-- Service 1: Grooming -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <img src="assets\css\images\grooming.jpg" class="card-img-top" alt="Pet Grooming">
                        <div class="card-body text-center">
                            <h4 class="card-title"><i class="fas fa-cut me-2 text-primary"></i>Grooming</h4>
                            <p class="card-text text-muted">Keep your furry friends clean and happy with our professional grooming services, from baths and brushing to nail trimming.</p>
                        </div>
                    </div>
                </div>
                <!-- Service 2: Vaccinations -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <img src="assets\css\images\vacination.jpg" class="card-img-top" alt="Pet Vaccinations">
                        <div class="card-body text-center">
                            <h4 class="card-title"><i class="fas fa-syringe me-2 text-primary"></i>Vaccinations</h4>
                            <p class="card-text text-muted">We provide core and lifestyle-based vaccinations to protect your pet from common and dangerous infectious diseases.</p>
                        </div>
                    </div>
                </div>
                <!-- Service 3: Dental Care -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <img src="assets\css\images\dental.jpg" class="card-img-top" alt="Pet Dental Care">
                        <div class="card-body text-center">
                            <h4 class="card-title"><i class="fas fa-tooth me-2 text-primary"></i>Dental Care</h4>
                            <p class="card-text text-muted">We offer professional cleaning, polishing, and extractions to prevent dental disease and maintain your pet's oral health.</p>
                        </div>
                    </div>
                </div>
                <!-- Service 4: Surgery -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <img src="assets\css\images\surgical.jpg" class="card-img-top" alt="Surgical Services">
                        <div class="card-body text-center">
                             <h4 class="card-title"><i class="fas fa-procedures me-2 text-primary"></i>Surgical Services</h4>
                            <p class="card-text text-muted">Our surgical suite is equipped for a wide range of procedures, from spaying and neutering to more complex operations.</p>
                        </div>
                    </div>
                </div>
                <!-- Service 5: Diagnostics -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <img src="assets\css\images\diagnos.jpg" class="card-img-top" alt="Diagnostics and Imaging">
                        <div class="card-body text-center">
                            <h4 class="card-title"><i class="fas fa-x-ray me-2 text-primary"></i>Diagnostics & Imaging</h4>
                            <p class="card-text text-muted">With in-house X-ray and lab services, we get fast, accurate results to guide your pet's treatment.</p>
                        </div>
                    </div>
                </div>
                <!-- Service 6: Emergency Care -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <img src="assets\css\images\emergency.jpg" class="card-img-top" alt="Emergency Pet Care">
                        <div class="card-body text-center">
                            <h4 class="card-title"><i class="fas fa-first-aid me-2 text-primary"></i>Emergency Care</h4>
                            <p class="card-text text-muted">Our team is trained to handle urgent medical situations with speed and expertise when your pet needs it most.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
