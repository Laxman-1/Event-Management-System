<?php
include '../Include/database.php';
include 'adminheader.php';
// Fetch events that have bookings
$sql = "SELECT eventdata.id, eventdata.event_name, eventdata.event_location
        FROM eventdata
        JOIN bookings ON eventdata.id = bookings.event_id
        GROUP BY eventdata.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Events with Bookings</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Styling from the first file -->
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
        .actions a {
            margin-right: 10px;
            padding: 5px 10px;
            color: #fff;
            border: none;
            border-radius: 4px;
            text-decoration: none;
        }
        .view {
            background-color: #4CAF50;
        }
        .view:hover {
            background-color: #45a049;
        }
        .btn {
            padding: 10px 15px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #1976D2;
        }
        .text-center {
            text-align: center;
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
        <h2>Events with Bookings</h2>
        <table>
            <thead>
                <tr>
                    <th>Event ID</th>
                    <th>Event Name</th>
                    <th>Event Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['event_name']}</td>
                                <td>{$row['event_location']}</td>
                                <td>
                                    <a href='viewbooking.php?id={$row['id']}' class='view'>View</a>
                                    <a href='send_message.php?id={$row['id']}' class='view'>Notice</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No events with bookings found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
$conn->close();
?>
