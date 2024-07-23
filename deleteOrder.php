<?php
// Establish a database connection
$conn = mysqli_connect("localhost", "root", "", "akcafe");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the order_id is provided via POST request
if (isset($_POST['order_id'])) {
    // Sanitize the input to prevent SQL injection
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    
    // Construct the delete query
    $delete_query = "DELETE FROM cafe_order WHERE order_id = '$order_id'";
    
    // Execute the delete query
    if (mysqli_query($conn, $delete_query)) {
        // Redirect back to the order page after successful deletion
        header("Location: orderPageStaff.php");
        exit(); // Ensure script stops execution after redirection
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // Redirect back to the order page if no order_id is provided
    header("Location: orderPageStaff.php");
    exit(); // Ensure script stops execution after redirection
}

// Close the database connection
mysqli_close($conn);
?>