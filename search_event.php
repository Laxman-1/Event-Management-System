<?php
session_start(); // Start the session

// Include database connection
include '../Laxman/Include/database.php'; // Ensure you have a proper database connection file

// Initialize an empty array to store the search results
$searchResults = [];

// Handle form submission
if (isset($_GET['search'])) {
    // Get the search query and sanitize it
    $searchQuery = '%' . $conn->real_escape_string($_GET['search']) . '%';

    // Search for events matching the query in event name, location, and organizer details
    $sql = "SELECT e.*, o.name as organizer_name, o.phoneno as organizer_phone, o.email as organizer_email 
            FROM eventdata e 
            JOIN organizer o ON e.organizer_id = o.id 
            WHERE e.event_name LIKE ? OR e.event_location LIKE ? OR o.name LIKE ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $searchQuery, $searchQuery, $searchQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all matching events
    while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Events</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        .event-image {
            max-width: 100%;
            height: auto;
            max-height: 200px; 
            object-fit: cover;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg">
        <div class="container mx-auto flex justify-between items-center p-4">
            <div class="flex items-center">
                <a class="nav-link btn-outline-primary rounded-pill px-3" href="event.php">Event</a>
                <!-- Add more navigation items here -->
            </div>
            <form method="GET" action="search_event.php" class="flex items-center">
                <input type="text" name="search" placeholder="Search by event name, location, or organizer" class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Search</button>
            </form>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-3xl font-bold text-gray-800">Search Events</h1>

            <!-- Display search results -->
            <div class="mt-6">
                <?php if (!empty($searchResults)): ?>
                    <h2 class="text-2xl font-semibold mb-4">Search Results:</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($searchResults as $event): ?>
                            <div class="border rounded-lg p-4 bg-white shadow-md transition-transform transform hover:scale-105">
                                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                <p class="text-gray-600"><?php echo htmlspecialchars($event['event_location']); ?></p>
                                <p class="text-gray-800">Organizer: <?php echo htmlspecialchars($event['organizer_name']); ?></p>
                                <img src="../Laxman/uploads/ echo htmlspecialchars($event['event_image']); ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>" class="event-image my-2">
                                <a href="viewEvent.php?id=<?php echo htmlspecialchars($event['id']); ?>" class="text-blue-500 hover:underline">View Details</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <?php if (isset($_GET['search'])): ?>
                        <p class="text-gray-600">No events found matching your search criteria.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

