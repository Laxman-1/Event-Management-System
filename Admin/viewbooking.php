<?php
include '../Include/database.php';
include 'adminheader.php';
// Fetch bookings for the specific event
$eventId = $_GET['id']; // Get the event ID from the URL
$sql = "SELECT * FROM bookings WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

// Approve booking
if (isset($_POST['approve'])) {
    $bookingId = $_POST['booking_id'];
    $sql = "UPDATE bookings SET status = 'complete' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $stmt->close();
    header("Location: viewbooking.php?id=" . $eventId); // Redirect to the same page to refresh the bookings list
    exit();
}

// Delete booking
if (isset($_POST['delete'])) {
    $bookingId = $_POST['booking_id'];
    $sql = "DELETE FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $stmt->close();
    header("Location: viewbooking.php?id=" . $eventId); // Redirect to the same page to refresh the bookings list
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bookings Management</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/custom.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .actions a, .actions button {
            margin-right: 10px;
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }

        .btn-primary, .btn-success, .btn-danger {
            margin-right: 5px;
        }

        .btn-success {
            background-color: #4CAF50;
        }

        .btn-danger {
            background-color: #f44336;
        }

        .btn-primary {
            background-color: #2196F3;
        }

        .btn-primary:hover {
            background-color: #1976D2;
        }

        .btn-success:hover {
            background-color: #45a049;
        }

        .btn-danger:hover {
            background-color: #e53935;
        }

        .text-center {
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        .back-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 15px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
    <div class="dashboard-container">
        <header class="dashboard-header">
            <ul>  <h1>
                <li><a href="dashboard.php">Home</a></li>
            </ul></h1>
            <nav>
            </nav>
            <nav>
                <ul>
                 <li><a href="sample.php"> Back to Event List</a></li>
                    <li><a href="adminLogin.php">Logout</a></li>
                </ul>
            </nav>
        </header></div>
        
        <h2>Bookings for Event ID: <?php echo $eventId; ?></h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Invoice No</th>
                    <th>Total Attendees</th>
                    <th>Quantity</th>
                    <th>User ID</th>
                    <th>Normal Price</th>
                    <th>VIP Price</th>
                    <th>VVIP Price</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['invoice_no']}</td>
                                <td>{$row['total_attendees']}</td>
                                <td>{$row['Quantity']}</td>
                                <td>{$row['user_id']}</td>
                                <td>{$row['normal_price']}</td>
                                <td>{$row['vip_price']}</td>
                                <td>{$row['vvip_price']}</td>
                                <td>{$row['total']}</td>
                                <td>{$row['status']}</td>
                                <td>
                                    <form method='POST' action='' style='display:inline;'>
                                        <input type='hidden' name='booking_id' value='{$row['id']}'>
                                        <button type='submit' name='approve' class='btn btn-success'>Approve</button>
                                    </form>
                                    <form method='POST' action='' style='display:inline;'>
                                        <input type='hidden' name='booking_id' value='{$row['id']}'>
                                        <button type='submit' name='delete' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this booking?\");'>Delete</button>
                                    </form>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>
