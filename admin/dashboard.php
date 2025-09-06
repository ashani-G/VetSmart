<?php 
// Include the header, which handles session and security checks.
include 'includes/header.php'; 

// --- Fetch Admin Dashboard Data ---

// Set the timezone to Sri Lanka
date_default_timezone_set('Asia/Colombo');

// Get count of today's appointments
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');
$today_appt_sql = "SELECT COUNT(appointment_id) AS today_count FROM appointments WHERE appointment_date BETWEEN ? AND ?";
$stmt_today = mysqli_prepare($conn, $today_appt_sql);
mysqli_stmt_bind_param($stmt_today, "ss", $today_start, $today_end);
mysqli_stmt_execute($stmt_today);
$today_result = mysqli_stmt_get_result($stmt_today);
$today_count = mysqli_fetch_assoc($today_result)['today_count'];
mysqli_stmt_close($stmt_today);


// Get count of pending reviews
$pending_reviews_sql = "SELECT COUNT(review_id) AS pending_reviews_count FROM reviews WHERE is_approved = 0";
$pending_reviews_result = mysqli_query($conn, $pending_reviews_sql);
$pending_reviews_count = mysqli_fetch_assoc($pending_reviews_result)['pending_reviews_count'];

// Get count of total clients
$client_count_sql = "SELECT COUNT(user_id) AS client_count FROM users WHERE role = 'client'";
$client_result = mysqli_query($conn, $client_count_sql);
$client_count = mysqli_fetch_assoc($client_result)['client_count'];

// Get count of total pets
$pet_count_sql = "SELECT COUNT(pet_id) AS pet_count FROM pets";
$pet_result = mysqli_query($conn, $pet_count_sql);
$pet_count = mysqli_fetch_assoc($pet_result)['pet_count'];

// Fetch Recent Activity (last 5 appointments and orders)
$recent_appointments = [];
$recent_appts_sql = "SELECT a.appointment_date, a.status, p.pet_name, u.full_name FROM appointments a JOIN pets p ON a.pet_id = p.pet_id JOIN users u ON a.client_id = u.user_id ORDER BY a.appointment_date DESC LIMIT 5";
$recent_appts_result = mysqli_query($conn, $recent_appts_sql);
if ($recent_appts_result) {
    while($row = mysqli_fetch_assoc($recent_appts_result)){
        $recent_appointments[] = $row;
    }
}

$recent_orders = [];
$recent_orders_sql = "SELECT o.order_date, o.order_status, o.total_amount, u.full_name FROM orders o JOIN users u ON o.client_id = u.user_id ORDER BY o.order_date DESC LIMIT 5";
$recent_orders_result = mysqli_query($conn, $recent_orders_sql);
if ($recent_orders_result) {
    while($row = mysqli_fetch_assoc($recent_orders_result)){
        $recent_orders[] = $row;
    }
}
?>

<!-- Main Dashboard Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">Admin Dashboard</h1>
        <p class="lead text-muted">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Here's what's happening today.</p>
    </div>
    <div class="text-end">
        <h5 class="fw-bold mb-1"><i class="fas fa-clock me-2"></i><?php echo date('h:i A'); ?></h5>
        <p class="text-muted mb-0"><?php echo date('l, F j, Y'); ?></p>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small">Today's Appointments</div>
                    <div class="h2 fw-bold mb-0"><?php echo $today_count; ?></div>
                </div>
                <div class="h1 text-primary ms-3"><i class="fas fa-calendar-day"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small">Pending Reviews</div>
                    <div class="h2 fw-bold mb-0"><?php echo $pending_reviews_count; ?></div>
                </div>
                <div class="h1 text-warning ms-3"><i class="fas fa-comments"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small">Total Clients</div>
                    <div class="h2 fw-bold mb-0"><?php echo $client_count; ?></div>
                </div>
                <div class="h1 text-info ms-3"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="text-muted text-uppercase small">Registered Pets</div>
                    <div class="h2 fw-bold mb-0"><?php echo $pet_count; ?></div>
                </div>
                <div class="h1 text-success ms-3"><i class="fas fa-paw"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="fw-bold mb-0"><i class="fas fa-history me-2"></i>Recent Activity</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach($recent_appointments as $appt): ?>
                        <div class="list-group-item">
                            <i class="fas fa-calendar-check me-2 text-primary"></i>
                            New appointment for <strong><?php echo htmlspecialchars($appt['pet_name']); ?></strong> (Owner: <?php echo htmlspecialchars($appt['full_name']); ?>)
                            <span class="float-end text-muted small"><?php echo date('M j, g:i a', strtotime($appt['appointment_date'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php foreach($recent_orders as $order): ?>
                        <div class="list-group-item">
                            <i class="fas fa-shopping-cart me-2 text-success"></i>
                            New order placed by <strong><?php echo htmlspecialchars($order['full_name']); ?></strong> for Rs. <?php echo number_format($order['total_amount'], 2); ?>
                            <span class="float-end text-muted small"><?php echo date('M j, Y', strtotime($order['order_date'])); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="fw-bold mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-3">
                    <a href="appointments.php" class="btn btn-primary btn-lg"><i class="fas fa-calendar-alt me-2"></i>Manage Appointments</a>
                    <a href="products.php" class="btn btn-success btn-lg"><i class="fas fa-box-open me-2"></i>Add New Product</a>
                    <a href="clients.php" class="btn btn-info btn-lg"><i class="fas fa-users me-2"></i>View All Clients</a>
                    <a href="reports.php" class="btn btn-warning btn-lg"><i class="fas fa-file-invoice me-2"></i>Generate a Report</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>

