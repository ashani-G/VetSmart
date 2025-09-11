<?php
// Start the session and include necessary files.
session_start();
require_once 'db_connect.php';

// --- Security Check: Ensure user is logged in ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'client') {
    header('Location: ../login.php');
    exit;
}

// --- BOOK APPOINTMENT LOGIC ---
if (isset($_POST['book_appointment'])) {

    // --- Data Collection & Sanitization ---
    $client_id = $_SESSION['user_id'];
    $pet_id = mysqli_real_escape_string($conn, $_POST['pet_id']);
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $reason = mysqli_real_escape_string($conn, $_POST['reason']);

    // --- Server-Side Validation ---
    if (empty($pet_id) || empty($appointment_date) || empty($reason)) {
        $_SESSION['message'] = "Please fill in all the required fields.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../client/book_appointment.php");
        exit();
    }

    // Optional: Validate that the selected pet belongs to the logged-in user
    $sql_verify_pet = "SELECT pet_id FROM pets WHERE pet_id = ? AND owner_id = ?";
    $stmt_verify = mysqli_prepare($conn, $sql_verify_pet);
    mysqli_stmt_bind_param($stmt_verify, "ii", $pet_id, $client_id);
    mysqli_stmt_execute($stmt_verify);
    mysqli_stmt_store_result($stmt_verify);
    
    if (mysqli_stmt_num_rows($stmt_verify) == 0) {
        // Pet does not belong to the user, handle error
        $_SESSION['message'] = "Invalid pet selection.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../client/book_appointment.php");
        exit();
    }
    mysqli_stmt_close($stmt_verify);


    // --- Insert into Database ---
    // The status is 'Pending' by default and vet_id is NULL until an admin confirms it.
    $sql = "INSERT INTO appointments (client_id, pet_id, appointment_date, reason, status) VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiss", $client_id, $pet_id, $appointment_date, $reason);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Appointment requested successfully! You will be notified upon confirmation.";
        $_SESSION['msg_type'] = "success";
        // Redirect to the appointments list page
        header("Location: ../client/appointments.php"); 
    } else {
        $_SESSION['message'] = "Error: Could not book appointment. Please try again.";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../client/book_appointment.php");
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit();
}

// If no valid action was provided, redirect back.
header("Location: ../client/dashboard.php");
exit();
?>
