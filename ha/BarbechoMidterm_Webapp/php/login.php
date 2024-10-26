<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../db_connect/db_connect.php'; // Ensure the path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Check what data is being sent
    // var_dump($_POST);

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement
    $check_email = "SELECT id, password FROM user WHERE email = ?";
    $stmt = $conn->prepare($check_email);

    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    // Bind parameters and execute
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if any account is found
    if ($stmt->num_rows == 0) {
        echo "No account found with that email address.";
    } else {
        // Bind the result variables
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            echo "Login successful! Welcome!";
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit(); // Prevent further code execution
        } else {
            echo "Incorrect password.";
        }
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
