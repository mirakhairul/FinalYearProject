<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection
$con = mysqli_connect("localhost", "root", "", "akcafe");

// Check the connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Retrieve user details from the login form
$customer_username = isset($_POST['customer_username']) ? mysqli_real_escape_string($con, $_POST['customer_username']) : '';
$customer_password = isset($_POST['customer_password']) ? mysqli_real_escape_string($con, $_POST['customer_password']) : '';

// Debugging: Print received username and password
error_log("Received username: $customer_username");
error_log("Received password: $customer_password");

// Check if the username exists
$checkQuery = "SELECT * FROM cafe_customer WHERE customer_username='$customer_username'";
$checkResult = mysqli_query($con, $checkQuery);

if ($checkResult && mysqli_num_rows($checkResult) == 1) {
    $row = mysqli_fetch_assoc($checkResult);
    // Debugging: Print the hashed password from the database
    error_log("Hashed password from DB: " . $row['customer_password']);

    // Verify the password
    if (password_verify($customer_password, $row['customer_password'])) {
        // Password matches, set session variables and redirect to home page
        $_SESSION['customer_username'] = $customer_username;
        $_SESSION['customer_name'] = $row['customer_name'];
        $_SESSION['customer_phoneno'] = $row['customer_phoneno'];
        $_SESSION['customer_email'] = $row['customer_email'];
        $_SESSION['login_success'] = true;
        error_log("Login successful. Redirecting to home.php");
        header("Location: home.php?login_success=1");
        exit();
    } else {
        // Password does not match
        $_SESSION['error_message'] = "Incorrect password. Please try again.";
        error_log("Password mismatch. Redirecting to login_customer.php");
        header("Location: login_customer.php");
        exit();
    }
} else {
    // Username does not exist
    $_SESSION['error_message'] = "Username not found. Please register or try again.";
    error_log("Username not found. Redirecting to login_customer.php");
    header("Location: login_customer.php");
    exit();
}

// Close the connection
mysqli_close($con);
?>
