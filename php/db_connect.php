<?php
/**
 * Database Connection Script
 * * This script connects to the MySQL database using mysqli.
 * It's designed to be included in other PHP files that need database access.
 */

// --- Database Configuration ---
// Replace these values with your actual local database credentials.
$db_host = 'localhost';      // Server name, usually 'localhost' for XAMPP
$db_user = 'root';           // Default username for XAMPP MySQL
$db_pass = '';               // Default password for XAMPP MySQL is empty
$db_name = 'vet_hospital_db'; // The name of the database we created

// --- Create Connection ---
// The mysqli_connect() function attempts to open a new connection to the MySQL server.
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// --- Check Connection ---
// It's important to check if the connection was successful.
// If mysqli_connect_errno() returns an error number, the connection failed.
if (mysqli_connect_errno()) {
    // The die() function prints a message and exits the current script.
    // This prevents the rest of the page from loading if the database isn't available.
    die("Database connection failed: " . mysqli_connect_error());
}

// --- Set Character Set ---
// It's good practice to set the character set to utf8mb4 to support a wide range of characters.
mysqli_set_charset($conn, "utf8mb4");

// The $conn variable now holds the active database connection and can be used to perform queries.
// We don't close the connection here; it will be closed automatically when the script finishes,
// or we can close it manually at the end of the script that includes this file.
?>
