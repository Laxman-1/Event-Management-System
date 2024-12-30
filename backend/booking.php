<?php


// Ensure user is logged in and has a valid session
if (!isset($_SESSION['email']) || $_SESSION['usertype'] != 0) {
    header("Location: ../login.php");
    exit();
}

// Include database connection
include '../Include/database.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $invoice_no = isset($_POST['inputInvoiceNo4']) ? $_POST['inputInvoiceNo4'] : '';
    $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $newAttendees = isset($_POST['attendees']) ? intval($_POST['attendees']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0; // Added quantity retrieval
    $total_amount = isset($_POST['inputAmount4']) ? floatval(str_replace('Rs ', '', $_POST['inputAmount4'])) : 0;

    // Set default prices (you can adjust this logic based on ticket type if needed)
    $normal_price = $total_amount; // Assuming the total amount is for normal price tickets
    $vip_price = 0.00; // Adjust if needed
    $vvip_price = 0.00; // Adjust if needed

    // Prepare the SQL insert statement
    $insert_query = $conn->prepare("
        INSERT INTO bookings 
        (invoice_no, total_attendees, quantity, event_id, user_id, normal_price, vip_price, vvip_price, total, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'incomplete', NOW())
    ");

    // Bind parameters
    $insert_query->bind_param(
        "siidddddd", 
        $invoice_no,      // Invoice number (string)
        $newAttendees,    // Total attendees (integer)
        $quantity,        // Quantity (integer)
        $event_id,        // Event ID (integer)
        $user_id,         // User ID (integer)
        $normal_price,    // Normal price (decimal)
        $vip_price,       // VIP price (decimal)
        $vvip_price,      // VVIP price (decimal)
        $total_amount     // Total amount (decimal)
    );

    // Execute the query
    if ($insert_query->execute()) {
        // On success, store a success message in session and redirect
        $_SESSION['transaction_msg'] = "<div class='alert alert-success'>Booking successfully added to the database.</div>";
        header("Location: "); // Redirect to a success page
        exit();
    } else {
        // On failure, store an error message in session
        $_SESSION['transaction_msg'] = "<div class='alert alert-danger'>Failed to complete the booking. Please try again.</div>";
    }

    // Close the statement
    $insert_query->close();
}

// Close the database connection
$conn->close();
?>

<form class="row g-3 w-50 mt-4" action="" method="POST">
    <div class="col-md-6">
        <input type="hidden" class="form-control" id="inputAmount4" name="inputAmount4" value="Rs <?php echo htmlspecialchars($total_amount); ?>" readonly>
    </div>
    <div class="col-md-6">
        <input type="hidden" class="form-control" id="inputInvoiceNo4" name="inputInvoiceNo4" value="<?php echo htmlspecialchars($invoice_no); ?>" readonly>
    </div>
    <div class="col-12">
        <input type="hidden" class="form-control" id="inputPurchasedOrderName" name="inputPurchasedOrderName" value="<?php echo htmlspecialchars($event_id); ?>" readonly>
        <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
    </div>
    <div class="col-12">
        <input type="hidden" class="form-control" name="inputTicketType" value="<?php echo htmlspecialchars($ticket_type); ?>" readonly>
        <input type="hidden" name="ticket_type" value="<?php echo htmlspecialchars($ticket_type); ?>">
    </div>
    <div class="col-12">
        <input type="hidden" class="form-control" name="inputQuantity" value="<?php echo htmlspecialchars($quantity); ?>" readonly>
        <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>">
    </div>
    <div class="col-12">
        <input type="hidden" class="form-control" name="inputAttendees" value="<?php echo htmlspecialchars($newAttendees); ?>" readonly>
        <input type="hidden" name="attendees" value="<?php echo htmlspecialchars($newAttendees); ?>">
    </div>
    <div class="col-12">
        <input type="hidden" class="form-control" name="inputName" value="<?php echo htmlspecialchars($user_name); ?>" required>
    </div>
    <div class="col-md-6">
        <input type="hidden" class="form-control" name="inputEmail" value="<?php echo htmlspecialchars($user_email); ?>" required>
    </div>
    <div class="col-md-6"> 
        <input type="hidden" class="form-control" name="inputPhone" value="<?php echo htmlspecialchars($user_phoneno); ?>" required>
    </div>
    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
    <div class="col-12">
        <button type="submit" name="submit" class="btn btn-primary">Send data for confirmation</button>
    </div>
</form>
