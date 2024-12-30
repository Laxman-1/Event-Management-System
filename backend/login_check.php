<?php
session_start();
$_SESSION['user_logged_in'] = true; // Simulate user login for testing


include '../Include/database.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['psw']); 

    // Prepare and execute query
    $stmt = $conn->prepare("SELECT id, password, usertype FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if the user is in the database
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $usertype);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Store user details in session
            $_SESSION['user_id'] = $id;
            $_SESSION['email'] = $email;
            $_SESSION['usertype'] = $usertype;

            // Redirect users based on usertype
            if ($usertype == 0) {
                header("Location: ../index.php");
            } elseif ($usertype == 1) {
                header("Location: admin/dashboard.php");
            }
            exit(); // Ensure no further code is executed
        } else {
            echo "Invalid password."; // Consider logging this for auditing
        }
    } else {
        echo "Invalid email."; // Consider logging this for auditing
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
