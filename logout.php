<?php
session_start(); // Ensure session is started

// Check if a session is active before trying to destroy it
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();    // Clear all session variables
    session_destroy();  // Destroy the session
}

header("Location:login.php"); // Redirect to the login page
exit(); // Ensure no further code is executed
?>
