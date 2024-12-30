<?php
// approve.php

// Get the raw POST data
$data = file_get_contents("php://input");

// Decode the JSON data into a PHP array
$paymentData = json_decode($data, true);

// Initialize a response variable
$response = ['status' => 'error', 'message' => 'No data received.'];

// Check if the data is received correctly
if ($paymentData) {
    // Here, you can access the payment data using keys
    $transaction_id = $paymentData['transaction_id'];
    $purchase_order_id = $paymentData['purchase_order_id'];
    $amount = $paymentData['amount'];
    $ticket_type = $paymentData['ticket_type'];
    $quantity = $paymentData['quantity'];
    $event_id = $paymentData['event_id'];
    $user_id = $paymentData['user_id'];

    // Process the payment data (e.g., store it in the database)
    // Example: insert into database or update records as needed

    // Prepare the response message
    $response = ['status' => 'success', 'message' => 'Payment data processed successfully.'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Data Approval</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        body {
            background-color: #f9fafb;
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #3b82f6;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment Data Approval</h1>

        <?php if ($response['status'] === 'success'): ?>
            <p><strong><?php echo htmlspecialchars($response['message']); ?></strong></p>
            <table>
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Purchase Order ID</th>
                        <th>Amount (Rs)</th>
                        <th>Ticket Type</th>
                        <th>Quantity</th>
                        <th>Event ID</th>
                        <th>User ID</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($transaction_id); ?></td>
                        <td><?php echo htmlspecialchars($purchase_order_id); ?></td>
                        <td><?php echo number_format($amount / 100, 2); ?></td> <!-- Convert amount from paisa to rupees -->
                        <td><?php echo htmlspecialchars($ticket_type); ?></td>
                        <td><?php echo intval($quantity); ?></td>
                        <td><?php echo intval($event_id); ?></td>
                        <td><?php echo intval($user_id); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-red-600"><?php echo htmlspecialchars($response['message']); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
