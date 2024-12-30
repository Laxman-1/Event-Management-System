<?php
session_start(); 
include '../Include/database.php'; 
include 'adminheader.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #eef2f3;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
            font-size: 2.5em;
        }

        .input-group {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }

        .input-box {
            flex: 1 1 calc(50% - 20px);
            margin-right: 20px;
            margin-bottom: 15px;
        }

        .input-box:last-child {
            margin-right: 0;
        }

        .input-box label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        .input-box input,
        .input-box select,
        .input-box textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        .input-box input:focus,
        .input-box select:focus,
        .input-box textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        .input-box button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        .input-box button:hover {
            background-color: #0056b3;
        }

        .back-button {
            background-color: #6c757d;
            margin-top: 20px;
            text-align: center;
            display: block;
            padding: 10px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <section class="container">
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
        <h1>Create Event</h1>
        <form action="submit_form.php" method="POST" enctype="multipart/form-data" class="form">
            <div class="input-group">
                <div class="input-box">
                    <label>Name of Event</label>
                    <input type="text" name="event_name" placeholder="Enter Event Name" required>
                </div>
                <div class="input-box">
                    <label>Event Location</label>
                    <input type="text" name="event_location" placeholder="Enter Location of Event" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label>Number of Attendees</label>
                    <input type="number" name="num_attendees" min="0" value="0">

                </div>
                <div class="input-box">
                    <label>Start Date</label>
                    <input type="date" name="start_date" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label>End Date</label>
                    <input type="date" name="end_date" required>
                </div>
                <div class="input-box">
                    <label>Start Time</label>
                    <input type="time" name="start_time" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label>End Time</label>
                    <input type="time" name="end_time" required>
                </div>
                <div class="input-box">
                    <label for="event-image">Image of Event</label>
                    <input type="file" id="event-image" name="event_image" accept="image/*" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label>Normal Price</label>
                    <input type="number" name="normal_price" placeholder="Enter Normal Price (*optional)" step="0.01">
                </div>
                <div class="input-box">
                    <label>VIP Price</label>
                    <input type="number" name="vip_price" placeholder="Enter VIP Price (*optional)" step="0.01">
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label>VVIP Price</label>
                    <input type="number" name="vvip_price" placeholder="Enter VVIP Price (*optional)" step="0.01">
                </div>
                <div class="input-box">
                    <label>Additional Details</label>
                    <textarea name="additional_details" placeholder="Enter details" required></textarea>
                </div>
            </div>

            <div class="input-box">
                <label>Event Category</label>
                <select name="category_id" required>
                    <option value="" hidden>Select Category</option>
                    <?php
                    $category_result = $conn->query("SELECT id, category_name FROM category");
                    if ($category_result->num_rows > 0) {
                        while ($category = $category_result->fetch_assoc()) {
                            echo "<option value='{$category['id']}'>{$category['category_name']}</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No Categories Available</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="input-box">
                <label>Event Type</label>
                <select name="event_type_id" required>
                    <option hidden>Select Event Type</option>
                    <?php
                    $event_type_result = $conn->query("SELECT t_id, type_name FROM event_type");
                    while ($event_type = $event_type_result->fetch_assoc()) {
                        echo "<option value='{$event_type['t_id']}'>{$event_type['type_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <?php
            $organizer_id = $_SESSION['user_id'];
            $organizer_query = $conn->prepare("SELECT name, email, phoneno FROM organizer WHERE id = ?");
            $organizer_query->bind_param("i", $organizer_id);
            $organizer_query->execute();
            $organizer_result = $organizer_query->get_result();
            $organizer = $organizer_result->fetch_assoc();

            $organizer_name = isset($organizer['name']) ? $organizer['name'] : '';
            $organizer_email = isset($organizer['email']) ? $organizer['email'] : '';
            $organizer_phone = isset($organizer['phoneno']) ? $organizer['phoneno'] : '';
            ?>

            <input type="hidden" name="organizer_id" value="<?php echo htmlspecialchars($organizer_id); ?>">

            <h2>Event Organizer</h2>
            <div class="input-group">
                <div class="input-box">
                    <label>Organizer Name</label>
                    <input type="text" name="organizer_name" value="<?php echo htmlspecialchars($organizer_name); ?>" placeholder="Enter Organizer Name" required>
                </div>
                <div class="input-box">
                    <label>Organizer Email</label>
                    <input type="email" name="organizer_email" value="<?php echo htmlspecialchars($organizer_email); ?>" placeholder="Enter Organizer Email" required>
                </div>
            </div>

            <div class="input-group">
                <div class="input-box">
                    <label>Organizer Phone Number</label>
                    <input type="text" name="organizer_phone" value="<?php echo htmlspecialchars($organizer_phone); ?>" placeholder="Enter Organizer Phone Number" required>
                </div>
                <div class="input-box">
                    <button type="submit">Submit</button>
                </div>
            </div>
        </form>
        <a href="dashboard.php" class="back-button">Back</a>
    </section>
</body>
</html>
