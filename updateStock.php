<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$con = mysqli_connect("localhost", "root", "", "akcafe");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['prod_id']) && isset($_POST['action'])) {
    $prod_id = mysqli_real_escape_string($con, $_POST['prod_id']);
    $action = $_POST['action'];

    $result = mysqli_query($con, "SELECT stock FROM cafe_product WHERE prod_id='$prod_id'");
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $current_stock = (int)$row['stock'];

        if ($action == 'increase') {
            $new_stock = $current_stock + 1;
        } elseif ($action == 'decrease' && $current_stock > 0) {
            $new_stock = $current_stock - 1;
        } else {
            $new_stock = $current_stock;
        }

        $update_query = "UPDATE cafe_product SET stock='$new_stock' WHERE prod_id='$prod_id'";
        if (mysqli_query($con, $update_query)) {
            echo $new_stock;
        } else {
            http_response_code(500);
            echo "Error updating record: " . mysqli_error($con);
        }
    } else {
        http_response_code(500);
        echo "Error fetching stock: " . mysqli_error($con);
    }
} else {
    http_response_code(400);
    echo "Invalid request";
}

mysqli_close($con);
?>
