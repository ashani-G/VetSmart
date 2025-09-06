<?php 
// Include the header, which handles session and security checks.
include 'includes/header.php'; 

// Fetch completed appointments and check if a report already exists.
$appointments = [];
$sql = "SELECT 
            a.appointment_id, a.appointment_date,
            u.full_name AS client_name, p.pet_name,
            r.report_id
        FROM appointments a
        JOIN users u ON a.client_id = u.user_id
        JOIN pets p ON a.pet_id = p.pet_id
        LEFT JOIN reports r ON a.appointment_id = r.appointment_id
        WHERE a.status = 'Completed'
        ORDER BY a.appointment_date DESC";

$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
}
?>

<!-- Main Page Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="display-5 fw-bold mb-2">Reports & Billing</h1>
        <p class="lead text-muted">Generate health reports and manage billing for completed appointments.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fas fa-file-alt me-2"></i>Completed Appointments</h5>
            <div class="w-50">
                <input type="text" id="reportSearchInput" class="form-control" placeholder="Search by client or pet name...">
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="reportsTable">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Pet</th>
                        <th>Appointment Date</th>
                        <th class="text-center">Report Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5">No completed appointments found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $appt): ?>
                            <tr class="report-row" data-search-term="<?php echo strtolower(htmlspecialchars($appt['client_name'] . ' ' . $appt['pet_name'])); ?>">
                                <td><strong><?php echo htmlspecialchars($appt['client_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($appt['pet_name']); ?></td>
                                <td><?php echo date('F j, Y', strtotime($appt['appointment_date'])); ?></td>
                                <td class="text-center">
                                    <?php if ($appt['report_id']): ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Generated</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i>Awaiting</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($appt['report_id']): ?>
                                        <a href="view_receipt.php?report_id=<?php echo $appt['report_id']; ?>" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye me-1"></i>View Report
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#generateReportModal"
                                                data-appointment-id="<?php echo $appt['appointment_id']; ?>">
                                            <i class="fas fa-file-invoice me-1"></i>Generate Report
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Generate Report Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="generateReportModalLabel"><i class="fas fa-notes-medical me-2"></i>Generate Health Report & Bill</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="../php/process_admin_actions.php" method="POST">
        <div class="modal-body">
            <input type="hidden" name="appointment_id" id="modalAppointmentId">
            <div class="mb-3">
                <label for="diagnosis" class="form-label">Diagnosis</label>
                <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="treatmentNotes" class="form-label">Treatment Notes</label>
                <textarea class="form-control" id="treatmentNotes" name="treatment_notes" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label for="billingAmount" class="form-label">Total Billing Amount (Rs.)</label>
                <input type="number" step="0.01" class="form-control" id="billingAmount" name="billing_amount" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="generate_report" class="btn btn-success"><i class="fas fa-save me-2"></i>Save Report</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Pass appointment ID to the modal
    const generateReportModal = document.getElementById('generateReportModal');
    generateReportModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const appointmentId = button.getAttribute('data-appointment-id');
        const modalAppointmentIdInput = generateReportModal.querySelector('#modalAppointmentId');
        modalAppointmentIdInput.value = appointmentId;
    });

    // Search functionality
    const searchInput = document.getElementById('reportSearchInput');
    const reportRows = document.querySelectorAll('#reportsTable tbody .report-row');
    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();
        reportRows.forEach(row => {
            const rowSearchTerm = row.getAttribute('data-search-term');
            row.style.display = rowSearchTerm.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>
