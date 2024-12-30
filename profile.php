<?php
session_start(); // Start the session

include '../Laxman/header.php';
include '../Laxman/Include/database.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<div class='alert alert-danger'>You must log in to view your profile.</div>";
    exit;
}

// Fetch user details from the database
$user_id = $_SESSION['user_id']; // Get user ID from session
$query = "SELECT * FROM user WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit;
}

// Handle password reset form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the passwords match
    if ($new_password === $confirm_password) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update the password in the database
        $update_query = "UPDATE user SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $hashed_password, $user_id);

        if ($update_stmt->execute()) {
            echo "<div class='alert alert-success'>Password reset successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating password. Please try again.</div>";
        }

        $update_stmt->close();
    } else {
        echo "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-card {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Your Profile</h2>
        <div class="card profile-card">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($user['name']); ?></h5>
                <p class="card-text"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <p class="card-text"><strong>Phone Number:</strong> <?php echo htmlspecialchars($user['phoneno']); ?></p>
                <p class="card-text"><strong>Joined On:</strong> <?php echo htmlspecialchars($user['created_at']); ?></p>
                
                <h5>Reset Password</h5>
                <form action="" method="POST"> 
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
