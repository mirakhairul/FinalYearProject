<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Sample database connection (replace this with your actual connection code)
$con = mysqli_connect("localhost", "root", "", "akcafe");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Connect to the database using PDO
$pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');

// Check for any errors during connection
if (!$pdo) {
    die("Connection failed: " . $pdo->errorInfo());
}
?>

<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DASHBOARD STAFF</title>
    <link rel="stylesheet" href="stylef.css" />
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>

    <style>
        .container2nd {
            height: 80vh;
            overflow: hidden; /* Hide the scrollbar for the container */
            position: fixed;
        }

        .tableStaff {
            width: 70%; /* Adjust the width as needed */
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: calc(95vh - 80px); /* Adjust height based on the header height */
            overflow-y: scroll;
        }
        
        /* Hide scrollbar for WebKit browsers */
        .tableStaff::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge, and Firefox */
        .tableStaff {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
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
                <li><a href="staff.php">
                    <span class="nav-item">Dashboard</span>
                </a></li>
                <li><a href="customerPageStaff.php">
                    <span class="nav-item">Reg. Customers</span>
                </a></li>
                <li><a href="categoryPageStaff.php">
                    <span class="nav-item">Product Category</span>
                </a></li>
                <li><a href="productPageStaff.php">
                    <span class="nav-item">Products</span>
                </a></li>
                <li><a href="stock.php">
                    <span class="nav-item">Stock</span>
                </a></li>
                <li><a href="orderPageStaff.php">
                    <span class="nav-item">Orders</span>
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
            <div class="main-top">
                <h1 style="margin-right:10px;">Products</h1> <p>staff</p>
            </div>
        </section>

        <div class="tableStaff">
        <strong><a class="btn btn-secondary" style="color: rgb(89, 112, 227); box-sizing: 20px" href="addProduct.php" role="button">+ Add Product</a></strong>
        <h3>Product list</h3>

        <!-- Category Filter Form -->
        <form method="get" action="">
            <label style="font-size:14px;" for="category_filter">Filter by Category:</label>
            <select id="category_filter" name="category">
                <option style="font-size:14px;" value="">All</option>
                <?php
                // Fetch categories from the database
                $categories = $pdo->query('SELECT DISTINCT category_name FROM cafe_product')->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $category) {
                    $selected = isset($_GET['category']) && $_GET['category'] == $category['category_name'] ? 'selected' : '';
                    echo "<option value='{$category['category_name']}' $selected>{$category['category_name']}</option>";
                }
                ?>
            </select>
            <button style="font-size:14px; border-radius:2px; padding:2px;" type="submit">Filter</button>
        </form>
        <table class="table1" style=" margin-top:5px;">
            <thead>
            <tr>
                <th class="text-center">Product ID</th>
                <th class="text-center">Product Code</th>
                <th class="text-center">Name</th>
                <th class="text-center">Description</th>
                <th class="text-center">Category</th>
                <th class="text-center">Price</th>
                <th class="text-center">Availability</th>
                <th class="text-center">Image</th>
                <th class="text-center" colspan="2">Action</th>
            </tr>
            </thead>
            <tbody>

            <?php
            // Prepare and execute SQL query to select all records from a table (replace tablename with your actual table name)
            $categoryFilter = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : '';
            if ($categoryFilter) {
                $stmt = $pdo->prepare('SELECT * FROM cafe_product WHERE category_name = :category');
                $stmt->execute(['category' => $categoryFilter]);
            } else {
                $stmt = $pdo->query('SELECT * FROM cafe_product ORDER BY created_at DESC');
            }

            // Loop through each row in the result set and display the data in table rows
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['prod_id']}</td>";
                echo "<td>{$row['prod_code']}</td>";
                echo "<td>{$row['prod_name']}</td>";
                echo "<td>{$row['prod_desc']}</td>";
                echo "<td>{$row['category_name']}</td>";
                // Display price with currency symbol and 2 decimal points
                echo "<td>RM " . number_format($row['prod_price'], 2) . "</td>";
                echo "<td>";
                echo "<form action='updateAvailability.php' method='post'>";
                echo "<input type='hidden' name='prod_id' value='{$row['prod_id']}'>";
                echo "<select name='availability' onchange='this.form.submit()'>";
                echo "<option value='Available' " . ($row['availability'] == 'Available' ? 'selected' : '') . ">Available</option>";
                echo "<option value='Out of Stock' " . ($row['availability'] == 'Out of Stock' ? 'selected' : '') . ">Out of Stock</option>";
                echo "</select>";
                echo "</form>";
                echo "</td>";

                // Assuming $row['img'] contains the file path from the database
                echo "<td><img src='{$row['img']}' alt='{$row['prod_name']}' style='max-width: 50px; max-height: 150px;'></td>";

                // Add action buttons in the last column
                echo "<td>";
                echo "<form action='editProduct.php' method='post'>";
                echo "<input type='hidden' name='prod_id' value='{$row['prod_id']}'>";
                echo "<button type='submit' class='btn btn-primary' style='height:30px; background-color:rgb(71, 182, 71); padding: 4px; border-radius: 2px; color: white; margin-bottom:5px;'>Edit</button>";
                echo "</form>";

                echo "<form action='deleteProduct.php' method='post'>";
                echo "<input type='hidden' name='prod_id' value='{$row['prod_id']}'>";
                echo "<button type='submit' class='btn btn-primary' style='height:30px; background-color:red; padding: 4px; border-radius: 2px; color: white;'>Delete</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
