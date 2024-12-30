<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = []; // Array to store validation errors

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = $_POST['password']; // Do not hash yet for validation
    $phoneno = $conn->real_escape_string(trim($_POST['phoneno']));

    // Server-side validation

    // Validate name (must not be empty)
    if (empty($name)) {
        $error[] = "Name is required.";
    }

    // Validate email (must be a valid email format)
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Invalid email format.";
    }

    // Check if email already exists in the database
    $email_check = "SELECT email FROM user WHERE email = '$email'";
    $result = $conn->query($email_check);
    if ($result->num_rows > 0) {
        $error[] = "Email already exists. Please use a different email.";
    }

   // Validate password (must be at least 8 characters and meet pattern requirements)
if (strlen($password) < 8) {
    $error[] = "Password must be at least 8 characters long.";
} elseif (!preg_match("/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/", $password)) {
    $error[] = "Password must contain at least one number, one uppercase and lowercase letter.";
}

    // Hash the password if it passes validation
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Validate phone number (must be 10 digits)
    if (!preg_match('/^[0-9]{10}$/', $phoneno)) {
        $error[] = "Phone number must be 10 digits long.";
    }

    // If no errors, insert data into the database
    if (empty($error)) {
        $usertype = 0; // Default user type
        $sql = "INSERT INTO user (name, email, password, phoneno, usertype) VALUES ('$name', '$email', '$hashed_password', '$phoneno', '$usertype')";

        if ($conn->query($sql) === TRUE) {
            echo "<div style='color: green;'>User registered successfully!</div>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>User Registration</h2>
    <form action="#" method="POST">
        <div class="form-group">
            <input type="text" name="name" placeholder="Name" required>
        </div>
        <div class="form-group">
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group">
            <input type="tel" name="phoneno" placeholder="Phone Number" maxlength="10" required>
        </div>

        <!-- Display error messages -->
        <?php if (!empty($error)): ?>
            <div class="error">
                <ul>
                    <?php foreach ($error as $err): ?>
                        <li><?php echo $err; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-group">
            <input type="submit" value="Register">
        </div>
        
        <div class="form-group">
    <p>Already have an account? <a href="login.php">Login here</a></p>
</div>
    </form>
</div>

</body>
</html>
