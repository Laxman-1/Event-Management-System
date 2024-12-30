<?php
session_start(); // Start the session
include 'header.php';

//$userBookingIds = [1, 2]; // 
$messagesToShow = [];

// Retrieve messages for the user's bookings
if (isset($_SESSION['messages'])) {
    foreach ($userBookingIds as $bookingId) {
        if (isset($_SESSION['messages'][$bookingId])) {
            $messagesToShow[$bookingId] = $_SESSION['messages'][$bookingId];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Messages</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light background for contrast */
        }
        .notice-card {
            margin-bottom: 20px;
            background-color: #ffffff; /* White background for each notice */
            border: 1px solid #ccc; /* Light border */
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        .card-header {
            background-color: #007bff; /* Bootstrap primary color */
            color: white; /* White text for header */
            font-weight: bold;
        }
        .card-body {
            padding: 15px;
        }
        .message-time {
            font-size: 0.9em;
            color: #6c757d; /* Muted text color */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Your Notices</h2>
        <div id="messagesContainer">
            <?php
            if (!empty($messagesToShow)) {
                foreach ($messagesToShow as $bookingId => $messages) {
                    echo "<h5>Messages for Booking ID: $bookingId</h5>";
                    foreach ($messages as $message) {
                        $timestamp = date("Y-m-d H:i:s"); // You might want to replace this with your actual timestamp logic
                        echo "<div class='card notice-card'>
                                <div class='card-header'>Notice</div>
                                <div class='card-body'>
                                    <p class='card-text'>{$message}</p>
                                    <p class='message-time'>Received on: {$timestamp}</p>
                                </div>
                              </div>";
                    }
                }
            } else {
                echo "<div class='alert alert-info'>No messages found.</div>";
            }
            ?>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
