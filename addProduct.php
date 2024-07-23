<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Connect to the database (replace dbname, username, password, and host with your actual database details)
$pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');

// Check for any errors during connection
if (!$pdo) {
    die("Connection failed: " . $pdo->errorInfo());
}

// Fetch categories from session
$categories = isset($_SESSION["categories"]) ? $_SESSION["categories"] : [];

if (isset($_POST['add_product'])) {
    // Fetch values from form inputs
    $prod_code = $_POST['prod_code'];
    $prod_name = $_POST['prod_name'];
    $prod_desc = $_POST['prod_desc'];
    $prod_category = $_POST['category_name'];
    $prod_price = $_POST["prod_price"];
    $itempic = $_FILES["img"]["name"];
    $extension = substr($itempic, strlen($itempic) - 4, strlen($itempic));

    // Allowed extensions
    $allowed_extensions = array(".jpg", "jpeg", ".png", ".gif");

    if (!in_array($extension, $allowed_extensions)) {
        echo "<script>alert('Invalid format. Only jpg / jpeg / png / gif format allowed');</script>";
    } else {
        $itempic = md5($itempic) . $extension;
        $target_directory = "uploads/"; // Relative path to the directory where you want to store the uploaded files
        $target_file = $target_directory . $itempic;
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
            // Prepare SQL statement to insert new product into the database
            if (strtolower($prod_category) == 'drinks') {
                $stmt = $pdo->prepare("INSERT INTO cafe_product (prod_code, prod_name, prod_desc, category_name, prod_price, img) VALUES (?, ?, ?, ?, ?, ?)");
                $params = [$prod_code, $prod_name, $prod_desc, $prod_category, $prod_price, $target_file];
            } else {
                $stmt = $pdo->prepare("INSERT INTO cafe_product (prod_code, prod_name, prod_desc, category_name, prod_price, img, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $params = [$prod_code, $prod_name, $prod_desc, $prod_category, $prod_price, $target_file, 0]; // Default stock value
            }

            if ($stmt->execute($params)) {
                echo "<script>alert('Food Item has been added.');</script>";
                header("Location: productPage.php");
                exit();
            } else {
                echo "<script>alert('Failed to add Food Item.');</script>";
            }
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>ADD PRODUCT</title>
    <link rel="stylesheet" href="stylef.css" />
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <style> 
        .form-container { 
            display: flex; 
            flex-direction: column; 
            padding: 20px;
            position: relative;
            margin-bottom: -20px;
        } 

        .form-label { 
            margin-bottom: 10px; 
            font-size: 16px; 
            color: #444; 
            text-align: left; 
        } 

        .form-input { 
            padding: 10px; 
            margin-bottom: 20px; 
            border: 1px solid #ccc; 
            border-radius: 8px; 
            font-size: 16px; 
            width: 100%; 
            box-sizing: border-box; 
        } 

        .btn-submit, 
        .btn-close-popup { 
            padding: 10px 24px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            transition: background-color 0.3s ease, color 0.3s ease; 
            display: inline-block; /* Make the button inline */
            text-align: center; /* Ensure text alignment is centered */
            width: 150px; /* Set a fixed width for both buttons */
            box-sizing: border-box; /* Include padding and border in the element's total width and height */
        }

        .btn-submit { 
            background-color: green; 
            color: #fff; 
        } 

        .btn-close-popup { 
            margin-top: -10px; 
            background-color: #e74c3c; 
            color: #fff; 
            text-decoration: none; /* Remove default underline for anchor tag */
        } 

        .btn-submit:hover, 
        .btn-close-popup:hover { 
            background-color: #000000; 
            color: white;
        } 

        /* Keyframes for fadeInUp animation */ 
        @keyframes fadeInUp { 
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            } 

            to { 
                opacity: 1; 
                transform: translateY(0); 
            } 
        } 
        .container2nd {
            margin-left: 280px; /* Adjust this based on the width of your sidebar */
            padding: 40px;
            padding-top: 2px;
            overflow: hidden; /* Hide the scrollbar for the container */
            position: fixed;
            }
            
            .tableStaff {
            width: 70%; /* Adjust the width as needed */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: auto; /* Adjust height based on the header height */
            margin-left: auto;
            }
    </style> 
</head>
<body>
    <div class="container">
        <nav>
            <ul>
                <li><a href="logocafe.jpeg" class="logo">
                    <img src="logocafe.jpeg" alt="">
                    <span class="nav-item">POS SYSTEM</span>
                </a></li>
                <li><a href="admin.php">
                    <span class="nav-item">Dashboard</span>
                </a></li>
                <li><a href="staffPage.php">
                    <span class="nav-item">Staffs</span>
                </a></li>
                <li><a href="customerPage.php">
                    <span class="nav-item">Reg. Customers</span>
                </a></li>
                <li><a href="categoryPage.php">
                    <span class="nav-item">Product Category</span>
                </a></li>
                <li><a href="productPage.php">
                    <span class="nav-item">Products</span>
                </a></li>
                <li><a href="stock.php">
                <span class="nav-item">Stock</span>
                </a></li>
                <li><a href="orderPage.php">
                    <span class="nav-item">Orders</span>
                </a></li>
                <li><a href="feedbackReviews.php">
                    <span class="nav-item">Customer Feedback</span>
                </a></li>
                <li><a href="salesReport.php">
                    <span class="nav-item">Sales Report</span>
                </a></li>
                <li><a href="homeadmin.html" class="logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-item">Log out</span>
                </a></li>
            </ul>
        </nav>
    </div>

    <div class="container2nd">
        <section class="main">
            <br>
            <h2>Add New Product</h2>
        </section>

        <div class="tableStaff">
            <form class="form-container" method="post" onsubmit="return validateForm()" enctype="multipart/form-data"> 
                <label class="form-label" for="prod_code">Product Code:</label> 
                <input class="form-input" type="text" id="prod_code" name="prod_code" required> 

                <label class="form-label" for="prod_name">Product Name:</label> 
                <input class="form-input" type="text" id="prod_name" name="prod_name" required> 

                <label class="form-label" for="prod_desc">Product Description:</label> 
                <input class="form-input" type="text" id="prod_desc" name="prod_desc" required> 

                <label class="form-label" for="category_name">Product Category:</label> 
                <select class="form-input" id="category_name" name="category_name" required>
                    <option value="">Select Category</option>
                    <?php
                    // Prepare and execute SQL query to select all category names
                    $stmt = $pdo->query('SELECT category_name FROM cafe_category');
                    // Loop through each row in the result set and add options to the select tag
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . $row['category_name'] . "'>" . $row['category_name'] . "</option>";
                    }
                    ?>
                </select>

                <label class="form-label" for="prod_price">Product Price:</label> 
                <input class="form-input" type="number" step="0.01" id="prod_price" name="prod_price" required> 

                <label class="form-label" for="img">Product Image:</label> 
                <input class="form-input" type="file" id="img" name="img" required>


                <center>
                    <button class="btn-submit" type="submit" name="add_product">Submit</button>
                    <a href="productPage.php" class="btn-close-popup">Close</a>
                </center>
            </form> 
        </div>
    </div> 

    <script>
        function validateForm() {
            // Get the values of form fields
            var code = document.getElementById("prod_code").value;
            var name = document.getElementById("prod_name").value;
            var desc = document.getElementById("prod_desc").value;
            var category = document.getElementById("category_name").value;
            var price = document.getElementById("prod_price").value;
            var img = document.getElementById("img").value;

            // Check if any of the fields are empty
            if (code.trim() == '' || name.trim() == '' || desc.trim() == '' || category.trim() == '' || price.trim() == '' || img.trim() == '') {
                alert("Please fill in all fields.");
                return false; // Prevent form submission
            }

            // If all fields are filled, allow form submission
            return true;
        }
    </script>
</body>
</html>
