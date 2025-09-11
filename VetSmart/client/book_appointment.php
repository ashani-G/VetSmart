<?php 
// Include the header, which also starts the session and checks for login
include 'includes/header.php'; 

// Fetch the user's pets to populate the dropdown
$user_id = $_SESSION['user_id'];
$pets = [];
$sql_pets = "SELECT pet_id, pet_name FROM pets WHERE owner_id = ?";
$stmt_pets = mysqli_prepare($conn, $sql_pets);
mysqli_stmt_bind_param($stmt_pets, "i", $user_id);
mysqli_stmt_execute($stmt_pets);
$result_pets = mysqli_stmt_get_result($stmt_pets);
if ($result_pets) {
    while ($row = mysqli_fetch_assoc($result_pets)) {
        $pets[] = $row;
    }
}
mysqli_stmt_close($stmt_pets);
?>

<!-- Main Page Content -->
<h1 class="display-5 fw-bold mb-2">Book an Appointment</h1>
<p class="lead text-muted mb-4">Fill out the form below to request an appointment for your pet.</p>

<!-- Display Messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['msg_type']; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            unset($_SESSION['msg_type']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-4">
        <form action="../php/process_appointment.php" method="POST">
            <div class="mb-4">
                <label for="pet" class="form-label fw-bold">Select Pet</label>
                <select class="form-select form-select-lg" id="pet" name="pet_id" required>
                    <option value="" disabled selected>-- Choose a pet --</option>
                    <?php foreach ($pets as $pet): ?>
                        <option value="<?php echo $pet['pet_id']; ?>">
                            <?php echo htmlspecialchars($pet['pet_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (empty($pets)): ?>
                    <div class="form-text text-danger mt-2">
                        You must <a href="my_pets.php">add a pet</a> before you can book an appointment.
                    </div>
                <?php endif; ?>
            </div>
            <div class="mb-4">
                <label for="appointmentDate" class="form-label fw-bold">Preferred Date and Time</label>
                <input type="datetime-local" class="form-control form-control-lg" id="appointmentDate" name="appointment_date" required>
                <div class="form-text">Please select a time during our opening hours.</div>
            </div>
            <div class="mb-4">
                <label for="reason" class="form-label fw-bold">Reason for Visit</label>
                <textarea class="form-control form-control-lg" id="reason" name="reason" rows="4" placeholder="e.g., Annual check-up, not eating, etc." required></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" name="book_appointment" class="btn btn-primary btn-lg" <?php if (empty($pets)) echo 'disabled'; ?>>
                    <i class="fas fa-paper-plane me-2"></i>Request Appointment
                </button>
            </div>
        </form>
    </div>
</div>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
