<?php
// Start session and include necessary files
session_start();
require_once 'db_connect.php';

// --- Security Check: Ensure user is logged in ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// --- UPDATE PROFILE DETAILS LOGIC ---
if (isset($_POST['update_profile'])) {
    // Sanitize input
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Validation
    if (empty($full_name) || empty($email)) {
        $_SESSION['message'] = "Full Name and Email are required.";
        $_SESSION['msg_type'] = "danger";
        header('Location: ../client/profile.php');
        exit();
    }

    // SQL to update user details
    $sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ?, address = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $full_name, $email, $phone_number, $address, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Update session variables to reflect changes immediately
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $_SESSION['message'] = "Profile updated successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating profile. Please try again.";
        $_SESSION['msg_type'] = "danger";
    }
    mysqli_stmt_close($stmt);
    header('Location: ../client/profile.php');
    exit();
}

// --- CHANGE PASSWORD LOGIC ---
if (isset($_POST['change_password'])) {
    // Sanitize input
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Fetch current hashed password from DB
    $sql_fetch = "SELECT password FROM users WHERE user_id = ?";
    $stmt_fetch = mysqli_prepare($conn, $sql_fetch);
    mysqli_stmt_bind_param($stmt_fetch, "i", $user_id);
    mysqli_stmt_execute($stmt_fetch);
    $result = mysqli_stmt_get_result($stmt_fetch);
    $user = mysqli_fetch_assoc($result);
    $hashed_password_from_db = $user['password'];
    mysqli_stmt_close($stmt_fetch);

    // Verify current password
    if (!password_verify($current_password, $hashed_password_from_db)) {
        $_SESSION['message'] = "Incorrect current password.";
        $_SESSION['msg_type'] = "danger";
        header('Location: ../client/profile.php');
        exit();
    }

    // Validate new password
    if ($new_password !== $confirm_new_password) {
        $_SESSION['message'] = "New passwords do not match.";
        $_SESSION['msg_type'] = "danger";
        header('Location: ../client/profile.php');
        exit();
    }
    if (strlen($new_password) < 8) {
        $_SESSION['message'] = "New password must be at least 8 characters long.";
        $_SESSION['msg_type'] = "danger";
        header('Location: ../client/profile.php');
        exit();
    }

    // Hash new password and update in DB
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql_update = "UPDATE users SET password = ? WHERE user_id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $new_hashed_password, $user_id);

    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['message'] = "Password changed successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error changing password. Please try again.";
        $_SESSION['msg_type'] = "danger";
    }
    mysqli_stmt_close($stmt_update);
    header('Location: ../client/profile.php');
    exit();
}

// Redirect if no valid action was provided
header('Location: ../client/dashboard.php');
exit();
?>
