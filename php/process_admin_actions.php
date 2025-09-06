<?php
session_start();
require_once 'db_connect.php';

// --- SECURITY CHECK: Ensure user is a logged-in admin ---
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Unauthorized access.";
    $_SESSION['msg_type'] = "danger";
    header('Location: ../login.php');
    exit;
}

// --- REVIEW MANAGEMENT ---

// APPROVE REVIEW LOGIC
if (isset($_POST['approve_review'])) {
    $review_id = intval($_POST['review_id']);
    $sql = "UPDATE reviews SET is_approved = TRUE WHERE review_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $review_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Review approved and is now public.";
        $_SESSION['msg_type'] = "success";
    }
    header('Location: ../admin/reviews.php');
    exit();
}

// DELETE REVIEW LOGIC
if (isset($_POST['delete_review'])) {
    $review_id = intval($_POST['review_id']);
    $sql = "DELETE FROM reviews WHERE review_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $review_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Review has been deleted.";
        $_SESSION['msg_type'] = "warning";
    }
    header('Location: ../admin/reviews.php');
    exit();
}


// --- PRODUCT MANAGEMENT ---

// ADD PRODUCT LOGIC
if (isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);

    $sql = "INSERT INTO products (name, description, price, stock_quantity) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdi", $name, $desc, $price, $stock);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Product added successfully.";
        $_SESSION['msg_type'] = "success";
    }
    header('Location: ../admin/products.php');
    exit();
}

// EDIT PRODUCT LOGIC
if (isset($_POST['edit_product'])) {
    $product_id = intval($_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock_quantity']);

    $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock_quantity = ? WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdii", $name, $desc, $price, $stock, $product_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Product updated successfully.";
        $_SESSION['msg_type'] = "success";
    }
    header('Location: ../admin/products.php');
    exit();
}

// DELETE PRODUCT LOGIC
if (isset($_POST['delete_product'])) {
    $product_id = intval($_POST['product_id']);

    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Product deleted successfully.";
        $_SESSION['msg_type'] = "warning";
    }
    header('Location: ../admin/products.php');
    exit();
}


// --- ORDER MANAGEMENT ---

// UPDATE ORDER STATUS LOGIC
if (isset($_POST['update_order_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['order_status']);

    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $order_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Order status updated.";
        $_SESSION['msg_type'] = "success";
    }
    header('Location: ../admin/orders.php');
    exit();
}


// --- APPOINTMENT & REPORT MANAGEMENT ---

// Function to update appointment status
function updateAppointmentStatus($conn, $appointment_id, $new_status) {
    $sql = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $appointment_id);
    return mysqli_stmt_execute($stmt);
}

// CONFIRM APPOINTMENT LOGIC
if (isset($_POST['confirm_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $vet_id = $_SESSION['user_id'];

    $sql = "UPDATE appointments SET status = 'Confirmed', vet_id = ? WHERE appointment_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $vet_id, $appointment_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Appointment confirmed.";
        $_SESSION['msg_type'] = "success";
    }
    header('Location: ../admin/appointments.php');
    exit();
}

// CHECK-IN APPOINTMENT LOGIC
if (isset($_POST['checkin_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    if (updateAppointmentStatus($conn, $appointment_id, 'In Progress')) {
        $_SESSION['message'] = "Pet checked in successfully.";
        $_SESSION['msg_type'] = "success";
    }
    header('Location: ../admin/appointments.php');
    exit();
}

// COMPLETE APPOINTMENT LOGIC
if (isset($_POST['complete_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    if (updateAppointmentStatus($conn, $appointment_id, 'Completed')) {
        $_SESSION['message'] = "Appointment marked as completed. Ready for billing.";
        $_SESSION['msg_type'] = "info";
    }
    header('Location: ../admin/appointments.php');
    exit();
}

// CANCEL APPOINTMENT LOGIC
if (isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    if (updateAppointmentStatus($conn, $appointment_id, 'Cancelled')) {
        $_SESSION['message'] = "Appointment has been cancelled.";
        $_SESSION['msg_type'] = "warning";
    }
    header('Location: ../admin/appointments.php');
    exit();
}

// GENERATE REPORT LOGIC
if (isset($_POST['generate_report'])) {
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $treatment_notes = mysqli_real_escape_string($conn, $_POST['treatment_notes']);
    $billing_amount = mysqli_real_escape_string($conn, $_POST['billing_amount']);

    if (empty($appointment_id) || empty($diagnosis) || empty($billing_amount)) {
        $_SESSION['message'] = "Please fill in all required fields to generate a report.";
        $_SESSION['msg_type'] = "danger";
        header('Location: ../admin/reports.php');
        exit();
    }

    $sql = "INSERT INTO reports (appointment_id, diagnosis, treatment_notes, billing_amount, payment_status) VALUES (?, ?, ?, ?, 'Unpaid')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "isss", $appointment_id, $diagnosis, $treatment_notes, $billing_amount);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Report generated successfully.";
        $_SESSION['msg_type'] = "success";
    }
    header('Location: ../admin/reports.php');
    exit();
}


// --- DEFAULT REDIRECT ---
// Redirect if no specific action was triggered
header('Location: ../admin/dashboard.php');
exit();
?>
