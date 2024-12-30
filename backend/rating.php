<?php
session_start();
include '../Include/database.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['email']) || $_SESSION['usertype'] != 0) {
    header("Location: ../login.php");
    exit();
}

// Initialize event_id
$event_id = 0;
$user_has_rated = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get event_id from POST data
    if (isset($_POST['event_id'])) {
        $event_id = intval($_POST['event_id']);
    }

    // Get the form data
    $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : 0;
    $review = isset($_POST['review']) ? trim($_POST['review']) : '';

    // Validation checks
    if ($event_id <= 0) {
        die("Invalid Event ID.");
    }
    if ($rating < 1 || $rating > 5) {
        die("Rating is not valid.");
    }
    if (empty($review)) {
        die("Review cannot be empty.");
    }

    // Check if user has already rated this event
    $check_rating_stmt = $conn->prepare("SELECT COUNT(*) FROM event_ratings WHERE event_id = ? AND user_id = ?");
    $check_rating_stmt->bind_param("ii", $event_id, $_SESSION['user_id']);
    $check_rating_stmt->execute();
    $user_has_rated = $check_rating_stmt->get_result()->fetch_row()[0] > 0;
    $check_rating_stmt->close();

    if ($user_has_rated) {
        die("You have already rated this event.");
    }

    // Insert review into the database
    $stmt = $conn->prepare("INSERT INTO event_ratings (event_id, user_id, rating, review, DateTime) VALUES (?, ?, ?, ?, NOW())");

    // Bind parameters: i - integer, d - double, s - string
    $stmt->bind_param("iids", $event_id, $_SESSION['user_id'], $rating, $review);
    $stmt->execute(); // Execute the statement
    
    $stmt->close();
    
    // Calculate the new average rating
    $avg_rating_stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM event_ratings WHERE event_id = ?");
    $avg_rating_stmt->bind_param("i", $event_id);
    $avg_rating_stmt->execute();
    $avg_rating_result = $avg_rating_stmt->get_result();
    $avg_rating = $avg_rating_result->fetch_assoc()['avg_rating'];
    $avg_rating_stmt->close();

    // Update the average rating in the eventdata table
    $update_avg_stmt = $conn->prepare("UPDATE eventdata SET average_ratings = ? WHERE id = ?");
    $update_avg_stmt->bind_param("di", $avg_rating, $event_id);
    $update_avg_stmt->execute();
    $update_avg_stmt->close();

    echo "Rating and review added successfully!";
}
// Get event_id from URL (GET) if not set by POST
if ($event_id <= 0 && isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
}

// Validate event_id
if ($event_id <= 0) {
    die("Invalid Event ID.");
}

// Check if event exists
$event_check_stmt = $conn->prepare("SELECT COUNT(*) FROM eventdata WHERE id = ?");
$event_check_stmt->bind_param("i", $event_id);
$event_check_stmt->execute();
$result = $event_check_stmt->get_result()->fetch_row();
$event_exists = $result[0] > 0;
$event_check_stmt->close();

if (!$event_exists) {
    die("Event ID does not exist.");
}

// Fetch event details
$event_stmt = $conn->prepare("SELECT * FROM eventdata WHERE id = ?");
$event_stmt->bind_param("i", $event_id);
$event_stmt->execute();
$event = $event_stmt->get_result()->fetch_assoc();
$event_stmt->close();

// Fetch existing reviews along with user emails
$reviews_stmt = $conn->prepare("
    SELECT er.*, u.email AS reviewer_email 
    FROM event_ratings er 
    JOIN user u ON er.user_id = u.id 
    WHERE er.event_id = ?");
$reviews_stmt->bind_param("i", $event_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$reviews_stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/rating.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <style>
        /* Your existing CSS styles */
    </style>
</head>
<body>
<? include '../header.php';?>
    <div class="container">
        <h1 class="text-center my-4">Event Details</h1>
        <div class="event-details">
            <div class="event-image">
                <p><strong>Event Image:</strong></p>
                <img src="../uploads/<?php echo htmlspecialchars($event['event_image']); ?>" alt="Event Image">
            </div>
            <div class="event-info">
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if ($user_has_rated): ?>
                        <p>You have already rated this event.</p>
                    <?php else: ?>
                        <div class="rateyo" id="rating"></div>
                        <input type="hidden" name="rating" id="rating-value">
                        <textarea name="review" rows="4" cols="50" placeholder="Write your review here..." required></textarea>
                        <div class="submit-rating mt-3">
                            <input type="submit" name="add" value="Submit Rating" class="btn btn-primary">
                        </div>
                    <?php endif; ?>
                </form>
                <div class="review-container">
                    <h3>Reviews</h3>
                    <?php if (empty($reviews)): ?>
                        <p>No reviews yet.</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review">
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($review['reviewer_email']); ?></p>
                                <p><strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?></p>
                                <p><strong>Review:</strong> <?php echo htmlspecialchars($review['review']); ?></p>
                                <p><strong>Date:</strong> <?php echo htmlspecialchars($review['DateTime']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <a href="../event.php" class="back-button">Back to List</a>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>

    <script>
        $(function () {
            $(".rateyo").rateYo({
                starWidth: "30px",
                spacing: "5px",
                normalFill: "#A0A0A0",
                ratedFill: "#F39C12"
            }).on("rateyo.change", function (e, data) {
                var rating = data.rating;
                $(this).parent().find('#rating-value').val(rating); // Update the hidden input field
            });
        });
    </script>
</body>
</html>
