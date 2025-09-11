<?php 
// Include the header, which also starts the session and checks for login
include 'includes/header.php'; 

// --- Fetch Dashboard Data ---
$user_id = $_SESSION['user_id'];

// 1. Fetch data for summary cards
// Pet Count
$stmt_pets = $conn->prepare("SELECT COUNT(pet_id) AS total FROM pets WHERE owner_id = ?");
$stmt_pets->bind_param("i", $user_id);
$stmt_pets->execute();
$pet_count = $stmt_pets->get_result()->fetch_assoc()['total'] ?? 0;

// Upcoming Appointment Count
$stmt_appt = $conn->prepare("SELECT COUNT(appointment_id) AS total FROM appointments WHERE client_id = ? AND appointment_date >= NOW() AND status IN ('Pending', 'Confirmed')");
$stmt_appt->bind_param("i", $user_id);
$stmt_appt->execute();
$appt_count = $stmt_appt->get_result()->fetch_assoc()['total'] ?? 0;

// Total Orders Count
$stmt_orders = $conn->prepare("SELECT COUNT(order_id) AS total FROM orders WHERE client_id = ?");
$stmt_orders->bind_param("i", $user_id);
$stmt_orders->execute();
$order_count = $stmt_orders->get_result()->fetch_assoc()['total'] ?? 0;

// Health Reports Count
$stmt_reports = $conn->prepare("SELECT COUNT(r.report_id) AS total FROM reports r JOIN appointments a ON r.appointment_id = a.appointment_id JOIN pets p ON a.pet_id = p.pet_id WHERE p.owner_id = ?");
$stmt_reports->bind_param("i", $user_id);
$stmt_reports->execute();
$report_count = $stmt_reports->get_result()->fetch_assoc()['total'] ?? 0;

// 2. Fetch details for the next upcoming appointment
$next_appointment = null;
$stmt_next_appt = $conn->prepare("SELECT a.appointment_date, a.reason, p.pet_name FROM appointments a JOIN pets p ON a.pet_id = p.pet_id WHERE a.client_id = ? AND a.appointment_date >= NOW() AND a.status IN ('Pending', 'Confirmed') ORDER BY a.appointment_date ASC LIMIT 1");
$stmt_next_appt->bind_param("i", $user_id);
$stmt_next_appt->execute();
$result_next_appt = $stmt_next_appt->get_result();
if ($result_next_appt->num_rows > 0) {
    $next_appointment = $result_next_appt->fetch_assoc();
}

// 3. Fetch recent orders
$recent_orders = [];
$stmt_recent_orders = $conn->prepare("SELECT order_id, order_date, total_amount, order_status FROM orders WHERE client_id = ? ORDER BY order_date DESC LIMIT 3");
$stmt_recent_orders->bind_param("i", $user_id);
$stmt_recent_orders->execute();
$result_recent_orders = $stmt_recent_orders->get_result();
if ($result_recent_orders->num_rows > 0) {
    while($row = $result_recent_orders->fetch_assoc()) {
        $recent_orders[] = $row;
    }
}

// Function to determine badge color based on status
function get_order_status_badge($status) {
    switch ($status) {
        case 'Processing': return 'badge rounded-pill bg-primary';
        case 'Shipped': return 'badge rounded-pill bg-info text-dark';
        case 'Delivered': return 'badge rounded-pill bg-success';
        case 'Cancelled': return 'badge rounded-pill bg-danger';
        default: return 'badge rounded-pill bg-secondary'; // Pending
    }
}
?>

<!-- Main Dashboard Content -->
<h1 class="display-5 fw-bold mb-2">Dashboard</h1>
<p class="lead text-muted mb-4">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Here's a summary of your account.</p>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-paw fs-4"></i>
                    </div>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?php echo $pet_count; ?></h4>
                    <p class="text-muted mb-0">Registered Pets</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-calendar-check fs-4"></i>
                    </div>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?php echo $appt_count; ?></h4>
                    <p class="text-muted mb-0">Upcoming Appointments</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-box fs-4"></i>
                    </div>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?php echo $order_count; ?></h4>
                    <p class="text-muted mb-0">Total Orders</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fas fa-file-medical fs-4"></i>
                    </div>
                </div>
                <div>
                    <h4 class="fw-bold mb-0"><?php echo $report_count; ?></h4>
                    <p class="text-muted mb-0">Health Reports</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Dashboard Sections -->
<div class="row">
    <!-- Left Column: Appointments and Orders -->
    <div class="col-lg-8">
        <!-- Upcoming Appointment -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="fw-bold mb-0"><i class="fas fa-calendar-alt me-2"></i>Next Appointment</h5>
            </div>
            <div class="card-body">
                <?php if ($next_appointment): ?>
                    <h4 class="fw-bold"><?php echo date('l, F j, Y', strtotime($next_appointment['appointment_date'])); ?></h4>
                    <p class="fs-5 text-muted mb-2">at <?php echo date('g:i A', strtotime($next_appointment['appointment_date'])); ?></p>
                    <p><strong>Pet:</strong> <?php echo htmlspecialchars($next_appointment['pet_name']); ?></p>
                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($next_appointment['reason']); ?></p>
                    <a href="appointments.php" class="btn btn-outline-primary mt-2">View All Appointments</a>
                <?php else: ?>
                    <p class="text-center text-muted p-4">No upcoming appointments. <br><a href="book_appointment.php">Schedule one today!</a></p>
                <?php endif; ?>
            </div>
        </div>
        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h5 class="fw-bold mb-0"><i class="fas fa-history me-2"></i>Recent Orders</h5>
            </div>
            <div class="card-body">
                <?php if (empty($recent_orders)): ?>
                    <p class="text-center text-muted p-4">You haven't placed any orders yet. <br><a href="../marketplace.php">Explore the marketplace!</a></p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_orders as $order): ?>
                            <a href="view_order.php?order_id=<?php echo $order['order_id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="d-block">Order #<?php echo $order['order_id']; ?></strong>
                                    <small class="text-muted">Placed on <?php echo date('F j, Y', strtotime($order['order_date'])); ?></small>
                                </div>
                                <div class="text-end">
                                    <strong class="d-block">Rs. <?php echo number_format($order['total_amount'], 2); ?></strong>
                                    <span class="<?php echo get_order_status_badge($order['order_status']); ?>"><?php echo htmlspecialchars($order['order_status']); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Right Column: Quick Actions -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="fw-bold mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="book_appointment.php" class="list-group-item list-group-item-action fs-5 py-3"><i class="fas fa-plus-circle me-3 text-success"></i>Book Appointment</a>
                <a href="my_pets.php" class="list-group-item list-group-item-action fs-5 py-3"><i class="fas fa-paw me-3 text-primary"></i>Manage My Pets</a>
                <a href="../marketplace.php" class="list-group-item list-group-item-action fs-5 py-3"><i class="fas fa-store me-3 text-warning"></i>Go to Marketplace</a>
                <a href="my_orders.php" class="list-group-item list-group-item-action fs-5 py-3"><i class="fas fa-receipt me-3 text-info"></i>View My Orders</a>
                <a href="profile.php" class="list-group-item list-group-item-action fs-5 py-3"><i class="fas fa-user-cog me-3 text-secondary"></i>Edit My Profile</a>
            </div>
        </div>
    </div>
</div>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
