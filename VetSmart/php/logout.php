<?php
/**
 * Logout Script
 * This script handles the user logout process.
 */

// 1. Initialize the session
// It's necessary to start the session to access its data before destroying it.
session_start();

// 2. Unset all of the session variables
// $_SESSION = array(); frees all session variables currently registered.
$_SESSION = array();

// 3. Destroy the session
// This function destroys all of the data associated with the current session.
session_destroy();

// 4. Set a success message for the login page
// We start a new, empty session to pass a one-time message.
session_start();
$_SESSION['message'] = "You have been logged out successfully.";
$_SESSION['msg_type'] = "success";

// 5. Redirect to the login page
// The user is sent back to the login page after the session is terminated.
header("location: ../login.php");
exit;
?>
