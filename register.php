<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Start the session to use session variables

// Database connection
$con = mysqli_connect("localhost", "root", "", "akcafe");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error()); // Check and handle database connection failure
}

// Retrieve and sanitize user details from the registration form
$customer_name = isset($_POST['customer_name']) ? mysqli_real_escape_string($con, $_POST['customer_name']) : '';
$customer_username = isset($_POST['customer_username']) ? mysqli_real_escape_string($con, $_POST['customer_username']) : '';
$customer_email = isset($_POST['customer_email']) ? mysqli_real_escape_string($con, $_POST['customer_email']) : '';
$customer_password = isset($_POST['customer_password']) ? mysqli_real_escape_string($con, $_POST['customer_password']) : '';
$customer_phoneno = isset($_POST['customer_phoneno']) ? mysqli_real_escape_string($con, $_POST['customer_phoneno']) : '';
$customer_dob = isset($_POST['customer_dob']) ? mysqli_real_escape_string($con, $_POST['customer_dob']) : '';
$customer_gender = isset($_POST['customer_gender']) ? mysqli_real_escape_string($con, $_POST['customer_gender']) : '';

// Check if the username already exists
$checkQuery = "SELECT * FROM cafe_customer WHERE customer_username='$customer_username'";
$checkResult = mysqli_query($con, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    // Username already exists, display an error message
    $_SESSION['error_message'] = "Username already exists. Please choose another username.";
    header("Location: register.html");
    exit(); // Exit the script to stop further execution
}

// Hash the password
$hashed_password = password_hash($customer_password, PASSWORD_DEFAULT);

// Perform the registration by inserting the new user's details into the database
$sql = "INSERT INTO cafe_customer (customer_name, customer_username, customer_email, customer_password, customer_phoneno, customer_dob, customer_gender) VALUES ('$customer_name', '$customer_username', '$customer_email', '$hashed_password', '$customer_phoneno', '$customer_dob', '$customer_gender')";
$result = mysqli_query($con, $sql);

if ($result) {
    // Registration successful
    $_SESSION['success_message'] = "Registration successful. You can now log in.";
    header("Location: login_customer.php");
    exit(); // Exit the script to stop further execution
} else {
    // Registration unsuccessful
    $_SESSION['error_message'] = "Registration failed. Please try again.";
    header("Location: register.html");
    exit(); // Exit the script to stop further execution
}

// Close the connection
mysqli_close($con);
?>
