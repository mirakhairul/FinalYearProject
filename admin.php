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

  // Fetch total products
  $stmt = $pdo->prepare("SELECT COUNT(*) as prod_id FROM cafe_product");
  $stmt->execute();
  $totalProducts = $stmt->fetch(PDO::FETCH_ASSOC)['prod_id'];

  // Fetch total registered customers
  $stmt = $pdo->prepare("SELECT COUNT(*) as customer_name FROM cafe_customer");
  $stmt->execute();
  $totalCustomers = $stmt->fetch(PDO::FETCH_ASSOC)['customer_name'];

  // Check if form is submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['date_range'])) {
      // Get the selected date range
      $date_range = $_POST['date_range'];

      // Get start and end dates for the selected range
      list($start_date, $end_date) = getDateRange($date_range);

      // Adjust your queries based on the selected date range
      $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_price), 0) as total_sales, COUNT(*) as order_id FROM cafe_order WHERE order_date BETWEEN ? AND ?");
      if ($stmt->execute([$start_date, $end_date])) {
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          $totalSales = $result['total_sales'];
          $totalOrders = $result['order_id'];
      } else {
          echo "Error fetching data for selected date range: " . $stmt->errorInfo()[2];
          $totalSales = 0;
          $totalOrders = 0;
      }
  } else {
      // Fetch total sales and orders for today by default
      $today_start = date("Y-m-d 00:00:00");
      $today_end = date("Y-m-d 23:59:59");
      $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_price), 0) as total_sales, COUNT(*) as order_id FROM cafe_order WHERE order_date BETWEEN ? AND ?");
      if ($stmt->execute([$today_start, $today_end])) {
          $result = $stmt->fetch(PDO::FETCH_ASSOC);
          $totalSales = $result['total_sales'];
          $totalOrders = $result['order_id'];
      } else {
          echo "Error fetching data for today: " . $stmt->errorInfo()[2];
          $totalSales = 0;
          $totalOrders = 0;
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
    <style>
        .card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            width: 300px;
            height: 200px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .centered {
            margin: 0; /* Remove default margin */
        }

        .dashcontainer {
            margin-left: 320px; /* Adjust this based on the width of your sidebar */
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

<div class="dashcontainer">
    <section class="main">
        <div class="main-top">
            <h1 style="margin-right:1px;">Dashboard</h1>
            <p style="margin-left:10px;">admin</p>
        </div>
        <br>
        <!-- Date Range Selector -->
        <div class="date-range">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label for="date_range">Select Date Range:</label>
                <select name="date_range" id="date_range">
                    <option value="today" <?php if(isset($_POST['date_range']) && $_POST['date_range'] == 'today') echo 'selected'; ?>>Today</option>
                    <option value="this_week" <?php if(isset($_POST['date_range']) && $_POST['date_range'] == 'this_week') echo 'selected'; ?>>This Week</option>
                    <option value="this_month" <?php if(isset($_POST['date_range']) && $_POST['date_range'] == 'this_month') echo 'selected'; ?>>This Month</option>
                    <option value="this_year" <?php if(isset($_POST['date_range']) && $_POST['date_range'] == 'this_year') echo 'selected'; ?>>This Year</option>
                </select>
                <input type="submit" value=" Apply " style="border: 2px solid black; background-color: black; color: white; border-radius: 5px;">
            </form>
        </div>
        <div class="main-skills">
            <div class="card">
                <h2 class="centered"><?php echo $totalProducts; ?></h2>
                <p class="centered">PRODUCTS</p>
            </div>
            <div class="card">
                <h2 class="centered">RM<?php echo number_format((float)$totalSales, 2); ?></h2>
                <p class="centered">SALES</p>
            </div>
            <div class="card">
                <h2 class="centered"><?php echo $totalOrders; ?></h2>
                <p class="centered">ORDERS</p>
            </div>
            <div class="card">
                <h2 class="centered"><?php echo $totalCustomers; ?></h2>
                <p class="centered">REG. CUSTOMERS</p>
            </div>
        </div>
    </section>
</div>
</body>
</html>
