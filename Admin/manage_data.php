<?php
include '../Include/database.php';

// Handle Category Operations
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO category (category_name) VALUES (?)");
        $stmt->bind_param("s", $category_name);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];
    $stmt = $conn->prepare("DELETE FROM category WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $new_category_name = $_POST['new_category_name'];
    if (!empty($new_category_name)) {
        $stmt = $conn->prepare("UPDATE category SET category_name = ? WHERE id = ?");
        $stmt->bind_param("si", $new_category_name, $category_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle Event Type Operations
if (isset($_POST['add_event_type'])) {
    $type_name = $_POST['type_name'];
    if (!empty($type_name)) {
        $stmt = $conn->prepare("INSERT INTO event_type (type_name) VALUES (?)");
        $stmt->bind_param("s", $type_name);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['delete_event_type'])) {
    $event_type_id = $_POST['event_type_id'];
    $stmt = $conn->prepare("DELETE FROM event_type WHERE t_id = ?");
    $stmt->bind_param("i", $event_type_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update_event_type'])) {
    $event_type_id = $_POST['event_type_id'];
    $new_event_type_name = $_POST['new_event_type_name'];
    if (!empty($new_event_type_name)) {
        $stmt = $conn->prepare("UPDATE event_type SET type_name = ? WHERE t_id = ?");
        $stmt->bind_param("si", $new_event_type_name, $event_type_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle Event Location Operations
if (isset($_POST['add_event_location'])) {
    $location_name = $_POST['location_name'];
    if (!empty($location_name)) {
        $stmt = $conn->prepare("INSERT INTO event_location (location_name) VALUES (?)");
        $stmt->bind_param("s", $location_name);
        $stmt->execute();
        $stmt->close();
    }
}

if (isset($_POST['delete_event_location'])) {
    $event_location_id = $_POST['event_location_id'];
    $stmt = $conn->prepare("DELETE FROM event_location WHERE l_id = ?");
    $stmt->bind_param("i", $event_location_id);
    $stmt->execute();
    $stmt->close();
}

if (isset($_POST['update_event_location'])) {
    $event_location_id = $_POST['event_location_id'];
    $new_event_location_name = $_POST['new_event_location_name'];
    if (!empty($new_event_location_name)) {
        $stmt = $conn->prepare("UPDATE event_location SET location_name = ? WHERE l_id = ?");
        $stmt->bind_param("si", $new_event_location_name, $event_location_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch data for displaying
$categories = $conn->query("SELECT id, category_name FROM category");
$event_types = $conn->query("SELECT t_id, type_name FROM event_type");
$event_locations = $conn->query("SELECT l_id, location_name FROM event_location");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 40px;
        }

        .input-box {
            margin-bottom: 15px;
        }

        .input-box label {
            display: block;
            margin-bottom: 5px;
        }

        .input-box input, .input-box select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .input-box button {
            background-color: #04AA6D;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .input-box button:hover {
            background-color: #039f5a;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .data-table th, .data-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .data-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<a href="dashboard.php" class="btn btn-primary">Homepage</a>

    <div class="container">
        <h1>Manage Data</h1>

        <!-- Category Section -->
        <div class="section">
            <h2>Manage Categories</h2>
            <form action="manage_data.php" method="POST">
                <div class="input-box">
                    <label>Category Name</label>
                    <input type="text" name="category_name" placeholder="Enter Category Name">
                </div>
                <div class="input-box">
                    <button type="submit" name="add_category">Add Category</button>
                </div>
            </form>
            <h3>Existing Categories</h3>
            <table class="data-table">
                <tr>
                    <th>ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $categories->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['category_name']; ?></td>
                    <td>
                        <form action="manage_data.php" method="POST" style="display:inline;">
                            <input type="hidden" name="category_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_category">Delete</button>
                        </form>
                        <form action="manage_data.php" method="POST" style="display:inline;">
                            <input type="hidden" name="category_id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="new_category_name" placeholder="New Name">
                            <button type="submit" name="update_category">Update</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Event Type Section -->
        <div class="section">
            <h2>Manage Event Types</h2>
            <form action="manage_data.php" method="POST">
                <div class="input-box">
                    <label>Event Type Name</label>
                    <input type="text" name="type_name" placeholder="Enter Event Type Name">
                </div>
                <div class="input-box">
                    <button type="submit" name="add_event_type">Add Event Type</button>
                </div>
            </form>
            <h3>Existing Event Types</h3>
            <table class="data-table">
                <tr>
                    <th>ID</th>
                    <th>Event Type Name</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $event_types->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['t_id']; ?></td>
                    <td><?php echo $row['type_name']; ?></td>
                    <td>
                        <form action="manage_data.php" method="POST" style="display:inline;">
                            <input type="hidden" name="event_type_id" value="<?php echo $row['t_id']; ?>">
                            <button type="submit" name="delete_event_type">Delete</button>
                        </form>
                        <form action="manage_data.php" method="POST" style="display:inline;">
                            <input type="hidden" name="event_type_id" value="<?php echo $row['t_id']; ?>">
                            <input type="text" name="new_event_type_name" placeholder="New Name">
                            <button type="submit" name="update_event_type">Update</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <!-- Event Location Section -->
        <div class="section">
            <h2>Manage Event Locations</h2>
            <form action="manage_data.php" method="POST">
                <div class="input-box">
                    <label>Event Location Name</label>
                    <input type="text" name="location_name" placeholder="Enter Event Location Name">
                </div>
                <div class="input-box">
                    <button type="submit" name="add_event_location">Add Event Location</button>
                </div>
            </form>
            <h3>Existing Event Locations</h3>
            <table class="data-table">
                <tr>
                    <th>ID</th>
                    <th>Event Location Name</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $event_locations->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['l_id']; ?></td>
                    <td><?php echo $row['location_name']; ?></td>
                    <td>
                        <form action="manage_data.php" method="POST" style="display:inline;">
                            <input type="hidden" name="event_location_id" value="<?php echo $row['l_id']; ?>">
                            <button type="submit" name="delete_event_location">Delete</button>
                        </form>
                        <form action="manage_data.php" method="POST" style="display:inline;">
                            <input type="hidden" name="event_location_id" value="<?php echo $row['l_id']; ?>">
                            <input type="text" name="new_event_location_name" placeholder="New Name">
                            <button type="submit" name="update_event_location">Update</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
