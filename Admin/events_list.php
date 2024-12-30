<?php

session_start(); 
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] !== 'approved') {
    header("Location: orglogin.php"); 
    exit();
}
include 'adminheader.php';
// Database connection
$conn = new mysqli('localhost', 'root', '', 'event');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM eventdata WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        echo "<p>Event deleted successfully.</p>";
    } else {
        echo "<p>Error deleting event: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Retrieve events
$result = $conn->query("SELECT * FROM eventdata");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
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
            position: relative; /* Added for positioning the back button */
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
        .update {
            background-color: #2196F3;
        }
        .delete {
            background-color: #f44336;
        }
        .view:hover {
            background-color: #45a049;
        }
        .update:hover {
            background-color: #1976D2;
        }
        .delete:hover {
            background-color: #e53935;
        }
        .back-button {
            position: absolute; /* Positioning the back button */
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
       
        <h1>Event List</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Location</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['event_location']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                    <td class="actions">
                        <a href="eventview.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="view">View</a>
                        <a href="update_event.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="update">Update</a>
                        <a href="?delete_id=<?php echo htmlspecialchars($row['id']); ?>" class="delete" onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
