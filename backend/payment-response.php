<?php
session_start();
include '../Include/database.php';  // Include your database connection



// Example payment data (replace with actual data retrieval logic)
$transaction_id = $_GET['transaction_id'];
$purchase_order_id = $_GET['purchase_order_id'] ?? 'PO78910';
$purchase_order_name = $_GET['purchase_order_name'] ?? 'Event 2024';
$status = $_GET['status'] ?? 'Success';
$amount = $_GET['amount'] ?? 10000;
$ticket_type = $_GET['ticket_type'] ?? '';
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 0;
$attendees = isset($_GET['attendees']) ? intval($_GET['attendees']) : 0;
$event_id = $_GET['purchase_order_name'] ?? 'Event 2024';
$user_id = $_SESSION['user_id'] ?? 1;

// Check if the event ID exists
$event_check_query = "SELECT * FROM eventdata WHERE id = ?";
$stmt = $conn->prepare($event_check_query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Error: Event ID does not exist in the database.");
}

// Fetch the event data (prices)
$event_data = $result->fetch_assoc();
$normal_price = $event_data['normal_price'];
$vip_price = $event_data['vip_price'];
$vvip_price = $event_data['vvip_price'];

// Handle form submission to insert data into the bookings table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_no = uniqid('INV');
    $total = $amount / 100;

    // Prepare SQL query to insert booking data
    $sql = "INSERT INTO bookings (invoice_no, transaction_id, total_attendees, Quantity, event_id, user_id, normal_price, vip_price, vvip_price, total, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'incomplete')";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiissddds", $invoice_no, $transaction_id, $attendees, $quantity, $event_id, $user_id, $normal_price, $vip_price, $vvip_price, $total);

    if ($stmt->execute()) {
        echo "<script>alert('Payment data successfully inserted into bookings.');</script>";
        header('Location: message.php');
        exit();
    } else {
        echo "<script>alert('Error inserting payment data: " . $stmt->error . "');</script>";
    }
}

// Prepare payment data as an array
$paymentData = [
    'transaction_id' => $transaction_id,
    'purchase_order_id' => $purchase_order_id,
    'purchase_order_name' => $purchase_order_name,
    'status' => $status,
    'amount' => $amount,
    'ticket_type' => $ticket_type,
    'quantity' => $quantity,
    'event_id' => $event_id,
    'user_id' => $user_id,
    'attendees' => $attendees
];

// Encode payment data as JSON
$jsonPaymentData = json_encode($paymentData);
?>


<!-- HTML content remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment Response</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        body {
            background-color: #f9fafb;
        }
        .payment-card {
            max-width: 800px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: left;
        }
        .payment-card h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .payment-card p {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        .payment-card .total {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .payment-card button {
            background-color: #3b82f6;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .payment-card button:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <h1>Khalti Payment Response</h1>
        <!-- Display payment information -->
        <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($transaction_id); ?></p>
        <p><strong>Purchase Order ID:</strong> <?php echo htmlspecialchars($purchase_order_id); ?></p>
        <p><strong>Purchase Order Name (Event Name):</strong> <?php echo htmlspecialchars($purchase_order_name); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
        <p><strong>Amount:</strong> Rs <?php echo number_format($amount / 100, 2); ?></p>
        <p><strong>Ticket Type:</strong> <?php echo htmlspecialchars($ticket_type); ?></p>
        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($quantity); ?></p>
        <p><strong>Attendees:</strong> <?php echo htmlspecialchars($attendees); ?></p> <!-- Display the number of attendees -->
        <p><strong>Event ID:</strong> <?php echo intval($event_id); ?></p>
        <p><strong>User ID:</strong> <?php echo intval($user_id); ?></p>

        <!-- Form to send payment data for approval -->
        <form method="POST">
            <button type="submit">Send for Approval</button>
        </form>
<br>
        
        <button onclick="window.print()">Print Invoice</button>
    </div>
</body>
</html>
