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

// Check if the connection is successful
if (!$pdo) {
    die("Connection failed");
}

// Initialize date range variables
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Ensure the date format is YYYY-MM-DD for MySQL compatibility
$start_date = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : '';
$end_date = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : '';

// Create the SQL query with date range filter
$sql = 'SELECT * FROM cafe_order';
if (!empty($start_date) && !empty($end_date)) {
    $sql .= ' WHERE DATE(order_date) BETWEEN :start_date AND :end_date';
}
$sql .= ' ORDER BY order_date DESC';

$stmt = $pdo->prepare($sql);

// Bind date range parameters if set
if (!empty($start_date) && !empty($end_date)) {
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
}

$stmt->execute();
?>

<span style="font-family: verdana, geneva, sans-serif;">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>DASHBOARD STAFF</title>
    <link rel="stylesheet" href="stylef.css" />
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .small-font {
            font-size: 12px;
        }

        .container2nd {
            margin-left: 300px; /* Adjust this based on the width of your sidebar */
            margin-top: 30px;
            padding: 20px;
            height: 80vh;
            overflow: hidden; /* Hide the scrollbar for the container */
            position: fixed;
        }

        .tableStaff {
            width: 75%; /* Adjust the width as needed */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: calc(95vh - 80px); /* Adjust height based on the header height */
            overflow-y: scroll;
            margin-left: auto;
            margin-top: -5px;
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
            <div class="main-top" >
                <h1 style="margin-right:10px; margin-left:-30px; margin-top:-40px;">Orders</h1> 
                <p style="margin-right:1200px; margin-top:-40px;">staff</p>
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
            <h2>Order list</h2>
            <table class="table1">
                <thead>
                    <tr style="font-size: 12px;">
                        <th class="text-center">Order ID</th>
                        <th class="text-center">Order Items</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Order Date</th>
                        <th class="text-center">Customer Name</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Contact</th>
                        <th class="text-center">Payment method</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Loop through each row in the result set and display the data in table rows
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td class='small-font'>{$row['order_id']}</td>";

                        // Decode the JSON string only if it's not null and in the correct format
                        if (!empty($row['order_items'])) {
                            $order_items = json_decode($row['order_items'], true);
                            if (is_array($order_items)) {
                                echo "<td class='small-font'>";
                                foreach ($order_items as $item) {
                                    echo "{$item['prod_name']} {$item['quantity']}x - RM{$item['prod_price']}<br>";
                                    if (!empty($item['special_instructions'])) {
                                        echo "Special Instructions: {$item['special_instructions']}<br>";
                                    }
                                }
                                echo "</td>";
                            } else {
                                echo "<td class='small-font'>Invalid Data</td>";
                            }
                        } else {
                            echo "<td class='small-font'>No Items</td>";
                        }

                        echo "<td class='small-font'>RM {$row['total_price']}</td>";
                        echo "<td class='small-font'>{$row['order_date']}</td>";
                        echo "<td class='small-font'>{$row['customer_name']}</td>";
                        echo "<td class='small-font'>{$row['customer_email']}</td>";
                        echo "<td class='small-font'>{$row['customer_phoneno']}</td>";
                        echo "<td class='small-font'>{$row['payment_method']}</td>";

                        // Add status dropdown
                        echo "<td class='small-font'>";
                        echo "<form>";
                        echo "<input type='hidden' name='order_id' value='{$row['order_id']}'>";
                        echo "<select name='status' onchange='updateOrderStatus(this)'>";
                        echo "<option value='uncompleted' " . ($row['status'] == 'Incomplete' ? 'selected' : '') . ">Incomplete</option>";
                        echo "<option value='complete' " . ($row['status'] == 'Complete' ? 'selected' : '') . ">Complete</option>";
                        echo "<option value='pending' " . ($row['status'] == 'Pending' ? 'selected' : '') . ">Pending</option>";
                        echo "</select>";
                        echo "</form>";
                        echo "</td>";

                        // Add action buttons in the last column
                        echo "<td>";
                        echo "<form action='editOrder.php' method='post'>";
                        echo "<input type='hidden' name='update_id' value='{$row['order_id']}'>";
                        echo "<button type='submit' class='btn btn-primary' style='height:30px; background-color:rgb(71, 182, 71); padding: 4px; border-radius: 2px; color: white; margin-bottom:5px;'>Edit</button>";
                        echo "</form>";
                    
                        echo "<form action='deleteOrder.php' method='post'>";
                        echo "<input type='hidden' name='order_id' value='{$row['order_id']}'>";
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
    <script>
    function updateOrderStatus(selectElement) {
        var form = selectElement.closest('form');
        var order_id = form.querySelector('input[name="order_id"]').value;
        var status = selectElement.value;

        $.ajax({
            url: 'updateOrderStatus.php',
            type: 'POST',
            data: {
                order_id: order_id,
                status: status
            },
            dataType: 'json',  // Ensure the response is parsed as JSON
            success: function(response) {
                if(response.success) {
                    alert('Order status updated successfully.');
                } else {
                    alert('Failed to update order status: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error updating order status: ' + error);
            }
        });
    }
</script>
</body>
</html>
</span>
