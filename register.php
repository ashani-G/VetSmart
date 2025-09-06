<?php session_start(); // Start the session to display messages ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - VetSmart</title>
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

        .register-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: var(--ios-white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-heavy);
            overflow: hidden;
        }

        .register-image-section {
            background: url('assets/css/images/register cat.jpg') no-repeat center center;
            background-size: cover;
            flex: 1;
        }

        .register-form-section {
            flex: 1;
            padding: 3rem 4rem;
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
            border-color: var(--ios-green);
            box-shadow: 0 0 0 3px rgba(52,199,89,0.1);
            outline: none;
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--ios-green), #30D158);
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 12px 20px;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52,199,89,0.2);
        }

        .brand-logo {
            font-weight: 700;
            font-size: 2rem;
            color: var(--ios-blue);
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .register-image-section {
                display: none;
            }
            .register-form-section {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>

    <div class="register-container">
        <!-- Image Section -->
        <div class="register-image-section"></div>

        <!-- Form Section -->
        <div class="register-form-section">
            <div class="text-center mb-4">
                <a class="brand-logo" href="index.php"><i class="fas fa-heartbeat me-2"></i>VetSmart</a>
                <h1 class="h3 fw-bold mt-3">Create Your Account</h1>
                <p class="text-muted">Join our community of happy pet owners.</p>
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

            <form action="php/process_register.php" method="POST">
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                 <div class="mb-3">
                    <label for="phoneNumber" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phoneNumber" name="phone_number">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                    </div>
                </div>
                <div class="d-grid mt-3">
                    <button type="submit" name="register" class="btn btn-success btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </div>
            </form>
            <p class="text-center text-muted mt-4">
                Already have an account? <a href="login.php" class="fw-bold">Login here</a>
            </p>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

