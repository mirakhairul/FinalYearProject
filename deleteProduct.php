<?php
// Establish a database connection
$conn = mysqli_connect("localhost", "root", "", "akcafe");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the product ID is provided via POST request
if (isset($_POST['prod_id'])) {
    // Sanitize the input to prevent SQL injection
    $prod_id = mysqli_real_escape_string($conn, $_POST['prod_id']);
    
    // Construct the delete query
    $delete_query = "DELETE FROM cafe_product WHERE prod_id = '$prod_id'";
    
    // Execute the delete query
    if (mysqli_query($conn, $delete_query)) {
        // Redirect back to the product page after successful deletion
        header("Location: productPage.php");
        exit(); // Ensure script stops execution after redirection
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // Redirect back to the product page if no product ID is provided
    header("Location: productPage.php");
    exit(); // Ensure script stops execution after redirection
}

// Close the database connection
mysqli_close($conn);
?>

