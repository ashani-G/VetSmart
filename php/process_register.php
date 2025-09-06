<?php
// Start a session to store messages.
session_start();

// Include the database connection file.
require_once 'db_connect.php';

// Check if the form was submitted by checking for the 'register' button's name attribute.
if (isset($_POST['register'])) {

    // --- Data Collection & Sanitization ---
    // Use mysqli_real_escape_string to prevent SQL injection.
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);

    // --- Server-Side Validation ---
    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['message'] = "Please fill in all required fields.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../register.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../register.php");
        exit();
    }
    
    if (strlen($password) < 8) {
        $_SESSION['message'] = "Password must be at least 8 characters long.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../register.php");
        exit();
    }

    // --- Check for Existing Email ---
    // Use prepared statements to prevent SQL injection.
    $sql_check_email = "SELECT email FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql_check_email);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['message'] = "An account with this email already exists.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../register.php");
        exit();
    }
    mysqli_stmt_close($stmt);

    // --- Password Hashing ---
    // Hash the password for security before storing it in the database.
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // --- Insert New User into Database ---
    $sql_insert_user = "INSERT INTO users (full_name, email, password, phone_number, role) VALUES (?, ?, ?, ?, 'client')";
    $stmt = mysqli_prepare($conn, $sql_insert_user);
    mysqli_stmt_bind_param($stmt, "ssss", $full_name, $email, $hashed_password, $phone_number);

    if (mysqli_stmt_execute($stmt)) {
        // Success
        $_SESSION['message'] = "Registration successful! Please login.";
        $_SESSION['msg_type'] = "success";
        header("Location: ../login.php"); // Redirect to login page on success
        exit();
    } else {
        // Failure
        $_SESSION['message'] = "Something went wrong. Please try again.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../register.php");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    // If the form wasn't submitted, redirect back to the registration page.
    header("Location: ../register.php");
    exit();
}
?>
