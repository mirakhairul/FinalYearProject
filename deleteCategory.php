<?php
// Establish a database connection
$conn = mysqli_connect("localhost", "root", "", "akcafe");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the category ID is provided via POST request
if (isset($_POST['category_id'])) {
    // Sanitize the input to prevent SQL injection
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    
    // Get the category name to delete products under this category
    $category_name_query = "SELECT category_name FROM cafe_category WHERE category_id = '$category_id'";
    $category_name_result = mysqli_query($conn, $category_name_query);
    if ($category_name_result && mysqli_num_rows($category_name_result) > 0) {
        $category_name_row = mysqli_fetch_assoc($category_name_result);
        $category_name = $category_name_row['category_name'];
        
        // Delete products under the category
        $delete_products_query = "DELETE FROM cafe_product WHERE category_name = '$category_name'";
        mysqli_query($conn, $delete_products_query);
    }
    
    // Construct the delete query for the category
    $delete_category_query = "DELETE FROM cafe_category WHERE category_id = '$category_id'";
    
    // Execute the delete query
    if (mysqli_query($conn, $delete_category_query)) {
        echo "<script>
                alert('Category and associated products deleted successfully.');
                window.location.href = 'categoryPage.php';
              </script>";
    } else {
        echo "<script>
                alert('Error deleting record: " . mysqli_error($conn) . "');
                window.location.href = 'categoryPage.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Category ID not provided.');
            window.location.href = 'categoryPage.php';
          </script>";
}

// Close the database connection
mysqli_close($conn);
?>
