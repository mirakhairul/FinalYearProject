<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Establish a database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get the date range for SQL queries
function getDateRange($range) {
    switch ($range) {
        case "today":
            $start_date = date("Y-m-d 00:00:00");
            $end_date = date("Y-m-d 23:59:59");
            break;
        case "this_week":
            $start_date = date('Y-m-d 00:00:00', strtotime('monday this week'));
            $end_date = date('Y-m-d 23:59:59', strtotime('sunday this week'));
            break;
        case "this_month":
            $start_date = date("Y-m-01 00:00:00");
            $end_date = date("Y-m-t 23:59:59");
            break;
        case "this_year":
            $start_date = date("Y-01-01 00:00:00");
            $end_date = date("Y-12-31 23:59:59");
            break;
        default:
            $start_date = null;
            $end_date = null;
            break;
    }
    return array($start_date, $end_date);
}

// Get the list of product categories
$categories = $pdo->query("SELECT DISTINCT category_name FROM cafe_product")->fetchAll(PDO::FETCH_ASSOC);

// Initialize total sales and top products
$totalSales = 0.00;
$topProducts = [];
$selectedCategory = '';
$date_range = 'today'; // Default date range

// Check if the date range form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date_range'])) {
    // Get the selected date range
    $date_range = $_POST['date_range'];

    // Get start and end dates for the selected range
    list($start_date, $end_date) = getDateRange($date_range);

    // Fetch total sales for the selected date range for completed orders only
    $stmt = $pdo->prepare("SELECT SUM(total_price) as total_sales FROM cafe_order WHERE status = 'complete' AND order_date BETWEEN ? AND ?");
    if ($stmt->execute([$start_date, $end_date])) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            $totalSales = $result['total_sales'];
            $totalSales = $totalSales !== null ? number_format((float)$totalSales, 2) : number_format(0, 2); // Ensure totalSales is not null
        } else {
            $totalSales = number_format(0, 2);
        }
    } else {
        echo "Error fetching total sales for selected date range: " . $stmt->errorInfo()[2];
    }

    // Fetch top ranking products within the selected date range
    $stmt = $pdo->prepare("SELECT order_items FROM cafe_order WHERE status = 'complete' AND order_date BETWEEN ? AND ?");
    if ($stmt->execute([$start_date, $end_date])) {
        $productSales = array(); // Associative array to store product sales
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order_items = json_decode($row['order_items'], true);
            if (is_array($order_items)) {
                foreach ($order_items as $item) {
                    $prod_name = $item['prod_name'];

                    // Increment the count for this product
                    if (isset($productSales[$prod_name])) {
                        $productSales[$prod_name]++;
                    } else {
                        $productSales[$prod_name] = 1;
                    }
                }
            }
        }
        arsort($productSales); // Sort product sales in descending order
        // Get the top 10 products
        $topProducts = array_slice($productSales, 0, 10, true);
    }
}

// Check if the category filter form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['category_name'])) {
    // Get the selected category
    $selectedCategory = $_POST['category_name'];

    // Get start and end dates for the current date range
    list($start_date, $end_date) = getDateRange($date_range);

    // Fetch top ranking products within the selected category and date range
    $stmt = $pdo->prepare("SELECT order_items FROM cafe_order WHERE status = 'complete' AND order_date BETWEEN ? AND ?");
    if ($stmt->execute([$start_date, $end_date])) {
        $productSales = array(); // Associative array to store product sales
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order_items = json_decode($row['order_items'], true);
            if (is_array($order_items)) {
                foreach ($order_items as $item) {
                    $prod_name = $item['prod_name'];

                    // Fetch the category of the product
                    $stmt_category = $pdo->prepare("SELECT category_name FROM cafe_product WHERE prod_name = ?");
                    if ($stmt_category->execute([$prod_name])) {
                        $product_category = $stmt_category->fetch(PDO::FETCH_ASSOC);
                        if ($product_category !== false) {
                            $product_category = $product_category['category_name'];

                            // Increment the count for this product if it matches the selected category
                            if ($product_category == $selectedCategory) {
                                if (isset($productSales[$prod_name])) {
                                    $productSales[$prod_name]++;
                                } else {
                                    $productSales[$prod_name] = 1;
                                }
                            }
                        }
                    }
                }
            }
        }
        arsort($productSales); // Sort product sales in descending order
        // Get the top 10 products
        $topProducts = array_slice($productSales, 0, 10, true);
    }
}

// If no form is submitted, fetch total sales for today by default for completed orders only
if (!isset($_POST['date_range']) && !isset($_POST['category_name'])) {
    $today_start = date("Y-m-d 00:00:00");
    $today_end = date("Y-m-d 23:59:59");
    $stmt = $pdo->prepare("SELECT SUM(total_price) as total_sales FROM cafe_order WHERE status = 'complete' AND order_date BETWEEN ? AND ?");
    if ($stmt->execute([$today_start, $today_end])) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            $totalSales = $result['total_sales'];
            $totalSales = $totalSales !== null ? number_format((float)$totalSales, 2) : number_format(0, 2); // Ensure totalSales is not null
        } else {
            $totalSales = number_format(0, 2);
        }
    } else {
        echo "Error fetching total sales for today: " . $stmt->errorInfo()[2];
    }

    // Fetch top ranking products for today by default
    $stmt = $pdo->prepare("SELECT order_items FROM cafe_order WHERE status = 'complete' AND order_date BETWEEN ? AND ?");
    if ($stmt->execute([$today_start, $today_end])) {
        $productSales = array(); // Associative array to store product sales
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $order_items = json_decode($row['order_items'], true);
            if (is_array($order_items)) {
                foreach ($order_items as $item) {
                    $prod_name = $item['prod_name'];

                    // Increment the count for this product
                    if (isset($productSales[$prod_name])) {
                        $productSales[$prod_name]++;
                    } else {
                        $productSales[$prod_name] = 1;
                    }
                }
            }
        }
        arsort($productSales); // Sort product sales in descending order
        // Get the top 10 products
        $topProducts = array_slice($productSales, 0, 10, true);
    } else {
        echo "Error fetching top products for today: " . $stmt->errorInfo()[2];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>DASHBOARD ADMIN</title>
    <link rel="stylesheet" href="stylef.css" />
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <!-- Include Chart.js for graph/chart rendering -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <!-- Include jQuery for easier AJAX handling -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .centered {
            margin: 0; /* Remove default margin */
        }
        .main-top {
            display: fixed;
            justify-content: space-between;
        }
        .card {
            width: 300px;
            height: 200px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .top-products {
            margin: 8px 0;
            padding: 0 12px;
            text-align: left;
            overflow-y: auto;
        }
        .table-container {
            height: 130px;
            overflow-y: auto;
            margin-top: 8px;
        }
        .top-products table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
        }
        .top-products th, .top-products td {
            border: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            padding: 4px;
            text-align: justify;
        }
        .top-products th:first-child,
        .top-products td:first-child {
            border-left: none;
        }
        .salescontainer{
            margin-left: 320px;
            margin-top: -800px;
            position: static;
            overflow-x: hidden;
            overflow-y: hidden;
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
<div class="salescontainer">
    <section class="main">
        <section class="main-course">
            <div class="main-top">
                <h1 style="margin-right:10px; margin-left:2px">SALES REPORT</h1>
            </div>
            <div class="course-box date-range">
                <label for="startDate" class="date-range">Start:</label>
                <input type="date" id="startDate">
                <label for="endDate" class="date-range">End:</label>
                <input type="date" id="endDate">
                <input type="button" value="View" onclick="fetchCustomSalesData()" class="date-range" style="border: 3px solid black; background-color: black; color: white; border-radius: 5px;">
                <!-- Chart area -->
                <div class="course">
                    <canvas id="salesChart" width="400" height="130"></canvas>
                </div>
            </div>

            <br>
            <!-- Date Range Selector -->
            <div class="date-range">
              <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                  <label for="date_range">Select Date Range:</label>
                  <select name="date_range" id="date_range">
                      <option value="today" <?php if($date_range == 'today') echo 'selected'; ?>>Today</option>
                      <option value="this_week" <?php if($date_range == 'this_week') echo 'selected'; ?>>This Week</option>
                      <option value="this_month" <?php if($date_range == 'this_month') echo 'selected'; ?>>This Month</option>
                      <option value="this_year" <?php if($date_range == 'this_year') echo 'selected'; ?>>This Year</option>
                  </select>
                  <input type="submit" value="Apply" style="border: 2px solid black; background-color: black; color: white; border-radius: 5px;">
              </form>
            </div>

            <div class="main-skills" style="margin-top: -20px;">
                <div class="card" style = "display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;">
                  <h2 class="centered">RM<?php echo $totalSales;?></h2>
                  <p>SALES</p>
                </div>

                <!-- Display top product rank table -->
                <div class="card">
                    <div class="top-products">
                        <h3 style="text-align: left; margin-bottom: 5px; margin-top: -5px;">Top Selling Products</h3>
                        <!-- Category Filter -->
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                            <input type="hidden" name="date_range" value="<?php echo $date_range; ?>">
                            <label for="category_name">Select Category:</label>
                            <select name="category_name" id="category_name">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['category_name']); ?>" <?php if($selectedCategory == $category['category_name']) echo 'selected'; ?>><?php echo htmlspecialchars($category['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="submit" value="Apply" style="border: 2px solid black; background-color: black; color: white; border-radius: 5px; font-size: 10px;">
                        </form>
                        <div class="table-container">
                            <table>
                                <?php
                                // Check if $topProducts is set and not null
                                if (isset($topProducts) && is_array($topProducts)) {
                                    foreach ($topProducts as $product => $count) {
                                        echo "<tr><td>$product</td><td>$count</td></tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='2'>No data available</td></tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
         </section>
      </div>

      <script>
// Function to fetch sales data for custom date range and update the chart
function fetchCustomSalesData() {
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    // Call fetchSalesData with custom date range
    fetchSalesData(startDate, endDate);
}

// Function to fetch sales data and update the chart
function fetchSalesData(startDate, endDate) {
    $.ajax({
        url: 'fetch_sales_data.php',
        type: 'POST',
        data: {
            action: 'fetch_sales',
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            var data = JSON.parse(response);
            updateSalesChart(data.labels, data.sales);
            updateTotalSales(data.totalSales);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching sales data:', error);
        }
    });
}

// Function to fetch sales data based on selected date range
function fetchSalesDataForRange() {
    var dateRange = $('#date_range').val();
    var startDate, endDate;

    switch (dateRange) {
        case "today":
            startDate = new Date().toISOString().slice(0,10) + " 00:00:00";
            endDate = new Date().toISOString().slice(0,10) + " 23:59:59";
            break;
        case "this_week":
            startDate = new Date(new Date().setDate(new Date().getDate() - new Date().getDay() + 1)).toISOString().slice(0,10) + " 00:00:00";
            endDate = new Date(new Date().setDate(new Date().getDate() - new Date().getDay() + 7)).toISOString().slice(0,10) + " 23:59:59";
            break;
        case "this_month":
            startDate = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0,10) + " 00:00:00";
            endDate = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().slice(0,10) + " 23:59:59";
            break;
        case "this_year":
            startDate = new Date(new Date().getFullYear(), 0, 1).toISOString().slice(0,10) + " 00:00:00";
            endDate = new Date(new Date().getFullYear(), 11, 31).toISOString().slice(0,10) + " 23:59:59";
            break;
    }

    fetchSalesData(startDate, endDate);
}

// Function to fetch top products based on selected category and date range
function fetchTopProducts() {
    var dateRange = $('#date_range').val();
    var category = $('#category_name').val();

    var startDate, endDate;
    switch (dateRange) {
        case "today":
            startDate = new Date().toISOString().slice(0,10) + " 00:00:00";
            endDate = new Date().toISOString().slice(0,10) + " 23:59:59";
            break;
        case "this_week":
            startDate = new Date(new Date().setDate(new Date().getDate() - new Date().getDay() + 1)).toISOString().slice(0,10) + " 00:00:00";
            endDate = new Date(new Date().setDate(new Date().getDate() - new Date().getDay() + 7)).toISOString().slice(0,10) + " 23:59:59";
            break;
        case "this_month":
            startDate = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0,10) + " 00:00:00";
            endDate = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toISOString().slice(0,10) + " 23:59:59";
            break;
        case "this_year":
            startDate = new Date(new Date().getFullYear(), 0, 1).toISOString().slice(0,10) + " 00:00:00";
            endDate = new Date(new Date().getFullYear(), 11, 31).toISOString().slice(0,10) + " 23:59:59";
            break;
    }

    $.ajax({
        url: 'fetch_sales_data.php',
        type: 'POST',
        data: {
            action: 'fetch_top_products',
            start_date: startDate,
            end_date: endDate,
            category: category
        },
        success: function(response) {
            var data = JSON.parse(response);
            updateTopProducts(data.topProducts);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching top products:', error);
        }
    });
}

// Function to update the sales chart
function updateSalesChart(labels, sales) {
    var ctx = document.getElementById('salesChart').getContext('2d');
    if(window.myChart instanceof Chart){
        window.myChart.destroy();
    }
    window.myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Sales',
                data: sales,
                borderColor: 'black',
                backgroundColor: 'rgba(0, 0, 0, 0.2)',
                borderWidth: 2,
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: 'black'
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Date Range'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Sales'
                    }
                }
            }
        }
    });
}

// Function to update the total sales value
function updateTotalSales(totalSales) {
    $('#totalSales').text('RM' + parseFloat(totalSales).toFixed(2));
}

// Function to update the top products table
function updateTopProducts(topProducts) {
    var table = $('#topProductsTable');
    table.empty();

    if (topProducts.length === 0) {
        table.append('<tr><td colspan="2">No data available</td></tr>');
    } else {
        topProducts.forEach(function(product) {
            table.append('<tr><td>' + product.name + '</td><td>' + product.count + '</td></tr>');
        });
    }
}

$(document).ready(function() {
    fetchSalesDataForRange();
});
</script>
</body>
</html>
