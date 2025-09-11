<?php
// Include the header for security and connection
include 'includes/header.php';

// Check if a report ID is provided in the URL
if (!isset($_GET['report_id']) || empty($_GET['report_id'])) {
    header('Location: reports.php');
    exit();
}

$report_id = intval($_GET['report_id']);
$user_id = $_SESSION['user_id'];

// SQL to fetch all details for the receipt, with a security check
$sql = "SELECT 
            r.report_id, r.diagnosis, r.treatment_notes, r.billing_amount, r.payment_status, r.generated_at,
            a.appointment_date,
            p.pet_name, p.species, p.breed,
            u.full_name AS client_name
        FROM reports r
        JOIN appointments a ON r.appointment_id = a.appointment_id
        JOIN pets p ON a.pet_id = p.pet_id
        JOIN users u ON p.owner_id = u.user_id
        WHERE r.report_id = ? AND p.owner_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $report_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$report = mysqli_fetch_assoc($result);

if (!$report) {
    $_SESSION['message'] = "Report not found or you do not have permission to view it.";
    $_SESSION['msg_type'] = "danger";
    header('Location: reports.php');
    exit();
}

// Function to determine badge color for payment status
function get_payment_status_badge($status) {
    return $status == 'Paid' ? 'badge rounded-pill bg-success' : 'badge rounded-pill bg-warning text-dark';
}
?>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4 print-hide">
    <div>
        <h1 class="display-5 fw-bold mb-2">Health Report</h1>
        <p class="lead text-muted">A detailed summary for your pet, <?php echo htmlspecialchars($report['pet_name']); ?>.</p>
    </div>
    <div>
        <a href="reports.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Reports</a>
        <button onclick="window.print();" class="btn btn-primary"><i class="fas fa-print me-2"></i>Print Report</button>
    </div>
</div>

<div class="card" id="report">
    <div class="card-body p-5">
        <!-- Report Header -->
        <div class="row mb-5">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary mb-2"><i class="fas fa-dog me-2"></i>VetSmart Hospital</h2>
                <p class="text-muted mb-0">123 Pet Street, Kurunegala</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h1 class="display-6 fw-bold text-uppercase text-muted">Health Report</h1>
                <p class="mb-0"><strong>Report ID:</strong> #<?php echo htmlspecialchars($report['report_id']); ?></p>
                <p class="mb-0"><strong>Issued On:</strong> <?php echo date('F j, Y', strtotime($report['generated_at'])); ?></p>
            </div>
        </div>

        <!-- Pet & Owner Info -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="fw-bold">Patient Details</h5>
                <p class="mb-0"><strong>Pet Name:</strong> <?php echo htmlspecialchars($report['pet_name']); ?></p>
                <p class="mb-0"><strong>Species:</strong> <?php echo htmlspecialchars($report['species']); ?></p>
                <p class="mb-0"><strong>Breed:</strong> <?php echo htmlspecialchars($report['breed']); ?></p>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold">Owner Details</h5>
                <p class="mb-0"><strong>Name:</strong> <?php echo htmlspecialchars($report['client_name']); ?></p>
                <p class="mb-0"><strong>Appointment Date:</strong> <?php echo date('F j, Y', strtotime($report['appointment_date'])); ?></p>
            </div>
        </div>
        
        <hr class="my-4">

        <!-- Clinical Findings -->
        <div class="mb-5">
            <h5 class="fw-bold mb-3">Clinical Findings</h5>
            <div class="p-4 rounded" style="background-color: #f8f9fa;">
                <h6 class="fw-bold">Diagnosis</h6>
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($report['diagnosis'])); ?></p>
                <h6 class="fw-bold mt-4">Treatment Notes</h6>
                <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($report['treatment_notes'])); ?></p>
            </div>
        </div>

        <!-- Billing Summary -->
        <div>
            <h5 class="fw-bold mb-3">Billing Summary</h5>
            <table class="table">
                <thead class="table-light">
                    <tr>
                        <th class="py-2">Description</th>
                        <th class="py-2 text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2">Consultation &amp; Treatment</td>
                        <td class="py-2 text-end">Rs. <?php echo number_format($report['billing_amount'], 2); ?></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td class="text-end border-0 pt-3">Total Amount</td>
                        <td class="text-end border-0 pt-3">Rs. <?php echo number_format($report['billing_amount'], 2); ?></td>
                    </tr>
                    <tr class="fw-bold">
                        <td class="text-end border-0">Payment Status</td>
                        <td class="text-end border-0">
                            <span class="<?php echo get_payment_status_badge($report['payment_status']); ?>">
                                <?php echo htmlspecialchars($report['payment_status']); ?>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- CSS for printing -->
<style>
@media print {
    body { background-color: #fff; }
    .print-hide, #sidebar-wrapper, .navbar { display: none !important; }
    #page-content-wrapper { padding: 0 !important; margin: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
    a[href]:after { content: none !important; }
}
</style>

<?php
mysqli_stmt_close($stmt);
include 'includes/footer.php';
?>
