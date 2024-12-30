<?php
session_start(); // Start the session

// Check if the user is logged in and has the correct user type
if (isset($_SESSION['email']) && $_SESSION['usertype'] == 0) {
    // The user is logged in, you can access session variables here if needed
    $user_email = $_SESSION['email'];
    // Add any other session-related logic here
} else {
    // The user is not logged in, you can set a variable to display an error
    $session_error = "You are not logged in. Please log in to access all features.";
}


include '../Laxman/Include/database.php';
include 'header.php'; 
include 'banner.php';
include 'footer.php';
 ?>


    <!-- Bootstrap -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <!-- Load jQuery require for isotope -->
    <script src="assets/js/jquery.min.js"></script>
    <!-- Isotope -->
    <script src="assets/js/isotope.pkgd.js"></script>
    <script>
        //slider hall
    function scrollLeft() {
    document.querySelector('.hall-slider').scrollBy({
        left: -300,
        behavior: 'smooth'
    });
}

function scrollRight() {
    document.querySelector('.hall-slider').scrollBy({
        left: 300,
        behavior: 'smooth'
    });
}

    $(document).ready(function() {
        $('body').on('click', function() {
            $.ajax({
                url: 'Include/session.php', // Adjusted the path to your session file
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'error') {
                        window.location.href = 'login.php'; // Redirect to login.php if not logged in
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error: ' + textStatus + ': ' + errorThrown);
                    alert('An error occurred while checking session: ' + textStatus);
                }
            });
        });
    });
</script>

</body>

</html>