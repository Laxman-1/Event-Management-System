<?php
session_start();
include 'header.php';
// Ensure user is logged in and has a valid session
if (!isset($_SESSION['email']) || $_SESSION['usertype'] != 0) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
include '../Include/database.php'; // Ensure you have a proper database connection file

// Assuming you have the event_id from the URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the event details
$sql = "SELECT * FROM eventdata WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$eventResult = $stmt->get_result();
$eventDetails = $eventResult->fetch_assoc();
$stmt->close();

// Get current date in YYYY-MM-DD format
$current_date = date("Y-m-d");

// Check if event is expired
$is_expired = strtotime($eventDetails['end_date']) < strtotime($current_date);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$is_expired) {
    $ticket_type = isset($_POST['ticket_type']) ? $_POST['ticket_type'] : '';
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    
    $price = 0;
    switch ($ticket_type) {
        case 'normal':
            $price = $eventDetails['normal_price'];
            break;
        case 'vip':
            $price = $eventDetails['vip_price'];
            break;
        case 'vvip':
            $price = $eventDetails['vvip_price'];
            break;
        default:
            $price = 0;
            break;
    }

    $total = $price * $quantity;

    // Generate a unique invoice number
    $invoice_no = 'INV' . strtoupper(uniqid());

    // Get the existing number of attendees from the database
    $newAttendees = $eventDetails['attendees'];

    // Redirect to checkout with the necessary booking details, including the current number of attendees
    header("Location: checkout.php?amount=$total&invoice_no=$invoice_no&event_id=$event_id&ticket_type=$ticket_type&quantity=$quantity&user_id={$_SESSION['user_id']}&attendees=$newAttendees");
    
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <style>
        body {
            background-color: #f9fafb;
        }

        .card {
            max-width: 400px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .card img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .card form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card select,
        .card input[type="number"],
        .card input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            outline: none;
        }

        .card select:focus,
        .card input[type="number"]:focus,
        .card input[type="text"]:focus {
            border-color: #3b82f6;
        }

        .card button {
            background-color: #3b82f6;
            color: #ffffff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .card button:hover {
            background-color: #2563eb;
        }

        .card button[disabled] {
            background-color: #9ca3af;
            cursor: not-allowed;
        }
    </style>
    <script>
        function calculateTotal() {
            // Get the selected ticket type and its price
            var ticketType = document.getElementById("ticket_type").value;
            var normalPrice = <?php echo $eventDetails['normal_price']; ?>;
            var vipPrice = <?php echo $eventDetails['vip_price']; ?>;
            var vvipPrice = <?php echo $eventDetails['vvip_price']; ?>;

            var price = 0;
            if (ticketType === "normal") {
                price = normalPrice;
            } else if (ticketType === "vip") {
                price = vipPrice;
            } else if (ticketType === "vvip") {
                price = vvipPrice;
            }

            // Get the quantity
            var quantity = document.getElementById("quantity").value;

            // Calculate total price
            var total = price * quantity;

            // Display total price in the textbox
            document.getElementById("total_price").value = "Rs " + total.toFixed(2);
        }

        function validateQuantity() {
            var quantity = document.getElementById("quantity").value;
            var maxAttendees = <?php echo $eventDetails['attendees']; ?>;

            if (quantity > maxAttendees) {
                alert("Quantity cannot exceed the number of attendees (" + maxAttendees + ")");
                document.getElementById("quantity").value = maxAttendees; // Set to max allowed
                calculateTotal(); // Recalculate total price
                return false;
            }
            return true;
        }
    </script>
</head>

<body>
    <div class="container mx-auto p-6">
        <div class="card">
            <img src="../uploads/<?php echo htmlspecialchars($eventDetails['event_image']); ?>" alt="<?php echo htmlspecialchars($eventDetails['event_name']); ?>">
            <h1><?php echo htmlspecialchars($eventDetails['event_name']); ?></h1>
            <p><?php echo htmlspecialchars($eventDetails['event_location']); ?></p>

            <!-- Display the current number of attendees and max limit -->
            <p>Attendees: <?php echo htmlspecialchars($eventDetails['attendees']); ?></p>

            <!-- Show event expiration status -->
            <?php if ($is_expired): ?>
                <p style="color: red;">This event has ended. Booking is closed.</p>
            <?php endif; ?>

            <form action="" method="POST" onsubmit="return validateQuantity()">
                <select name="ticket_type" id="ticket_type" required onchange="calculateTotal()" <?php if ($is_expired) echo 'disabled'; ?>>
                    <option value="normal">Normal - Rs <?php echo htmlspecialchars($eventDetails['normal_price']); ?></option>
                    <option value="vip">VIP - Rs <?php echo htmlspecialchars($eventDetails['vip_price']); ?></option>
                    <option value="vvip">VVIP - Rs <?php echo htmlspecialchars($eventDetails['vvip_price']); ?></option>
                </select>

                <input type="number" name="quantity" id="quantity" min="1" max="<?php echo htmlspecialchars($eventDetails['attendees']); ?>" required onchange="calculateTotal()" placeholder="Quantity" <?php if ($is_expired) echo 'disabled'; ?>>

                <input type="text" id="total_price" name="total_price" placeholder="Total Price" readonly>

                <button type="submit" <?php if ($is_expired) echo 'disabled'; ?>>Book Now</button>
            </form>
        </div>
    </div>
</body>

</html>
