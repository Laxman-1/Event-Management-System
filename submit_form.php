<?php
session_start(); // Start the session

// Database connection
include '../Include/database.php'; // Include database connection file

// Get form data
$event_name = $_POST['event_name'] ?? '';
$event_location = $_POST['event_location'] ?? '';
$attendees = isset($_POST['attendees']) ? intval($_POST['attendees']) : 0; // Ensure attendees is an integer
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$start_time = $_POST['start_time'] ?? '';
$end_time = $_POST['end_time'] ?? '';
$normal_price = $_POST['normal_price'] ?? null;
$vip_price = $_POST['vip_price'] ?? null;
$vvip_price = $_POST['vvip_price'] ?? null;
$additional_details = $_POST['additional_details'] ?? '';
$category_id = $_POST['category_id'] ?? 0;
$organizer_id = $_POST['organizer_id'] ?? 0; // Get organizer_id from form
$event_type_id = $_POST['event_type_id'] ?? 0;

// Check if organizer_id exists in the organizer table
$check_stmt = $conn->prepare("SELECT COUNT(*) FROM organizer WHERE id = ?");
$check_stmt->bind_param('i', $organizer_id);
$check_stmt->execute();
$check_stmt->bind_result($count);
$check_stmt->fetch();
$check_stmt->close();

if ($count == 0) {
    die("Invalid organizer_id. No such organizer exists.");
}

// Handle file upload
$event_image = '';
if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    
    // Create directory if it does not exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            die("Failed to create directory: $upload_dir");
        }
    }
    
    $tmp_name = $_FILES['event_image']['tmp_name'];
    $file_name = basename($_FILES['event_image']['name']);
    $upload_file = $upload_dir . $file_name;

    if (move_uploaded_file($tmp_name, $upload_file)) {
        $event_image = $file_name;
    } else {
        die("Failed to move uploaded file. Check directory permissions or path.");
    }
}

// Prepare SQL statement
$stmt = $conn->prepare("
    INSERT INTO eventdata (
        event_name, event_location, attendees, start_date, end_date, start_time, 
        end_time, event_image, normal_price, vip_price, vvip_price, 
        additional_details, category_id, organizer_id, event_type_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$stmt->bind_param(
    'ssisssssddisiii', 
    $event_name, 
    $event_location, 
    $attendees, 
    $start_date, 
    $end_date, 
    $start_time, 
    $end_time, 
    $event_image, 
    $normal_price, 
    $vip_price, 
    $vvip_price, 
    $additional_details, 
    $category_id, 
    $organizer_id, 
    $event_type_id
);

// Execute the statement
if ($stmt->execute()) {
    echo "New event created successfully";
} else {
    echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();
?>
