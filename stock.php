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
?>

<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>DASHBOARD ADMIN</title>
    <link rel="stylesheet" href="stylef.css" />
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <style>
        .container2nd {
            margin-left: 300px; /* Adjust this based on the width of your sidebar */
            margin-top: 30px;
            padding: 20px;
            height: 80vh;
            overflow: hidden; /* Hide the scrollbar for the container */
            position: fixed;
        }

        .main-top {
            display: fixed;
            justify-content: space-between;
        }
        .tableStaff {
            width: 70%; /* Adjust the width as needed */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: calc(95vh - 80px); /* Adjust height based on the header height */
            overflow-y: scroll;
            margin-left: auto;
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
        <div class="main-top">
            <h1 style="margin-right:10px; margin-left:-30px; margin-top:-40px;">Stock</h1>
            <p style="margin-right:1200px; margin-top:-40px;">admin</p>
        </div>
    </section>

    <div class="tableStaff">
        <h3>Stock list</h3>
        <!-- Category Filter Form -->
        <form method="get" action="" id="categoryForm">
            <label style="font-size:14px;" for="category_filter">Filter by Category:</label>
            <select id="category_filter" name="category" onchange="document.getElementById('categoryForm').submit()">
                <option style="font-size:14px;" value="">All</option>
                <option value="Pattisseries" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Pattisseries') ? 'selected' : ''; ?>>Pattisseries</option>
                <option value="Dessert" <?php echo (isset($_GET['category']) && $_GET['category'] == 'Dessert') ? 'selected' : ''; ?>>Dessert</option>
            </select>
        </form>
        <table class="table1" style=" margin-top:5px;">
            <thead>
            <tr>
                <th class="text-center">Product ID</th>
                <th class="text-center">Product Code</th>
                <th class="text-center">Name</th>
                <th class="text-center">Category</th>
                <th class="text-center">Image</th>
                <th class="text-center">Stock</th>
            </tr>
            </thead>
            <tbody>

            <?php
            // Connect to the database using PDO
            $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Check for any errors during connection
            if (!$pdo) {
                die("Connection failed: " . $pdo->errorInfo());
            }

            // Prepare and execute SQL query to select records from 'Patisseries' and 'Dessert' categories
            $categoryFilter = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : '';
            if ($categoryFilter) {
                $stmt = $pdo->prepare("SELECT * FROM cafe_product WHERE category_name = :category");
                $stmt->execute(['category' => $categoryFilter]);
            } else {
                $stmt = $pdo->query("SELECT * FROM cafe_product WHERE category_name IN ('Pattisseries', 'Dessert')");
            }

            // Loop through each row in the result set and display the data in table rows
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['prod_id']}</td>";
                echo "<td>{$row['prod_code']}</td>";
                echo "<td>{$row['prod_name']}</td>";
                echo "<td>{$row['category_name']}</td>";

                // Assuming $row['img'] contains the file path from the database
                echo "<td><img src='{$row['img']}' alt='{$row['prod_name']}' style='max-width: 50px; max-height: 150px;'></td>";

                // Stock column with increase and decrease functionality
                echo "<td>";
                echo "<form class='update-stock-form' data-prod-id='{$row['prod_id']}'>";
                echo "<button type='button' data-action='decrease' class='btn btn-primary' style='height:20px; background-color:#C8A2C8; padding: 2px; border-radius: 1px; color: white; margin-right:5px;'>-</button>";
                echo "<span class='stock-quantity'>{$row['stock']}</span>";
                echo "<button type='button' data-action='increase' class='btn btn-primary' style='height:20px; background-color:#C8A2C8; padding: 2px; border-radius: 1px; color: white; margin-left:5px;'>+</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.update-stock-form button').forEach(button => {
        button.addEventListener('click', function() {
            const form = this.closest('.update-stock-form');
            const prodId = form.getAttribute('data-prod-id');
            const action = this.getAttribute('data-action');
            const stockSpan = form.querySelector('.stock-quantity');

            fetch('updateStock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'prod_id': prodId,
                    'action': action
                })
            })
            .then(response => response.text())
            .then(data => {
                // Only update the stock quantity if the response is a valid number
                if (!isNaN(data)) {
                    stockSpan.textContent = data;
                } else {
                    console.error('Error updating stock:', data);
                    alert('An error occurred while updating the stock.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>

</body>
</html>

updateStock.php
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