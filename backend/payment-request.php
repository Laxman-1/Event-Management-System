<?php
session_start();
include '../Include/database.php'; // Include the database connection file

// Ensure user is logged in and has a valid session
if (!isset($_SESSION['email']) || $_SESSION['usertype'] != 0) {
    header("Location: ../login.php");
    exit();
}

// Retrieve data from the previous page
$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$ticket_type = isset($_GET['ticket_type']) ? $_GET['ticket_type'] : '';$quantity = isset($_GET['quantity']) ? intval($_GET['quantity']) : 0;
$total_amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 0;
$invoice_no = isset($_GET['invoice_no']) ? $_GET['invoice_no'] : '';
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
$newAttendees = isset($_GET['attendees']) ? $_GET['attendees'] : ''; // Fetch attendees

// Fetch user details using user_id
$user_query = $conn->prepare("SELECT name, email, phoneno FROM user WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();

$user_name = $user['name'] ?? '';
$user_email = $user['email'] ?? '';
$user_phoneno = $user['phoneno'] ?? '';

// Check if event exists
$event_query = $conn->prepare("SELECT event_name FROM eventdata WHERE id = ?");
$event_query->bind_param("i", $event_id);
$event_query->execute();
$event_result = $event_query->get_result();
$event_data = $event_result->fetch_assoc();
$event_name = $event_data['event_name'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve input values from POST with default values
    $event_id = intval($_POST['event_id'] ?? 0);
    $user_id = intval($_POST['user_id'] ?? 0);
    $ticket_type = trim($_POST['inputTicketType'] ?? '');
    $quantity = intval($_POST['inputQuantity'] ?? 0);
    $amount = floatval(preg_replace('/[^0-9.]/', '', $_POST['inputAmount4'] ?? '')) * 100; // Convert to paisa
    $purchase_order_id = trim($_POST['inputInvoiceNo4'] ?? '');
    $purchase_order_name = trim($_POST['inputPurchasedOrderName'] ?? '');
    $name = trim($_POST['inputName'] ?? '');
    $email = trim($_POST['inputEmail'] ?? '');
    $phone = trim($_POST['inputPhone'] ?? '');
    $newAttendees=trim($_POST['inputAttendees']??'');

    // Input validation
    if (empty($amount) || empty($purchase_order_id) || empty($purchase_order_name) || empty($name) || empty($email) || empty($phone)) {
        $_SESSION["validate_msg"] = 'All fields are required';
        header("Location: checkout.php");
        exit();
    }

    if (!is_numeric($amount) || $amount <= 0) {
        $_SESSION["validate_msg"] = 'Invalid amount';
        header("Location: checkout.php");
        exit();
    }

    if (!preg_match('/^\d{10}$/', $phone)) {
        $_SESSION["validate_msg"] = 'Phone must be a 10-digit number';
        header("Location: checkout.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["validate_msg"] = 'Invalid email address';
        header("Location: checkout.php");
        exit();
    }

    // Prepare the payment request data
    $postFields = array(
        "return_url" => "http://localhost/Laxman/backend/payment-response.php?ticket_type=" . urlencode($ticket_type) . "&quantity=" . urlencode($quantity) . "&attendees=" . urlencode($newAttendees),
        "website_url" => "http://localhost/khalti-payment/",
        "amount" => $amount,
        "purchase_order_id" => $purchase_order_id,
        "purchase_order_name" => $purchase_order_name,
        "ticket_type" => $ticket_type,
        "quantity" => $quantity,
        "attendees" => $newAttendees, // Add attendees to the post fields
        "customer_info" => array(
            "name" => $name,
            "email" => $email,
            "phone" => $phone
        ),
    );

    // Log the payment request data
    error_log("Payment Request: " . print_r($postFields, true));

    // Convert to JSON
    $jsonData = json_encode($postFields);

    // Initialize cURL for payment request
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://a.khalti.com/api/v2/epayment/initiate/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $jsonData,
        CURLOPT_HTTPHEADER => array(
            'Authorization: key live_secret_key_68791341fdd94846a146f0457ff7b455', 
            'Content-Type: application/json',
        ),
    ));

    // Execute cURL and handle errors
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        $_SESSION["validate_msg"] = 'Payment request failed: ' . curl_error($curl);
        curl_close($curl);
        header("Location: checkout.php");
        exit();
    }

    // Decode the response
    $responseArray = json_decode($response, true);
    curl_close($curl); // Close the cURL session

    // Handle the response from Khalti
    if (isset($responseArray['error'])) {
        $_SESSION["validate_msg"] = 'Payment Error: ' . $responseArray['error'];
        header("Location: checkout.php");
        exit();
    } elseif (isset($responseArray['payment_url'])) {
        // Redirect to the payment URL
        header('Location: ' . $responseArray['payment_url']);
        exit();
    } else {
        $_SESSION["validate_msg"] = 'Unexpected response from payment gateway';
        header("Location: checkout.php");
        exit();
    }
} else {
    // Redirect to checkout if no form submission occurred
    header("Location: checkout.php");
    exit();
}
?>
