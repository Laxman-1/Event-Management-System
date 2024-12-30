<?php
session_start(); // Ensure the session is started

// Check if the session exists before unsetting it
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();    // Clear all session variables
    session_destroy();  // Destroy the session
}

header("Location: ../login.php");
exit(); // Ensure the script stops executing
?>
login.php