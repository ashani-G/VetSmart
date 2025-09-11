<?php session_start(); // Start the session to manage login state ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - VetSmart</title>
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
            padding: 2rem;
        }

        .form-control {
            border: 2px solid var(--ios-light-gray);
            border-radius: var(--border-radius);
            padding: 12px 16px;
            transition: all 0.3s ease;
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
                    <li class="nav-item"><a class="nav-link" href="marketplace.php">Marketplace</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="services.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link active" href="contact.php">Contact</a></li>
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
            <h1 class="display-4">Get In Touch</h1>
            <p class="lead text-muted">We're here to help. Contact us with any questions you may have.</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-5">
        <div class="row g-5">
            <!-- Contact Information -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <h3 class="fw-bold mb-4">Our Clinic</h3>
                    <ul class="list-unstyled">
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-map-marker-alt fa-fw me-3 text-primary mt-1 fs-5"></i>
                            <span>123 Pet Street, Kurunegala, Sri Lanka</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-phone fa-fw me-3 text-primary mt-1 fs-5"></i>
                            <span>(076) 456-7890</span>
                        </li>
                        <li class="mb-3 d-flex align-items-start">
                            <i class="fas fa-envelope fa-fw me-3 text-primary mt-1 fs-5"></i>
                            <span>contact@vetsmart.com</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-clock fa-fw me-3 text-primary mt-1 fs-5"></i>
                            <div>
                                <strong class="d-block mb-1">Opening Hours:</strong>
                                <small class="text-muted">
                                    Mon - Fri: 9:00 AM - 6:00 PM<br>
                                    Sat: 10:00 AM - 4:00 PM<br>
                                    Sun: Closed
                                </small>
                            </div>
                        </li>
                    </ul>
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden mt-4">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d63261.4729783495!2d80.32185164228516!3d7.482901968560128!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae3398a2e729f59%3A0x863b8a7091b3e83e!2sKurunegala!5e0!3m2!1sen!2slk!4v1663182824891!5m2!1sen!2slk" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
            </div>
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="card h-100">
                     <h3 class="fw-bold mb-4">Send Us a Message</h3>
                    <!-- 
                        To make this form functional, follow these steps:
                        1. Go to formspree.io and create a new form.
                        2. Replace YOUR_UNIQUE_ID in the action URL below with your form's unique endpoint ID.
                    -->
                    <form action="https://formspree.io/f/myzdpwen" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-lg"><i class="fas fa-paper-plane me-2"></i>Send Message</button>
                    </form>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
