<?php
session_start(); // Start the session

// Handle message sending
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $bookingId = $_POST['booking_id'];
    $message = $_POST['message'];

    // Initialize messages array in session if not already set
    if (!isset($_SESSION['messages'])) {
        $_SESSION['messages'] = [];
    }

    // Add the message to the session under the booking ID
    $_SESSION['messages'][$bookingId][] = $message;

    // Respond to the AJAX request
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
    exit; // Exit to avoid displaying the HTML below
}

// Handle message editing
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_message'])) {
    $bookingId = $_POST['booking_id'];
    $messageIndex = $_POST['message_index'];
    $newMessage = $_POST['edit_message'];

    // Update the message in the session
    if (isset($_SESSION['messages'][$bookingId][$messageIndex])) {
        $_SESSION['messages'][$bookingId][$messageIndex] = $newMessage;
    }

    // Respond to the AJAX request
    echo json_encode(['status' => 'success', 'message' => 'Message updated successfully!']);
    exit;
}

// Handle message deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_message'])) {
    $bookingId = $_POST['booking_id'];
    $messageIndex = $_POST['message_index'];

    // Remove the message from the session
    if (isset($_SESSION['messages'][$bookingId][$messageIndex])) {
        unset($_SESSION['messages'][$bookingId][$messageIndex]);
    }

    // Respond to the AJAX request
    echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully!']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Send Message to User</h2>
        <form id="messageForm">
            <input type="hidden" name="booking_id" value="1"> <!-- Replace with actual booking ID -->
            <div class="form-group">
                <label for="message">Message:</label>
                <textarea id="message" name="message" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Message</button>
        </form>
        <div id="messageResponse"></div>

        <h2 class="mt-5">Sent Messages</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="messagesTable">
                <?php
                // Display sent messages
                if (isset($_SESSION['messages'])) {
                    foreach ($_SESSION['messages'] as $bookingId => $messages) {
                        foreach ($messages as $index => $message) {
                            echo "<tr data-booking-id='$bookingId' data-message-index='$index'>
                                    <td class='message-text'>$message</td>
                                    <td>
                                        <button class='btn btn-warning edit-button'>Edit</button>
                                        <button class='btn btn-danger delete-button'>Delete</button>
                                    </td>
                                  </tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='2' class='text-center'>No messages found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#messageForm').on('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                $.ajax({
                    type: 'POST',
                    url: 'send_message.php', // URL to send the message
                    data: $(this).serialize(), // Serialize the form data
                    dataType: 'json', // Expect a JSON response
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#messageResponse').html('<div class="alert alert-success">' + response.message + '</div>');
                            $('#messageForm')[0].reset(); // Reset the form
                            location.reload(); // Reload the page to display the new message
                        }
                    },
                    error: function() {
                        $('#messageResponse').html('<div class="alert alert-danger">Error sending message.</div>');
                    }
                });
            });

            // Edit message functionality
            $(document).on('click', '.edit-button', function() {
                var row = $(this).closest('tr');
                var messageText = row.find('.message-text').text();
                var bookingId = row.data('booking-id');
                var messageIndex = row.data('message-index');

                // Prompt for new message
                var newMessage = prompt("Edit your message:", messageText);
                if (newMessage !== null) {
                    $.ajax({
                        type: 'POST',
                        url: 'send_message.php',
                        data: {
                            edit_message: newMessage,
                            booking_id: bookingId,
                            message_index: messageIndex
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                location.reload(); // Reload the page to show updated message
                            }
                        }
                    });
                }
            });

            // Delete message functionality
            $(document).on('click', '.delete-button', function() {
                var row = $(this).closest('tr');
                var bookingId = row.data('booking-id');
                var messageIndex = row.data('message-index');

                if (confirm("Are you sure you want to delete this message?")) {
                    $.ajax({
                        type: 'POST',
                        url: 'send_message.php',
                        data: {
                            delete_message: true,
                            booking_id: bookingId,
                            message_index: messageIndex
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                location.reload(); // Reload the page to show updated messages
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
