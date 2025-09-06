<?php
// Start the session and check if the user is logged in as an admin.
session_start();

// If the user is not logged in, not an admin, or the session is not valid, redirect to login.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "You do not have permission to access this page.";
    $_SESSION['msg_type'] = "danger";
    header('Location: ../login.php');
    exit;
}

// Include the database connection
require_once '../php/db_connect.php';

// Get the current page name
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VetSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-light-gray: #F2F2F7;
            --ios-dark-gray: #1C1C1E;
            --sidebar-bg: #1C1C1E;
            --sidebar-text: #E5E5EA;
            --sidebar-hover: #3A3A3C;
            --main-bg: #F2F2F7;
            --card-bg: #FFFFFF;
            --border-radius: 12px;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background-color: var(--main-bg);
            overflow-x: hidden;
        }
        #wrapper {
            display: flex;
            min-height: 100vh;
        }
        #sidebar-wrapper {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            transition: all 0.3s ease;
        }
        .sidebar-heading {
            padding: 1.5rem 1.25rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
        }
        .list-group-item-action {
            background-color: transparent;
            border: none;
            color: var(--sidebar-text);
            padding: 1rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }
        .list-group-item-action .fa-fw {
            width: 24px;
            margin-right: 1rem;
        }
        .list-group-item-action:hover, .list-group-item-action.active {
            background-color: var(--ios-blue);
            color: #fff;
            border-radius: var(--border-radius);
        }
        .list-group .list-group-item-action {
             margin: 0.25rem 1rem;
             width: calc(100% - 2rem);
        }
        #page-content-wrapper {
            flex: 1;
        }
        .navbar {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            background-color: var(--card-bg);
        }
        .btn {
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <div class="sidebar-heading">
            <i class="fas fa-shield-alt me-2"></i>VetSmart Admin
        </div>
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-tachometer-alt"></i>Dashboard
            </a>
            <a href="appointments.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'appointments.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-calendar-check"></i>Appointments
            </a>
            <a href="clients.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'clients.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-users"></i>Clients & Pets
            </a>
            <a href="reports.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-file-invoice-dollar"></i>Reports & Billing
            </a>
            <a href="products.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-box-open"></i>Products
            </a>
            <a href="orders.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-shopping-cart"></i>Orders
            </a>
            <a href="reviews.php" class="list-group-item list-group-item-action <?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
                <i class="fas fa-fw fa-star"></i>Reviews
            </a>
            <hr class="mx-3" style="border-color: rgba(255,255,255,0.1);">
            <a href="../index.php" class="list-group-item list-group-item-action">
                <i class="fas fa-fw fa-home"></i>Back to Homepage
            </a>
            <a href="../php/logout.php" class="list-group-item list-group-item-action">
                <i class="fas fa-fw fa-sign-out-alt"></i>Logout
            </a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light sticky-top">
            <div class="container-fluid">
                 <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fw-bold" href="#">
                            <i class="fas fa-user-circle me-2"></i>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?> (Admin)
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid p-4">
