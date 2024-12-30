<?php
session_start(); // Start the session

// Initialize response array
$response = [];

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    $response['status'] = 'error'; 
    $response['message'] = 'User is not logged in.'; // Optional: add a message for clarity
    http_response_code(401); // Set response code for unauthorized
} else {
    $response['status'] = 'success'; // User is logged in
    $response['message'] = 'User is logged in.'; // Optional: add a message for clarity
    http_response_code(200); // Set response code for success
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
