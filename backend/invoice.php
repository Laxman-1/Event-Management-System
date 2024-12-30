<?php
session_start(); // Start the session

// Include database connection
include '../Include/database.php'; // Ensure you have a proper database connection file

// Retrieve booking ID from URL
$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;

// Initialize variables
$bookingDetails = [];

// Check if booking ID is valid and fetch booking details
if ($booking_id > 0) {
    $sql = "SELECT b.id, b.invoice_no, b.total, b.normal_price, b.vip_price, b.vvip_price, b.created_at, b.status, e.event_name, e.event_location, e.event_image
            FROM bookings b
            JOIN eventdata e ON b.event_id = e.id
            WHERE b.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $bookingResult = $stmt->get_result();
    $bookingDetails = $bookingResult->fetch_assoc();
    $stmt->close();
}

if (!$bookingDetails) {
    echo "<p>Invalid booking ID. Please check the URL or contact support.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Invoice</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        body {
            background-color: #f9fafb;
        }

        .invoice-card {
            max-width: 800px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: left;
        }

        .invoice-card h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .invoice-card p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }

        .invoice-card .total {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .invoice-card button {
            background-color: #3b82f6;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .invoice-card button:hover {
            background-color: #2563eb;
        }
    </style>
</head>

<body>
    <div class="invoice-card">
        <h1>Booking Invoice</h1>
        <p><strong>Invoice Number:</strong> <?php echo htmlspecialchars($bookingDetails['invoice_no']); ?></p>
        <p><strong>Event Name:</strong> <?php echo htmlspecialchars($bookingDetails['event_name']); ?></p>
        <p><strong>Event Location:</strong> <?php echo htmlspecialchars($bookingDetails['event_location']); ?></p>
        <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($bookingDetails['created_at']); ?></p>
        <p><strong>Status:</strong> <?php echo $bookingDetails['status'] == 0 ? 'Pending' : 'Confirmed'; ?></p>

        <p><strong>Normal Price:</strong> Rs <?php echo htmlspecialchars($bookingDetails['normal_price']); ?></p>
        <p><strong>VIP Price:</strong> Rs <?php echo htmlspecialchars($bookingDetails['vip_price']); ?></p>
        <p><strong>VVIP Price:</strong> Rs <?php echo htmlspecialchars($bookingDetails['vvip_price']); ?></p>
        
        <p class="total"><strong>Total Amount:</strong> Rs <?php echo htmlspecialchars($bookingDetails['total']); ?></p>
        
        <button onclick="window.print()">Print Invoice</button>
    </div>
   
</body>

</html>
