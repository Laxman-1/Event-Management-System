<?php
session_start();

// Ensure user is logged in and has a valid session
//if (!isset($_SESSION['email']) || $_SESSION['usertype'] != 0) {
  //  header("Location: ../login.php");
    //exit();
//}


include '../Laxman/Include/database.php';


//fetch events in descending order
$sql = "SELECT id, event_name, event_location, event_image, average_ratings FROM eventdata ORDER BY id DESC";
$result = $conn->query($sql);

// Display all events
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/assets/css/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link rel="apple-touch-icon" href="assets/img/apple-icon.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    <!-- Load Required CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font CSS -->
    <link href="assets/css/boxicon.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Load Template CSS -->
    <link rel="stylesheet" href="assets/css/templatemo.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">
    <!-- Enhanced CSS for Events -->
    <style>
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #fff;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }
        .card-img-top {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            height: 250px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .card-img-top:hover {
            transform: scale(1.05);
        }
        .card-body {
            padding: 25px;
        }
        .card-title {
            color: #007bff;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .card-text {
            color: #6c757d;
            margin-bottom: 15px;
        }
        .view, .book {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .view {
            background-color: #007bff;
        }
        .view:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .book {
            background-color: #28a745;
        }
        .book:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .rating {
            margin-bottom: 10px;
        }
        .rating .star {
            font-size: 22px;
            color: #e0e0e0;
            display: inline-block;
            position: relative;
            line-height: 1;
            transition: color 0.3s ease;
        }
        .rating .star.filled {
            color: #ffca28;
        }
        .rating .star.half::before {
            content: '\2605'; /* Unicode character for filled star */
            position: absolute;
            top: 0;
            left: 0;
            color: #ffca28;
            width: 50%;
            overflow: hidden;
        }
        .rating-text {
            color: #6c757d;
        }
    </style>
</head>
<?php include 'header.php'; ?>

    <!-- Display All Events -->
    <div class="container">
        <h1 class="text-center mb-4">Available Events</h1>
        <div class="row">
            <?php
            if ($result === false || $result->num_rows === 0) {
                echo "<p>No events available at this time.</p>";
            } else {
                while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="uploads/<?php echo htmlspecialchars($row['event_image']); ?>" class="card-img-top" alt="Event Image">
                        <div class="card-body">
                            <div class="rating">
                                <?php
                                $rating = isset($row['average_ratings']) ? floatval($row['average_ratings']) : 0;
                                for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?php echo ($i <= $rating) ? 'filled' : (($i - 0.5) <= $rating ? 'half' : ''); ?>">&#9733;</span>
                                <?php endfor; ?>
                                <span class="rating-text">
                                    <?php echo $rating > 0 ? "Rated " . number_format($rating, 1) . " out of 5" : "No Ratings Yet"; ?>
                                    <a href="backend/rating.php?id=<?php echo htmlspecialchars($row['id']); ?>">provide rating and review</a>
                                </span>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($row['event_name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['event_location']); ?></p>
                            <a href="backend/viewEvent.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="view">View</a>
                            <a href="backend/bookevent.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="book">Book</a>
                        </div>
                    </div>
                </div>
                <?php endwhile;
            }
            ?>
        </div>
    </div>

    <!-- Load Required Scripts -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/custom.js"></script>
</body>
</html>

<?php
$conn->close();
?>
<?php include 'footer.php'; ?>
