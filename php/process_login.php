<?php
// Start the session to manage user login state.
session_start();

// Include the database connection file.
require_once 'db_connect.php';

// Check if the login form was submitted.
if (isset($_POST['login'])) {

    // --- Data Collection & Sanitization ---
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // --- Server-Side Validation ---
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = "Please enter both email and password.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../login.php");
        exit();
    }

    // --- Fetch User from Database ---
    // Use a prepared statement to find the user by email.
    $sql = "SELECT user_id, full_name, email, password, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if a user with that email exists.
    if ($user = mysqli_fetch_assoc($result)) {
        // --- Verify Password ---
        // Use password_verify() to compare the submitted password with the stored hash.
        if (password_verify($password, $user['password'])) {
            // Password is correct. Login successful.

            // --- Store User Info in Session ---
            // This keeps the user logged in across different pages.
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['loggedin'] = true;

            // --- Redirect Based on Role ---
            if ($user['role'] == 'admin') {
                // Redirect to the admin dashboard.
                header("Location: ../admin/dashboard.php");
                exit();
            } else {
                // Redirect to the client dashboard.
                header("Location: ../client/dashboard.php");
                exit();
            }
        } else {
            // Password is not correct.
            $_SESSION['message'] = "Invalid email or password.";
            $_SESSION['msg_type'] = "danger";
            header("Location: ../login.php");
            exit();
        }
    } else {
        // No user found with that email.
        $_SESSION['message'] = "Invalid email or password.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../login.php");
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    // If the form wasn't submitted, redirect back to the login page.
    header("Location: ../login.php");
    exit();
}
?>
