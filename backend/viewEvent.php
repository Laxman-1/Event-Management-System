<?php
session_start(); // Start the session

// Check if the user is logged in and has the correct user type
if (isset($_SESSION['email']) && $_SESSION['usertype'] == 0) {
    
    $user_email = $_SESSION['email'];
   
} else {
    
    $session_error = "You are not logged in. Please log in to access all features.";
}
include '../Include/database.php'; 
include 'header.php';

// get event_id from the URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the event details along with organizer details
$sql = "SELECT e.*, o.name as organizer_name, o.phoneno as organizer_phone, o.email as organizer_email 
        FROM eventdata e 
        JOIN organizer o ON e.organizer_id = o.id 
        WHERE e.id = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$eventResult = $stmt->get_result();
$eventDetails = $eventResult->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($eventDetails['event_name']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
   
    <style>
        .hero {
            background-image: url('path/to/your/hero-image.jpg');
            background-size: cover;
            background-position: center;
        }
        .fade-in {
            animation: fadeIn 2s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .slide-up {
            animation: slideUp 1s ease-in-out;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .event-image {
            max-width: 100%;
            height: auto;
            max-height: 400px; /* Increase max height for larger images */
            object-fit: cover; /* Ensure the image covers the container without distortion */
            border-radius: 10px;
        }
        .event-data {
            background-color: #f7fafc; /* Slightly lighter background for event data sections */
            border: 1px solid #e2e8f0; /* Add subtle border */
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body class="bg-gray-100">
   
    <div class="container mx-auto p-6">
        <div class="bg-white shadow-lg rounded-lg p-6 fade-in">
            <h1 class="text-4xl font-bold text-gray-800"><?php echo htmlspecialchars($eventDetails['event_name']); ?></h1>
            <p class="text-gray-600 text-lg"><?php echo htmlspecialchars($eventDetails['event_location']); ?></p>
            <div class="flex flex-col md:flex-row mt-4">
                <div class="md:w-2/3">
                    <img src="../uploads/<?php echo htmlspecialchars($eventDetails['event_image']); ?>" alt="<?php echo htmlspecialchars($eventDetails['event_name']); ?>" class="event-image slide-up">
                    
                </div>
                <div class="md:w-1/3 md:pl-4">
                    <div class="event-data">
                        <h2 class="text-2xl font-semibold text-gray-800">Start and Ending Details</h2>
    
                        <div class="flex items-center mt-2">
                        <p class="text-lg text-gray-800 mr-4">Start Date: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['start_date']); ?></span></p>
                         <p class="text-lg text-gray-800">End Date: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['end_date']); ?></span></p>
                        </div>
                        <div class="flex items-center mt-1">
                         <p class="text-lg text-gray-800 mr-4">Start Time: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['start_time']); ?></span></p>
                        <p class="text-lg text-gray-800">End Time: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['end_time']); ?></span></p>
                        </div>
                        <br>
                        <h2 class="text-2xl font-semibold text-gray-800">Ticket Prices</h2>
                        <p class="text-lg text-gray-800 mt-2">Normal Price: <span class="font-semibold">Rs<?php echo htmlspecialchars($eventDetails['normal_price']); ?></span></p>
                        <p class="text-lg text-gray-800 mt-1">VIP Price: <span class="font-semibold">Rs<?php echo htmlspecialchars($eventDetails['vip_price']); ?></span></p>
                        <p class="text-lg text-gray-800 mt-1">VVIP Price: <span class="font-semibold">Rs<?php echo htmlspecialchars($eventDetails['vvip_price']); ?></span></p><br>
                        <h2 class="text-2xl font-semibold text-gray-800">Number of Attendees</h2>
                        <p class="text-lg text-gray-800 mt-2">Attendees: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['attendees']); ?></span></p><br>
                        <h2 class="text-2xl font-semibold text-gray-800">Event Description</h2>
                        <p class="text-lg text-gray-800 mt-1">Details: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['additional_details']); ?></span></p><br>
                        <h2 class="text-2xl font-semibold text-gray-800">Organizer Details</h2>
                        <p class="text-lg text-gray-800 mt-2">Name: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['organizer_name']); ?></span></p>
                        <p class="text-lg text-gray-800 mt-1">Phone: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['organizer_phone']); ?></span></p>
                        <p class="text-lg text-gray-800 mt-1">Email: <span class="font-semibold"><?php echo htmlspecialchars($eventDetails['organizer_email']); ?></span></p>
                      
                    </div>
                   
                       
                </div>
            </div>
        </div>
    </div>
    

            <?php
            // Function to calculate cosine similarity between two vectors
            function cosineSimilarity($vectorA, $vectorB) {
                $dotProduct = 0;
                $normA = 0;
                $normB = 0;

                foreach ($vectorA as $key => $valueA) {
                    $valueB = isset($vectorB[$key]) ? $vectorB[$key] : 0;
                    $dotProduct += $valueA * $valueB;
                    $normA += $valueA * $valueA;
                    $normB += $valueB * $valueB;
                }

                if ($normA == 0 || $normB == 0) return 0;

                return $dotProduct / (sqrt($normA) * sqrt($normB));
            }

            // Check if user_id is set in the session
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id']; // Get the user ID from session or authentication

                // Fetch user ratings for content-based filtering
                $sql = "SELECT event_id, rating FROM event_ratings WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                $userRatings = [];
                while ($row = $result->fetch_assoc()) {
                    $userRatings[$row['event_id']] = $row['rating'];
                }
                $stmt->close();

                // Fetch event features for content-based filtering
                $sql = "SELECT id, category_id, event_type_id, event_location, normal_price, vip_price, vvip_price FROM eventdata";
                $result = $conn->query($sql);

                $eventFeatures = [];
                while ($row = $result->fetch_assoc()) {
                    $eventFeatures[$row['id']] = [
                        'category_id' => intval($row['category_id']),
                        'event_type_id' => intval($row['event_type_id']),
                        'event_location' => $row['event_location'],
                        'normal_price' => floatval($row['normal_price']),
                        'vip_price' => floatval($row['vip_price']),
                        'vvip_price' => floatval($row['vvip_price'])
                    ];
                }

                // Build user profile based on the events they have rated
                $userProfile = [];
                $totalRatings = array_sum($userRatings);

                foreach ($userRatings as $eventId => $rating) {
                    foreach ($eventFeatures[$eventId] as $feature => $value) {
                        if (is_numeric($value)) {
                            if (!isset($userProfile[$feature])) {
                                $userProfile[$feature] = 0;
                            }
                            $userProfile[$feature] += $value * ($rating / $totalRatings);
                        }
                    }
                }

                // Calculate similarity between user profile and all events
                $eventSimilarities = [];

                foreach ($eventFeatures as $eventId => $features) {
                    $numericFeatures = array_filter($features, 'is_numeric');
                    $similarity = cosineSimilarity($userProfile, $numericFeatures);
                    $eventSimilarities[$eventId] = $similarity;
                }

                // Sort events by similarity score
                arsort($eventSimilarities);
                $topN = 4; // Number of recommended events to fetch

                // Exclude the current event being viewed
                if (isset($eventSimilarities[$event_id])) {
                    unset($eventSimilarities[$event_id]); // Remove the current event from the similarity array
                }
                
                $recommendedEvents = array_slice($eventSimilarities, 0, $topN, true);
                
                // Fetch event details for recommended events
                $recommendedEventIds = array_keys($recommendedEvents);
                
                if (!empty($recommendedEventIds)) {
                    $recommendedEventsQuery = implode(',', $recommendedEventIds);
                
                    // Fetch event details, including average ratings
                    $sql = "SELECT id, event_name, event_location, event_image, average_ratings 
                            FROM eventdata 
                            WHERE id IN ($recommendedEventsQuery)";
                
                    $result = $conn->query($sql);
                    $recommendedEventDetails = $result->fetch_all(MYSQLI_ASSOC);
                } else {
                    $recommendedEventDetails = [];
                }
            }                
            ?>

            <h2 class="text-3xl font-semibold mt-8">Recommended Events</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                <?php if (!empty($recommendedEventDetails)): ?>
                    <?php foreach ($recommendedEventDetails as $recommendedEvent): ?>
                        <div class="border rounded-lg p-4 bg-white shadow-md transition-transform transform hover:scale-105">
                            <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($recommendedEvent['event_name']); ?></h3>
                            <p class="text-gray-600"><?php echo htmlspecialchars($recommendedEvent['event_location']); ?></p>
                            <p class="text-gray-800">Average Ratings: <?php echo htmlspecialchars($recommendedEvent['average_ratings']); ?></p>
                            <img src="../uploads/<?php echo htmlspecialchars($recommendedEvent['event_image']); ?>" alt="<?php echo htmlspecialchars($recommendedEvent['event_name']); ?>" class="w-full h-auto my-2 rounded-lg shadow-sm">
                            <a href="viewEvent.php?id=<?php echo htmlspecialchars($recommendedEvent['id']); ?>" class="text-blue-500 hover:underline">View Details</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-600">No recommendations available at this time.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>
</html>