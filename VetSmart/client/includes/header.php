<?php
// Start the session and check if the user is logged in.
session_start();

// If the user is not logged in or is not a 'client', redirect them to the login page.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'client') {
    header('Location: ../login.php');
    exit;
}

// Include the database connection
require_once '../php/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - VetSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=-apple-system,BlinkMacSystemFont,'SF Pro Display','Segoe UI',Roboto,Oxygen,Ubuntu,Cantarell,sans-serif&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --ios-blue: #007AFF;
            --ios-green: #34C759;
            --ios-red: #FF3B30;
            --ios-gray: #8E8E93;
            --ios-light-gray: #F2F2F7;
            --ios-dark-gray: #1C1C1E;
            --ios-white: #FFFFFF;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.08);
            --border-radius: 12px;
            --border-radius-lg: 20px;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
            background-color: var(--ios-light-gray);
            color: var(--ios-dark-gray);
        }

        #wrapper {
            display: flex;
            min-height: 100vh;
        }

        #sidebar-wrapper {
            width: 260px;
            background-color: var(--ios-white);
            border-right: 1px solid #E5E5EA;
            transition: margin .25s ease-out;
            padding: 1.5rem;
        }

        .sidebar-heading {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--ios-blue);
            padding: 0.5rem 0;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
        }
        .sidebar-heading .fa-dog {
            margin-right: 0.75rem;
        }

        .list-group-item-action {
            padding: 0.85rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: var(--border-radius);
            color: var(--ios-dark-gray);
            font-weight: 500;
            border: none;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .list-group-item-action .fa-fw {
            margin-right: 0.75rem;
            color: var(--ios-gray);
        }
        
        .list-group-item-action:hover, .list-group-item-action.active {
            background-color: rgba(0, 122, 255, 0.1);
            color: var(--ios-blue);
        }
        .list-group-item-action:hover .fa-fw, .list-group-item-action.active .fa-fw {
            color: var(--ios-blue);
        }

        #page-content-wrapper {
            flex: 1;
            padding: 2rem;
            overflow-x: hidden;
        }

        .navbar {
            background: transparent;
            border: none;
        }

        .card {
            border: none;
            border-radius: var(--border-radius-lg);
            background: var(--ios-white);
            box-shadow: var(--shadow-light);
            padding: 2rem;
        }
        
        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--ios-light-gray);
            padding: 0 0 1rem 0;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #E5E5EA;
            border-radius: var(--border-radius);
            padding: 12px 16px;
        }
        .form-control:focus {
            border-color: var(--ios-blue);
            box-shadow: 0 0 0 3px rgba(0,122,255,0.1);
        }
        
        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            padding: 12px 24px;
        }
        
        .btn-primary { background-color: var(--ios-blue); }
        .btn-success { background-color: var(--ios-green); }

        .nav-tabs { border-bottom: none; }
        .nav-tabs .nav-link {
            border: none;
            background: var(--ios-light-gray);
            color: var(--ios-gray);
            margin-right: 0.5rem;
            border-radius: var(--border-radius) !important;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            background: var(--ios-blue);
            color: var(--ios-white);
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <div class="sidebar-heading"><i class="fas fa-dog"></i>VetSmart</div>
        <div class="list-group list-group-flush">
            <a href="dashboard.php" class="list-group-item list-group-item-action"><i class="fas fa-home fa-fw"></i>Dashboard</a>
            <a href="my_pets.php" class="list-group-item list-group-item-action"><i class="fas fa-paw fa-fw"></i>My Pets</a>
            <a href="appointments.php" class="list-group-item list-group-item-action"><i class="fas fa-calendar-check fa-fw"></i>Appointments</a>
            <a href="book_appointment.php" class="list-group-item list-group-item-action"><i class="fas fa-calendar-plus fa-fw"></i>Book Now</a>
            <a href="reports.php" class="list-group-item list-group-item-action"><i class="fas fa-file-medical fa-fw"></i>Health Reports</a>
            <a href="my_orders.php" class="list-group-item list-group-item-action"><i class="fas fa-box fa-fw"></i>My Orders</a>
            <a href="profile.php" class="list-group-item list-group-item-action active"><i class="fas fa-user fa-fw"></i>Profile</a>
            <hr class="my-3">
            <a href="../index.php" class="list-group-item list-group-item-action"><i class="fas fa-arrow-left fa-fw"></i>Back to Home</a>
            <a href="../php/logout.php" class="list-group-item list-group-item-action text-danger"><i class="fas fa-sign-out-alt fa-fw"></i>Logout</a>
        </div>
    </div>
    <!-- /#sidebar-wrapper -->

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="navbar-text">
                                Welcome, <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong>!
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container-fluid">
