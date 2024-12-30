<?php
session_start(); 

include 'adminheader.php';
// Check if the user is authenticated and approved
if (!isset($_SESSION['user_id']) || $_SESSION['user_status'] !== 'approved') {
    header("Location: orglogin.php"); 
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'event');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = intval($_POST['id']);
    $event_name = $_POST['event_name'];
    $event_location = $_POST['event_location'];
    $event_url = $_POST['event_url'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $normal_price = $_POST['normal_price'];
    $vip_price = $_POST['vip_price'];
    $vvip_price = $_POST['vvip_price'];
    $additional_details = $_POST['additional_details'];
    $category_id = $_POST['category_id'];
    $event_type_id = $_POST['event_type_id'];
    $attendees = intval($_POST['attendees']); // Handle number of attendees

    // Validate event ID
    if (!is_numeric($event_id) || $event_id <= 0) {
        echo "<p class='text-red-500'>Invalid event ID.</p>";
        exit();
    }

    // Handle file upload (only update if a new file is uploaded)
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $tmp_name = $_FILES['event_image']['tmp_name'];
        $file_name = basename($_FILES['event_image']['name']);
        $upload_file = $upload_dir . $file_name;

        if (move_uploaded_file($tmp_name, $upload_file)) {
            $event_image = $file_name;

            // Update the event with a new image
            $stmt = $conn->prepare("UPDATE eventdata SET event_name = ?, event_location = ?, event_url = ?, start_date = ?, end_date = ?, start_time = ?, end_time = ?, event_image = ?, normal_price = ?, vip_price = ?, vvip_price = ?, additional_details = ?, category_id = ?, event_type_id = ?, attendees = ? WHERE id = ?");
            $stmt->bind_param('ssssssssdddsiiii', $event_name, $event_location, $event_url, $start_date, $end_date, $start_time, $end_time, $event_image, $normal_price, $vip_price, $vvip_price, $additional_details, $category_id, $event_type_id, $attendees, $event_id);
        }
    } else {
        // Update the event without changing the image
        $stmt = $conn->prepare("UPDATE eventdata SET event_name = ?, event_location = ?, event_url = ?, start_date = ?, end_date = ?, start_time = ?, end_time = ?, normal_price = ?, vip_price = ?, vvip_price = ?, additional_details = ?, category_id = ?, event_type_id = ?, attendees = ? WHERE id = ?");
        $stmt->bind_param('ssssssssdddsiii', $event_name, $event_location, $event_url, $start_date, $end_date, $start_time, $end_time, $normal_price, $vip_price, $vvip_price, $additional_details, $category_id, $event_type_id, $attendees, $event_id);
    }

    if ($stmt->execute()) {
        echo "<p class='text-green-500'>Event updated successfully.</p>";
    } else {
        echo "<p class='text-red-500'>Error updating event: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Check if 'id' is set in the URL and is a valid number
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid event ID.");
}

// Retrieve event details for the form
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

// Fetch categories from the database
$category_stmt = $conn->prepare("SELECT id, category_name FROM category");
$category_stmt->execute();
$categories_result = $category_stmt->get_result();
$categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $categories[] = $row;
}
$category_stmt->close();

// Fetch event types from the database
$type_stmt = $conn->prepare("SELECT t_id, type_name FROM event_type");
$type_stmt->execute();
$event_types_result = $type_stmt->get_result();
$event_types = [];
while ($row = $event_types_result->fetch_assoc()) {
    $event_types[] = $row;
}
$type_stmt->close();

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto max-w-2xl p-6 bg-white rounded-lg shadow-md mt-10">
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
        <h1 class="text-2xl font-bold mb-6">Update Event</h1>
        <form action="update_event.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id']); ?>">

            <div class="mb-4">
                <label class="block text-gray-700" for="event_name">Event Name:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="text" id="event_name" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="event_location">Location:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="text" id="event_location" name="event_location" value="<?php echo htmlspecialchars($event['event_location']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="event_url">Event URL:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="text" id="event_url" name="event_url" value="<?php echo htmlspecialchars($event['event_url']); ?>">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="start_date">Start Date:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="date" id="start_date" name="start_date" value="<?php echo htmlspecialchars($event['start_date']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="end_date">End Date:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="date" id="end_date" name="end_date" value="<?php echo htmlspecialchars($event['end_date']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="start_time">Start Time:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="time" id="start_time" name="start_time" value="<?php echo htmlspecialchars($event['start_time']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="end_time">End Time:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="time" id="end_time" name="end_time" value="<?php echo htmlspecialchars($event['end_time']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="event_image">Event Image:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="file" id="event_image" name="event_image">
                <?php if (!empty($event['event_image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($event['event_image']); ?>" alt="Event Image" class="mt-4">
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="normal_price">Normal Price:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="number" step="0.01" id="normal_price" name="normal_price" value="<?php echo htmlspecialchars($event['normal_price']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="vip_price">VIP Price:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="number" step="0.01" id="vip_price" name="vip_price" value="<?php echo htmlspecialchars($event['vip_price']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="vvip_price">VVIP Price:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="number" step="0.01" id="vvip_price" name="vvip_price" value="<?php echo htmlspecialchars($event['vvip_price']); ?>" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="additional_details">Additional Details:</label>
                <textarea class="mt-1 block w-full border border-gray-300 rounded-md p-2" id="additional_details" name="additional_details" rows="4"><?php echo htmlspecialchars($event['additional_details']); ?></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="category_id">Category:</label>
                <select class="mt-1 block w-full border border-gray-300 rounded-md p-2" id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php if ($category['id'] == $event['category_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($category['category_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="event_type_id">Event Type:</label>
                <select class="mt-1 block w-full border border-gray-300 rounded-md p-2" id="event_type_id" name="event_type_id" required>
                    <?php foreach ($event_types as $type): ?>
                        <option value="<?php echo htmlspecialchars($type['t_id']); ?>" <?php if ($type['t_id'] == $event['event_type_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($type['type_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700" for="attendees">Number of Attendees:</label>
                <input class="mt-1 block w-full border border-gray-300 rounded-md p-2" type="number" id="attendees" name="attendees" value="<?php echo htmlspecialchars($event['attendees']); ?>" required>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Update Event</button>
            <a href="events_list.php" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Back to List</a>
        </form>
    </div>
</body>
</html>
