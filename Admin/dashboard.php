<?php
session_start(); // Start the session

// Check if the user is authenticated and approved
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] !== 'approved') {
    header("Location: orglogin.php"); // Redirect to login page if not authenticated or not approved
    exit();
}

// User is authenticated and approved, show the dashboard content
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 28px;
        }

        .dashboard-header nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .dashboard-header nav ul li {
            margin-left: 20px;
        }

        .dashboard-header nav ul li a {
            color: white;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .dashboard-header nav ul li a:hover {
            color: #ffc107;
        }

        .dashboard-main {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 20px;
        }

        .dashboard-section {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex: 1 1 calc(33% - 20px);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .dashboard-section:hover {
            transform: translateY(-5px);
        }

        .dashboard-section h2 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #343a40;
        }

        .dashboard-section p {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .dashboard-section .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .dashboard-section .btn:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .dashboard-section {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <nav>
                <ul>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </header>

        <main class="dashboard-main">
            <section class="dashboard-section">
                <h2>Create Events</h2>
                <p>Create new events.</p>
                <a href="events.php" class="btn">Create Events</a>
            </section>

            <section class="dashboard-section">
                <h2>Manage Events</h2>
                <p>View and manage all the events created.</p>
                <a href="events_list.php" class="btn">View Events</a>
            </section>

         

            <section class="dashboard-section">
                <h2>(category and type)</h2>
                <p>Manage Event information(category and type)</p>
                <a href="categoryType.php" class="btn">Manage category and eventype</a>
            </section>

           

            <section class="dashboard-section">
                <h2>Manage Bookings</h2>
                <p>Manage hall bookings.</p>
                <a href="sample.php" class="btn">Manage Bookings</a>
            </section>

        </main>
    </div>
</body>
</html>
