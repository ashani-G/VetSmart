<?php 
// Include the header, which also starts the session and checks for login
include 'includes/header.php'; 

// Fetch all reports associated with the pets of the logged-in user.
$user_id = $_SESSION['user_id'];
$reports = [];

$sql = "SELECT 
            r.report_id,
            r.generated_at,
            r.billing_amount,
            r.payment_status,
            p.pet_name,
            a.appointment_date
        FROM reports r
        JOIN appointments a ON r.appointment_id = a.appointment_id
        JOIN pets p ON a.pet_id = p.pet_id
        WHERE p.owner_id = ?
        ORDER BY r.generated_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reports[] = $row;
    }
}
mysqli_stmt_close($stmt);

// Function to determine badge color for payment status
function get_payment_status_badge($status) {
    return $status == 'Paid' ? 'badge rounded-pill bg-success' : 'badge rounded-pill bg-warning text-dark';
}
?>

<!-- Main Page Content -->
<h1 class="display-5 fw-bold mb-2">My Health Reports</h1>
<p class="lead text-muted mb-4">Find all health reports and billing summaries for your pets' appointments.</p>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($reports)): ?>
            <div class="text-center p-5">
                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                <h4 class="fw-bold">No Reports Found</h4>
                <p class="text-muted">Health reports for your pets will appear here after their appointments.</p>
                <a href="book_appointment.php" class="btn btn-primary mt-3">
                    <i class="fas fa-calendar-plus me-2"></i>Book an Appointment
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="py-3 ps-4">Pet Name</th>
                            <th scope="col" class="py-3">Appointment Date</th>
                            <th scope="col" class="py-3">Report Issued</th>
                            <th scope="col" class="py-3">Amount</th>
                            <th scope="col" class="py-3">Payment Status</th>
                            <th scope="col" class="py-3 pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td class="py-3 ps-4 fw-bold"><?php echo htmlspecialchars($report['pet_name']); ?></td>
                                <td class="py-3"><?php echo date('F j, Y', strtotime($report['appointment_date'])); ?></td>
                                <td class="py-3"><?php echo date('F j, Y', strtotime($report['generated_at'])); ?></td>
                                <td class="py-3">Rs. <?php echo number_format($report['billing_amount'], 2); ?></td>
                                <td class="py-3">
                                    <span class="<?php echo get_payment_status_badge($report['payment_status']); ?>">
                                        <?php echo htmlspecialchars($report['payment_status']); ?>
                                    </span>
                                </td>
                                <td class="py-3 pe-4">
                                    <a href="view_report.php?report_id=<?php echo $report['report_id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
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
