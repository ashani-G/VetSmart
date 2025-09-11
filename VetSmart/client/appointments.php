<?php 
// Include the header, which also starts the session and checks for login
include 'includes/header.php'; 

// Fetch all appointments for the logged-in user, joining with the pets table to get pet names
$user_id = $_SESSION['user_id'];
$appointments = []; // Initialize an empty array

// The SQL query joins 'appointments' and 'pets' tables
$sql = "SELECT 
            a.appointment_id,
            a.appointment_date,
            a.reason,
            a.status,
            p.pet_name
        FROM appointments a
        JOIN pets p ON a.pet_id = p.pet_id
        WHERE a.client_id = ?
        ORDER BY a.appointment_date DESC"; // Order by most recent first

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}
mysqli_stmt_close($stmt);

// Function to determine badge color based on status
function get_status_badge($status) {
    switch ($status) {
        case 'Confirmed':
            return 'badge bg-success';
        case 'Pending':
            return 'badge bg-warning text-dark';
        case 'Cancelled':
            return 'badge bg-danger';
        case 'Completed':
            return 'badge bg-info text-dark';
        default:
            return 'badge bg-secondary';
    }
}
?>

<!-- Main Page Content -->
<h1 class="mt-4">My Appointments</h1>
<p>Here is a list of all your past and upcoming appointments.</p>

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

<div class="card">
    <div class="card-header">
        Appointment History
    </div>
    <div class="card-body">
        <?php if (empty($appointments)): ?>
            <p class="text-center">You have no appointments scheduled. <a href="book_appointment.php">Book one now!</a></p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Pet Name</th>
                            <th>Date & Time</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appt): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appt['pet_name']); ?></td>
                                <td><?php echo date('F j, Y, g:i a', strtotime($appt['appointment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($appt['reason']); ?></td>
                                <td>
                                    <span class="<?php echo get_status_badge($appt['status']); ?>">
                                        <?php echo htmlspecialchars($appt['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($appt['status'] == 'Pending' || $appt['status'] == 'Confirmed'): ?>
                                        <a href="#" class="btn btn-sm btn-danger">Cancel</a>
                                    <?php else: ?>
                                        <!-- No actions for past/cancelled appointments -->
                                        <button class="btn btn-sm btn-secondary" disabled>N/A</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
