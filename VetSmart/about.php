<?php session_start(); // Start the session to manage login state ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - VetSmart</title>
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
            padding: 80px 0;
            background: var(--ios-white);
            border-bottom: 1px solid #E5E5EA;
        }
        
        .page-header h1 {
            font-size: 3rem;
            font-weight: 700;
            color: var(--ios-dark-gray);
        }

        .page-header p {
            font-size: 1.3rem;
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
            height: 250px;
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

        .team-img {
            height: 300px;
            object-fit: cover;
        }

        .value-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--ios-green);
        }
        
        /* Footer */
        footer {
            background: var(--ios-dark-gray) !important;
            color: var(--ios-white) !important;
            padding: 40px 0 !important;
        }
        
        /* Section styling */
        section {
            padding: 60px 0;
        }
        section:nth-of-type(odd) {
            background: var(--ios-white);
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link active" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
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
            <h1 class="display-4">About VetSmart</h1>
            <p class="lead">Dedicated to the health and happiness of every pet.</p>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Our Mission Section -->
        <section class="our-mission">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <img src="assets\css\images\happy dog.png" class="img-fluid rounded-3 shadow-sm" alt="A happy dog being held by a veterinarian">
                    </div>
                    <div class="col-lg-6">
                        <h2 class="display-5 fw-bold mb-3">Our Mission</h2>
                        <p class="lead text-muted">At VetSmart, our mission is simple: to provide the highest standard of veterinary care for pets. We provide friendly, informative, and supportive services for pets and their owners.</p>
                        <p class="lead text-muted">We believe in a proactive approach to wellness, focusing on preventative care to keep your companions healthy and happy for a lifetime. Our state-of-the-art facility is equipped to handle everything from routine check-ups to complex surgical procedures.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Team Section -->
        <section class="our-team text-center">
            <div class="container">
                <h2 class="display-5 fw-bold mb-3">Meet Our Team</h2>
                <p class="lead text-muted mb-5">Our team of compassionate and experienced professionals is dedicated to your pet's well-being.</p>
                <div class="row">
                    <!-- Team Member 1 -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 text-center">
                            <img src="assets\css\images\doctor_ashani.jpg" class="card-img-top team-img" alt="Dr. Emily Smith">
                            <div class="card-body">
                                <h5 class="card-title">Dr.Parthana</h5>
                                <p class="card-text text-muted">Lead Veterinarian</p>
                                <p class="card-text small">With over 15 years of experience, Dr. Parthana specializes in internal medicine and is passionate about animal welfare.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Team Member 2 -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 text-center">
                            <img src="assets\css\images\dath dosthara.jpg" class="card-img-top team-img" alt="John Doe">
                            <div class="card-body">
                                <h5 class="card-title">Dr. Anne Marrie</h5>
                                <p class="card-text text-muted">Veterinary Technician</p>
                                <p class="card-text small">Anne Marrie is a certified vet tech who ensures every pet feels safe and comfortable during their visit.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Team Member 3 -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 text-center">
                            <img src="assets\css\images\kasun.jpg" class="card-img-top team-img" alt="Kasun Senarathne">
                            <div class="card-body">
                                <h5 class="card-title">Kasun Senarathne</h5>
                                <p class="card-text text-muted">Clinic Manager</p>
                                <p class="card-text small">Kasun Senarathne handles all our clinic operations, ensuring a smooth and pleasant experience for you and your pet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Values Section -->
        <section class="our-values text-center">
            <div class="container">
                <h2 class="display-5 fw-bold mb-3">Our Core Values</h2>
                <div class="row mt-5">
                    <div class="col-md-4 mb-4">
                        <i class="fas fa-dogbeat value-icon"></i>
                        <h4 class="fw-bold">Compassion</h4>
                        <p class="text-muted">We treat all pets as if they were our own, with kindness and empathy.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <i class="fas fa-graduation-cap value-icon"></i>
                        <h4 class="fw-bold">Excellence</h4>
                        <p class="text-muted">We are committed to the highest standards of medical and surgical care.</p>
                    </div>
                    <div class="col-md-4 mb-4">
                        <i class="fas fa-users value-icon"></i>
                        <h4 class="fw-bold">Teamwork</h4>
                        <p class="text-muted">We collaborate with you, the owner, to create the best healthcare plan for your pet.</p>
                    </div>
                </div>
            </div>
        </section>
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
