<?php 
// Include the header, which handles session and security checks.
include 'includes/header.php'; 

// --- Fetch All Appointments ---
$appointments = [
    'Today' => [],
    'Pending' => [],
    'Confirmed' => [],
    'In Progress' => [],
    'Completed' => [],
    'Cancelled' => []
];

$sql = "SELECT a.appointment_id, a.appointment_date, a.status, a.reason, u.full_name AS client_name, u.phone_number, p.pet_name
        FROM appointments a
        JOIN users u ON a.client_id = u.user_id
        JOIN pets p ON a.pet_id = p.pet_id
        ORDER BY a.appointment_date ASC";

$result = mysqli_query($conn, $sql);
if ($result) {
    $today_date = date('Y-m-d');
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[$row['status']][] = $row;
        $appt_date = date('Y-m-d', strtotime($row['appointment_date']));
        if ($row['status'] == 'Confirmed' && $appt_date == $today_date) {
            $appointments['Today'][] = $row;
        }
    }
}

// Function to determine badge color based on status
function get_status_badge($status) {
    switch ($status) {
        case 'Confirmed': return 'badge bg-success';
        case 'Pending': return 'badge bg-warning text-dark';
        case 'In Progress': return 'badge bg-primary';
        case 'Completed': return 'badge bg-info text-dark';
        case 'Cancelled': return 'badge bg-danger';
        default: return 'badge bg-secondary';
    }
}

// Helper function to render the table rows for appointments
function render_appointment_rows($appts) {
    if (empty($appts)) {
        echo '<tr><td colspan="7" class="text-center text-muted p-4">No appointments in this category.</td></tr>';
        return;
    }
    foreach ($appts as $appt) {
        echo '<tr class="appointment-row" data-search-term="' . strtolower(htmlspecialchars($appt['client_name'] . ' ' . $appt['pet_name'])) . '">';
        echo '<td>' . htmlspecialchars($appt['client_name']) . '</td>';
        echo '<td>' . htmlspecialchars($appt['pet_name']) . '</td>';
        echo '<td>' . date('F j, Y, g:i a', strtotime($appt['appointment_date'])) . '</td>';
        echo '<td><span class="' . get_status_badge($appt['status']) . '">' . htmlspecialchars($appt['status']) . '</span></td>';
        echo '<td>';
        echo '<form action="../php/process_admin_actions.php" method="POST" class="d-inline">';
        echo '<input type="hidden" name="appointment_id" value="' . $appt['appointment_id'] . '">';
        if ($appt['status'] == 'Pending') {
            echo '<button type="submit" name="confirm_appointment" class="btn btn-sm btn-success">Confirm</button> ';
            echo '<button type="submit" name="cancel_appointment" class="btn btn-sm btn-danger">Cancel</button>';
        } elseif ($appt['status'] == 'Confirmed') {
            echo '<button type="submit" name="checkin_appointment" class="btn btn-sm btn-primary">Check-In</button>';
        } elseif ($appt['status'] == 'In Progress') {
            echo '<button type="submit" name="complete_appointment" class="btn btn-sm btn-info">Complete</button>';
        } else {
            echo '<button class="btn btn-sm btn-secondary" disabled>No Action</button>';
        }
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
}
?>

<!-- Main Page Content -->
<h1 class="display-5 fw-bold mb-2">Appointment Management</h1>
<p class="lead text-muted mb-4">A modern interface to manage the daily appointment workflow.</p>

<!-- Today's Confirmed Appointments Section -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="fw-bold mb-0"><i class="fas fa-star me-2"></i>Today's Confirmed Appointments</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Client</th><th>Pet</th><th>Time</th><th>Status</th><th>Action</th></tr></thead>
                <tbody><?php render_appointment_rows($appointments['Today']); ?></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Main Tabbed Interface -->
<div class="card">
    <div class="card-header">
         <div class="d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs card-header-tabs" id="appointmentTabs" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#pending" type="button">Pending <span class="badge rounded-pill bg-secondary"><?php echo count($appointments['Pending']); ?></span></button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#confirmed" type="button">Confirmed <span class="badge rounded-pill bg-secondary"><?php echo count($appointments['Confirmed']); ?></span></button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#inprogress" type="button">In Progress <span class="badge rounded-pill bg-secondary"><?php echo count($appointments['In Progress']); ?></span></button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#completed" type="button">Completed</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cancelled" type="button">Cancelled</button></li>
            </ul>
             <div class="w-25"><input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search by client or pet..."></div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="pending" role="tabpanel"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Client</th><th>Pet</th><th>Date & Time</th><th>Status</th><th>Action</th></tr></thead><tbody><?php render_appointment_rows($appointments['Pending']); ?></tbody></table></div></div>
            <div class="tab-pane fade" id="confirmed" role="tabpanel"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Client</th><th>Pet</th><th>Date & Time</th><th>Status</th><th>Action</th></tr></thead><tbody><?php render_appointment_rows($appointments['Confirmed']); ?></tbody></table></div></div>
            <div class="tab-pane fade" id="inprogress" role="tabpanel"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Client</th><th>Pet</th><th>Date & Time</th><th>Status</th><th>Action</th></tr></thead><tbody><?php render_appointment_rows($appointments['In Progress']); ?></tbody></table></div></div>
            <div class="tab-pane fade" id="completed" role="tabpanel"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Client</th><th>Pet</th><th>Date & Time</th><th>Status</th><th>Action</th></tr></thead><tbody><?php render_appointment_rows($appointments['Completed']); ?></tbody></table></div></div>
            <div class="tab-pane fade" id="cancelled" role="tabpanel"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Client</th><th>Pet</th><th>Date & Time</th><th>Status</th><th>Action</th></tr></thead><tbody><?php render_appointment_rows($appointments['Cancelled']); ?></tbody></table></div></div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const appointmentRows = document.querySelectorAll('.appointment-row');
    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();
        appointmentRows.forEach(function(row) {
            const rowSearchTerm = row.getAttribute('data-search-term');
            row.style.display = rowSearchTerm.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>
