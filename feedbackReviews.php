<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$con = new mysqli("localhost", "root", "", "akcafe");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Ensure the date format is YYYY-MM-DD for MySQL compatibility
$start_date = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : '';
$end_date = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : '';

// Create the SQL query
$sql = 'SELECT * FROM cafe_feedback';
if (!empty($start_date) && !empty($end_date)) {
    $sql .= ' WHERE DATE(created_at) BETWEEN ? AND ?';
}
$sql .= ' ORDER BY id DESC';

$stmt = $con->prepare($sql);

// Bind date range parameters if set
if (!empty($start_date) && !empty($end_date)) {
    $stmt->bind_param('ss', $start_date, $end_date);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DASHBOARD ADMIN</title>
    <link rel="stylesheet" href="stylef.css">
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
            display: flex;
            justify-content: space-between;
        }
        .tableStaff {
            width: 70%; /* Adjust the width as needed */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: auto; /* Adjust height based on the header height */
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
            <h1 style="margin-right:10px; margin-left:-30px; margin-top:-40px;">Feedback</h1> 
            <p style="margin-right:1200px; margin-top:-40px;">admin</p>
        </div>
        <!-- Add the date range filter form -->
        <form method="GET" action="" style="font-size: 12px;">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" style="font-size: 12px;" value="<?php echo htmlspecialchars($start_date); ?>">
            <label style="margin-left:2px;" for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" style="font-size: 12px;" value="<?php echo htmlspecialchars($end_date); ?>">
            <button type="submit" style="background-color: black; border-radius:2px; color:white; font-size: 12px; padding:2px; margin-left:2px;" >Filter</button>
        </form>
    </section>

    <div class="tableStaff">
        <h2>Feedback list</h2>
        <table class="table1">
            <thead>
                <tr style="font-size: 12px;">
                    <th class="text-center">Comment</th>  
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td class='small-font'>{$row['comment']}</td>";
                    echo "<td>";
                    echo "<form action='deleteFeedback.php' method='post'>";
                    echo "<input type='hidden' name='id' value='{$row['id']}'>";
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

<?php
$stmt->close();
$con->close();
?>
