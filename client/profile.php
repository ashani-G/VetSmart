<?php 
// Include the header, which also starts the session and checks for login
include 'includes/header.php'; 

// Fetch the current user's information from the database
$user_id = $_SESSION['user_id'];
$user_info = [];

$sql = "SELECT full_name, email, phone_number, address FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $user_info = mysqli_fetch_assoc($result);
}
mysqli_stmt_close($stmt);
?>

<!-- Main Page Content -->
<h1 class="display-5 fw-bold mb-2">My Profile</h1>
<p class="lead text-muted mb-4">Update your personal information and manage your account password.</p>

<!-- Display Messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['msg_type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
        <i class="fas fa-<?php echo $_SESSION['msg_type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
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
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">
                    <i class="fas fa-user-edit me-2"></i>Profile Details
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">
                    <i class="fas fa-key me-2"></i>Change Password
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body pt-4">
        <!-- Tab Content -->
        <div class="tab-content" id="profileTabsContent">
            <!-- Profile Details Tab -->
            <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                <form action="../php/process_profile_actions.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="full_name" value="<?php echo htmlspecialchars($user_info['full_name']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phoneNumber" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phoneNumber" name="phone_number" value="<?php echo htmlspecialchars($user_info['phone_number']); ?>">
                        </div>
                         <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user_info['address']); ?></textarea>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary mt-4">Save Changes</button>
                </form>
            </div>
            <!-- Change Password Tab -->
            <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                 <form action="../php/process_profile_actions.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        </div>
                        <div class="col-md-6">
                            <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmNewPassword" name="confirm_new_password" required>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-success mt-4">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>
