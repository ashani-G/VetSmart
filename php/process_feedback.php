<?php
session_start();
require_once 'db_connect.php';

if (isset($_POST['submit_review'])) {
    // Sanitize input
    $client_name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    // Validation
    if (empty($client_name) || empty($rating) || empty($comment) || $rating < 1 || $rating > 5) {
        // Handle error - for simplicity, we'll just redirect
        header('Location: ../index.php');
        exit();
    }

    // Insert into database (is_approved is FALSE by default)
    $sql = "INSERT INTO reviews (client_name, rating, comment) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sis", $client_name, $rating, $comment);
    
    if (mysqli_stmt_execute($stmt)) {
        // You can set a success message here if you want
    }

    // Redirect back to the homepage
    header('Location: ../index.php');
    exit();
}
?>
