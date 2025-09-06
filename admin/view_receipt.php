<?php
// Include the header for security and connection
include 'includes/header.php';

// Check if a report ID is provided in the URL
if (!isset($_GET['report_id']) || empty($_GET['report_id'])) {
    echo "<div class='alert alert-danger'>No report specified.</div>";
    include 'includes/footer.php';
    exit();
}

$report_id = intval($_GET['report_id']);

// SQL to fetch all details for the report
$sql = "SELECT 
            r.report_id, r.diagnosis, r.treatment_notes, r.billing_amount, r.payment_status, r.generated_at,
            a.appointment_date,
            p.pet_name, p.species, p.breed, p.date_of_birth,
            u.full_name, u.email, u.phone_number
        FROM reports r
        JOIN appointments a ON r.appointment_id = a.appointment_id
        JOIN pets p ON a.pet_id = p.pet_id
        JOIN users u ON a.client_id = u.user_id
        WHERE r.report_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $report_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$report = mysqli_fetch_assoc($result);

if (!$report) {
    echo "<div class='alert alert-danger'>Report not found.</div>";
    include 'includes/footer.php';
    exit();
}

// Function to calculate age from date of birth
function calculate_age($dob) {
    if (!$dob) return 'N/A';
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    $age = $today->diff($birthDate);
    return $age->y . " years, " . $age->m . " months, " . $age->d . " days";
}
?>

<!-- Print-friendly styles -->
<style>
    @media print {
        body * { visibility: hidden; }
        .printable-area, .printable-area * { visibility: visible; }
        .printable-area { position: absolute; left: 0; top: 0; width: 100%; padding: 0; }
        .no-print { display: none; }
        #sidebar-wrapper, .navbar, .page-title-header { display: none !important; }
        #page-content-wrapper { margin-left: 0 !important; padding: 0 !important; }
        .container-fluid { padding: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
    }
</style>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4 page-title-header">
    <div>
        <h1 class="display-5 fw-bold mb-2">Health Report</h1>
        <p class="lead text-muted">A detailed clinical summary for the appointment on <?php echo date('F j, Y', strtotime($report['appointment_date'])); ?>.</p>
    </div>
    <div class="no-print">
        <a href="reports.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Reports</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-2"></i>Print Report</button>
    </div>
</div>

<div class="card printable-area">
    <div class="card-body p-5">
        <!-- Report Header -->
        <div class="row mb-5 align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold text-primary"><i class="fas fa-dogbeat me-2"></i>VetSmart Clinic</h2>
                <p class="text-muted mb-0">123 Pet Street, Kurunegala, Sri Lanka</p>
            </div>
            <div class="col-md-6 text-md-end">
                <h1 class="display-4 fw-bold text-muted">CLINICAL REPORT</h1>
                <p class="fw-bold mb-0">Report ID: #<?php echo htmlspecialchars($report['report_id']); ?></p>
                <p class="text-muted">Issued: <?php echo date('F j, Y, g:i a', strtotime($report['generated_at'])); ?></p>
            </div>
        </div>

        <!-- Patient & Client Information -->
        <div class="row border-top border-bottom py-4 mb-5">
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">PATIENT DETAILS</h5>
                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($report['pet_name']); ?></p>
                <p class="mb-1"><strong>Species:</strong> <?php echo htmlspecialchars($report['species']); ?></p>
                <p class="mb-1"><strong>Breed:</strong> <?php echo htmlspecialchars($report['breed']); ?></p>
                <p class="mb-1"><strong>Age:</strong> <?php echo calculate_age($report['date_of_birth']); ?></p>
            </div>
            <div class="col-md-6">
                <h5 class="fw-bold mb-3">CLIENT INFORMATION</h5>
                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($report['full_name']); ?></p>
                <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($report['email']); ?></p>
                <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($report['phone_number']); ?></p>
            </div>
        </div>

        <!-- Clinical Details -->
        <div class="mb-5">
            <h4 class="fw-bold text-primary mb-3">Clinical Findings</h4>
            <div class="p-4 bg-light rounded">
                <h5 class="fw-bold">Diagnosis</h5>
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($report['diagnosis'])); ?></p>
                <hr>
                <h5 class="fw-bold mt-4">Treatment & Notes</h5>
                <p class="text-muted"><?php echo nl2br(htmlspecialchars($report['treatment_notes'])); ?></p>
            </div>
        </div>
        
        <!-- Billing Summary -->
        <div class="row justify-content-end">
            <div class="col-md-5">
                <h5 class="fw-bold text-primary">Billing Summary</h5>
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <th class="border-0">Consultation & Services Fee</th>
                            <td class="text-end border-0">Rs. <?php echo number_format($report['billing_amount'], 2); ?></td>
                        </tr>
                        <tr class="bg-light">
                            <th class="fw-bold fs-5 align-middle">Total Amount</th>
                            <td class="fw-bold fs-5 text-end align-middle">Rs. <?php echo number_format($report['billing_amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <th class="border-0">Payment Status</th>
                            <td class="text-end border-0 fw-bold">
                                <?php if ($report['payment_status'] == 'Paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer -->
         <div class="row border-top mt-5 pt-4">
            <div class="col text-center text-muted">
                <p>This report was generated on <?php echo date('F j, Y'); ?>. If you have any questions, please contact the clinic.</p>
                <p><strong>VetSmart Clinic - Compassionate Care for Your Beloved Pets.</strong></p>
            </div>
        </div>
    </div>
</div>

<?php
mysqli_stmt_close($stmt);
include 'includes/footer.php';
?>
