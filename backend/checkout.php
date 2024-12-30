
<?php
include 'header.php'; 
session_start(); // Start the session
// Ensure user is logged in and has a valid session
if (!isset($_SESSION['email']) || $_SESSION['usertype'] != 0) {
    header("Location: ../login.php");
    exit();
}
//

include '../Include/database.php'; 


// Get the data passed from the header in previous page
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$ticket_type = isset($_GET['ticket_type']) ? $_GET['ticket_type'] : '';
$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 0;
$total_amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
$invoice_no = isset($_GET['invoice_no']) ? $_GET['invoice_no'] : '';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$newAttendees = isset($_GET['attendees']) ? $_GET['attendees'] : ''; // Fetch attendees

// Fetch user details using user_id
$user_query = $conn->prepare("SELECT name, email, phoneno FROM user WHERE id=?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

$user_name = isset($user['name']) ? $user['name'] : '';
$user_email = isset($user['email']) ? $user['email'] : '';
$user_phoneno = isset($user['phoneno']) ? $user['phoneno'] : '';

// Check if event exists
$event_query = $conn->prepare("SELECT event_name FROM eventdata WHERE id=?");
$event_query->bind_param("i", $event_id);
$event_query->execute();
$event_result = $event_query->get_result();
$event_data = $event_result->fetch_assoc();
$event_name = isset($event_data['event_name']) ? $event_data['event_name'] : '';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment Integration</title>

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style> .center-data{ display:flex; justify-content: center;align-items:center;height:0vh;}</style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="m-4">

    <?php
    if (isset($_SESSION['transaction_msg'])) {
        echo $_SESSION['transaction_msg'];
        unset($_SESSION['transaction_msg']);
    }

    if (isset($_SESSION['validate_msg'])) {
        echo $_SESSION['validate_msg'];
        unset($_SESSION['validate_msg']);
    }
    ?>
    <h1 class="text-center">Check out page </h1>
    <div class="d-flex justify-content-center mt-3">
    <form class="row g-3 w-50 mt-4" action="payment-request.php" method="POST">
        <label for="">Event Details:</label>

        <div class="col-md-6">
            <label for="inputAmount4" class="form-label">Amount</label>
            <input type="text" class="form-control" id="inputAmount4" name="inputAmount4" value="Rs <?php echo htmlspecialchars($total_amount); ?>" readonly>
        </div>
        <div class="col-md-6">
            <label for="inputInvoiceNo4" class="form-label">Invoice No</label>
            <input type="text" class="form-control" id="inputInvoiceNo4" name="inputInvoiceNo4" value="<?php echo htmlspecialchars($invoice_no); ?>" readonly>
        </div>
        <div class="col-12">
            <label for="inputPurchasedOrderName" class="form-label">Event ID</label>
            <input type="text" class="form-control" id="inputPurchasedOrderName" name="inputPurchasedOrderName" value="<?php echo htmlspecialchars($event_id); ?>" readonly>
            <!-- Hidden field to pass event_id -->
            <input type="hidden" name="event_id" value="<?php echo htmlspecialchars($event_id); ?>">
        </div>
        <div class="col-12">
            <label for="inputTicketType" class="form-label">Ticket Type</label>
            <input type="text" class="form-control" name="inputTicketType" value="<?php echo htmlspecialchars($ticket_type); ?>" readonly>
            <input type="hidden" name="ticket_type" value="<?php echo htmlspecialchars($ticket_type); ?>">
        </div>

        <div class="col-12">
            <label for="inputQuantity" class="form-label">Quantity</label>
            <input type="text" class="form-control" name="inputQuantity" value="<?php echo htmlspecialchars($quantity); ?>" readonly>
            <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>">
        </div>

        <div class="col-12">
            <label for="inputAttendees" class="form-label">Attendees</label>
            <input type="text" class="form-control" name="inputAttendees" value="<?php echo htmlspecialchars($newAttendees); ?>" readonly>
            <input type="hidden" name="attendees" value="<?php echo htmlspecialchars($newAttendees); ?>">
        </div>

        <label for="">Customer Details:</label>
        <div class="col-12">
            <label for="inputName" class="form-label">Name</label>
            <input type="text" class="form-control" name="inputName" value="<?php echo htmlspecialchars($user_name); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="inputEmail" class="form-label">Email</label>
            <input type="email" class="form-control" name="inputEmail" value="<?php echo htmlspecialchars($user_email); ?>" required>
        </div>
        <div class="col-md-6">
            <label for="inputPhone" class="form-label">Phone</label>
            <input type="text" class="form-control" name="inputPhone" value="<?php echo htmlspecialchars($user_phoneno); ?>" required>
        </div>
        
        <!-- Hidden field to pass user_id -->
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">

        <div class="col-12">
            <button type="submit" name="submit" class="btn btn-primary">Pay with Khalti</button>
        </div>
    </form>
    </div>
    <div class="center-data">
   <!-- <?php include 'booking.php'; ?>-->
    </div>
</body>

</html>
