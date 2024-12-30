<?php
session_start(); 

include 'header.php';
if (!isset($_SESSION['email']) || $_SESSION['usertype'] != 0) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .full-page-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }
        .card img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <?php
    if (isset($_SESSION['transaction_msg'])) {
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Payment Successful",
                text: "'. $_SESSION['transaction_msg'] .'",
                confirmButtonText: "OK"
            });
        </script>';
        unset($_SESSION['transaction_msg']);
    }
    ?>

    <div class="full-page-container">
        <div class="card text-center">
            <img src="payment-success.jpg" class="img-fluid" alt="Payment Successful">
            <div class="card-body bg-success text-white">
                <h5 class="card-title">Dear Customer,</h5>
                <p class="card-text">Your payment has been successfully processed.</p>
            </div>
           
        </div>
    </div>

    <!-- Bootstrap JS (optional but recommended for proper functionality of components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
