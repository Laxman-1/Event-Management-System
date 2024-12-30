<?php
session_start(); 
include 'adminheader.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] !== 'approved') {
    header("Location: orglogin.php"); // Redirect to login page if not authenticated or not approved
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'event');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve event details
$event_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM eventdata WHERE id = ?");
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
} else {
    die("Event not found.");
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #6b7f9f, #a3c3d7); /* Gradient background */
            margin: 0;
            padding: 20px;
            animation: fadeIn 1s; /* Animation for fade-in effect */
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #ffffff; /* White background for the container */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2); /* Soft shadow for depth */
            transition: transform 0.3s, box-shadow 0.3s; /* Transition for hover effects */
        }
        .container:hover {
            transform: translateY(-5px); /* Lift effect on hover */
            box-shadow: 0 8px 40px rgba(0,0,0,0.3); /* Enhanced shadow on hover */
        }
        h1 {
            text-align: center;
            color: #333; /* Darker text for headings */
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2); /* Shadow for depth */
        }
        .event-details {
            display: flex; /* Flexbox for column layout */
            margin-top: 20px;
        }
        .event-image {
            flex: 1; /* Left column for image */
            margin-right: 20px; /* Space between columns */
        }
        .event-image img {
            max-width: 100%; /* Responsive image */
            border-radius: 8px; /* Rounded image corners */
            transition: transform 0.3s; /* Transition for image scaling */
        }
        .event-image img:hover {
            transform: scale(1.05); /* Scale effect on hover */
        }
        .event-info {
            flex: 2; /* Right column for details */
            color: #666; /* Lighter grey for paragraph text */
            line-height: 1.6; /* Increased line height for readability */
        }
        .event-info p {
            margin: 5px 0;
        }
        .event-info strong {
            color: #555; /* Medium grey for labels */
        }
        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2196F3; /* Button color */
            color: #ffffff; /* White text */
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
            margin-top: 20px; /* Space above the button */
            transition: background-color 0.3s, transform 0.3s; /* Transition for hover effects */
        }
        .back-button:hover {
            background-color: #1976D2; /* Darker blue on hover */
            transform: scale(1.05); /* Scale effect on hover */
        }
        @media (max-width: 600px) {
            .event-details {
                flex-direction: column; /* Stack columns on smaller screens */
                margin: 0; /* Reset margin */
            }
            .event-image {
                margin-right: 0; /* Reset right margin */
                margin-bottom: 15px; /* Space below the image */
            }
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="dashboard-container">
        <header class="dashboard-header">
            <ul>  <h1>
                <li><a href="dashboard.php">Home</a></li>
            </ul></h1>
            <nav>
                
            </nav>
            <nav>
                <ul>
                    <li><a href="adminLogin.php">Logout</a></li>
                </ul>
            </nav>
        </header></div>
        <h1>Event Details</h1>
        <div class="event-details">
            <div class="event-image">
                <?php if (!empty($event['event_image'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($event['event_image']); ?>" alt="Event Image">
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>
            <div class="event-info">
                <p><strong>Event Name:</strong> <?php echo htmlspecialchars($event['event_name']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($event['event_location']); ?></p>
                <?php if (!empty($event['event_url'])): ?>
                    <p><strong>Event URL:</strong> <a href="<?php echo htmlspecialchars($event['event_url']); ?>" target="_blank"><?php echo htmlspecialchars($event['event_url']); ?></a></p>
                <?php endif; ?>
                <p><strong>Start Date:</strong> <?php echo htmlspecialchars($event['start_date']); ?></p>
                <p><strong>End Date:</strong> <?php echo htmlspecialchars($event['end_date']); ?></p>
                <p><strong>Start Time:</strong> <?php echo htmlspecialchars($event['start_time']); ?></p>
                <p><strong>End Time:</strong> <?php echo htmlspecialchars($event['end_time']); ?></p>
                <p><strong>Normal Price:</strong> Rs <?php echo htmlspecialchars($event['normal_price']); ?></p>
                <p><strong>VIP Price:</strong> Rs <?php echo htmlspecialchars($event['vip_price']); ?></p>
                <p><strong>VVIP Price:</strong> Rs <?php echo htmlspecialchars($event['vvip_price']); ?></p>
                <p><strong>Additional Details:</strong> <?php echo htmlspecialchars($event['additional_details']); ?></p>
                <p><strong>Category ID:</strong> <?php echo htmlspecialchars($event['category_id']); ?></p>
                <p><strong>Organizer ID:</strong> <?php echo htmlspecialchars($event['organizer_id']); ?></p>
                <p><strong>Event Type ID:</strong> <?php echo htmlspecialchars($event['event_type_id']); ?></p>
                <p><strong>Number of Attendees:</strong> <?php echo htmlspecialchars($event['attendees']); ?></p>
            </div>
        </div>
        <a href="events_list.php" class="back-button">Back to List</a>
    </div>
</body>
</html>
