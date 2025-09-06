<?php session_start(); // Start the session to display messages ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VetSmart</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif&display=swap" rel="stylesheet">
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-light-blue: #5AC8FA;
            --ios-green: #34C759;
            --ios-red: #FF3B30;
            --ios-gray: #8E8E93;
            --ios-light-gray: #F2F2F7;
            --ios-white: #FFFFFF;
            --border-radius: 12px;
            --border-radius-lg: 20px;
            --shadow-heavy: 0 8px 30px rgba(0,0,0,0.1);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background-color: var(--ios-light-gray);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: var(--ios-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-heavy);
            overflow: hidden;
        }

        .login-image-section {
            background: url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?q=80&w=2069&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            flex: 1;
        }

        .login-form-section {
            flex: 1;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
            outline: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--ios-blue), var(--ios-light-blue));
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 12px 20px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,122,255,0.2);
        }

        .brand-logo {
            font-weight: 700;
            font-size: 2rem;
            color: var(--ios-blue);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .login-image-section {
                display: none;
            }
            .login-form-section {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Image Section -->
        <div class="login-image-section"></div>

        <!-- Form Section -->
        <div class="login-form-section">
            <div class="text-center mb-5">
                <a class="brand-logo" href="index.php"><i class="fas fa-dogbeat me-2"></i>VetSmart</a>
                <h1 class="h3 fw-bold mt-3">Welcome Back!</h1>
                <p class="text-muted">Sign in to continue to your dashboard.</p>
            </div>

            <!-- Display Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
                    <?php 
                        echo $_SESSION['message']; 
                        unset($_SESSION['message']);
                        unset($_SESSION['msg_type']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form action="php/process_login.php" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="d-grid">
                    <button type="submit" name="login" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </div>
            </form>
            <p class="text-center text-muted mt-4">
                Don't have an account? <a href="register.php" class="fw-bold">Register here</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
