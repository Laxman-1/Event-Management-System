<?php
session_start(); // Start the session

$host = 'localhost';
$user = 'root';
$password = '';
$db = 'event';

// Create connection
$conn = mysqli_connect($host, $user, $password, $db);

if ($conn === false) {
    die("Connection error: " . mysqli_connect_error());
}

// Initialize variables for form data
$email = '';
$password = '';
$error_message = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Print the POST array
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);

        // Query to get the user data
        $sql = "SELECT * FROM organizer WHERE email='$email'";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // Check if the password matches
            if (password_verify($password, $user['password'])) {
                // Check if the user's status is approved
                if ($user['status'] === 'approved') {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_status'] = $user['status'];

                    // Redirect to the organizer dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error_message = "Your account is not approved yet.";
                }
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Email or password not set.";
    }
}

// Close the connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Login</title>
    <style>
        /* Reset some default browser styles */
        body, html {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f0f0f0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Center the login container */
        .login-container {
            width: 100%;
            max-width: 350px;  /* Increased width */
            padding: 40px;  /* Increased padding */
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            margin-bottom: 30px;  /* Increased margin */
            font-size: 2em;  /* Increased font size */
        }

        /* Style all input fields */
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 16px;  /* Increased padding */
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 6px;
            margin-bottom: 20px;  /* Increased margin */
            font-size: 1.2em;  /* Increased font size */
        }

        /* Style the submit button */
        input[type="submit"] {
            width: 100%;  /* Changed width to 100% to match container */
            background-color: #04AA6D;
            color: white;
            padding: 16px 20px;  /* Increased padding */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2em;  /* Increased font size */
        }

        input[type="submit"]:hover {
            background-color: #039f5a;
        }

        /* Style the register link */
        .right-link {
            display: inline-block;
            margin-top: 15px;
            font-size: 1em;
            color: #04AA6D;
            text-decoration: none;
        }

        .right-link:hover {
            text-decoration: underline;
        }

        /* Style error messages */
        .error-message {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin(Organizer) Login</h2>
        <form method="post" action="">
            <input type="email" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <?php if ($error_message): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <!--<a href="register.php" class="right-link">Register here</a>
        --></div>
</body>
</html>
