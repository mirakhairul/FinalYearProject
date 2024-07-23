<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session to manage user data
session_start();

// Handle login success and redirect to home2.php
if (isset($_GET['login_success']) && $_GET['login_success'] == 1) {
    echo "<script>alert('Logged in!')</script>";
    echo "<script>window.location.assign('home2.php')</script>";
}

// Handle logout success and redirect to home.php
if (isset($_GET['logout_success']) && $_GET['logout_success'] == 1) {
    echo "<script>alert('Logged out!')</script>";
    echo "<script>window.location.assign('home.php')</script>";
}

// Get the username and login status from session
$printUsername = isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : 'None';
$loggedIn = isset($_SESSION['customer_email']);

// Set the timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

// Retrieve data from form submission
$order_id = substr(uniqid(), 0, 10); // Generate a unique order ID with the first 10 characters of the unique ID
$order_date = date("Y-m-d H:i:s"); // Get current date and time
$customer_name = isset($_POST['customer_name']) ? $_POST['customer_name'] : 'Unknown';
$customer_email = isset($_POST['customer_email']) ? $_POST['customer_email'] : 'Unknown';
$customer_phoneno = isset($_POST['customer_phoneno']) ? $_POST['customer_phoneno'] : 'Unknown';
$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'Unknown';
$total_price = 0;
$order_items = isset($_POST['cart']) ? $_POST['cart'] : [];

// Calculate total price
foreach ($order_items as $item) {
    // Check if quantity is provided, otherwise set a default value
    $quantity = isset($item['quantity']) ? $item['quantity'] : 1;
    $total_price += $item['prod_price'] * $quantity;
}

// Sample database connection (replace this with your actual connection code)
$con = mysqli_connect("localhost", "root", "", "akcafe");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Encode order items array to JSON
$order_items_json = json_encode($order_items);

// Insert order details into the database
$stmt = $con->prepare("INSERT INTO cafe_order (order_id, order_date, customer_name, customer_email, customer_phoneno, payment_method, total_price, order_items, special_instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $con->error);
}

// Collect special instructions
$special_instructions = [];
foreach ($order_items as $item) {
    $special_instructions[] = isset($item['special_instructions']) ? $item['special_instructions'] : '';
}
$special_instructions = implode('; ', $special_instructions);

// Bind parameters and execute the statement
$stmt->bind_param("sssssssss", $order_id, $order_date, $customer_name, $customer_email, $customer_phoneno, $payment_method, $total_price, $order_items_json, $special_instructions);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

// Update stock for each ordered item
foreach ($order_items as $item) {
    if (isset($item['prod_code']) && isset($item['quantity'])) {
        $code = $item['prod_code'];
        $quantity = $item['quantity'];
        
        // Check if category_name is set
        if (isset($item['category_name'])) {
            $category = strtolower($item['category_name']);
        } else {
            $category = '';
        }

        if (!in_array($category, ['noncoffee', 'coffee', 'signature', 'frappe', 'sparkling', 'cheese foam', 'matcha', 'non coffee'])) {
            // Retrieve current stock
            $result = mysqli_query($con, "SELECT stock FROM cafe_product WHERE prod_code='$code'");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                if ($row) {
                    $stock = $row['stock'];

                    // Update stock in the database
                    $new_stock = $stock - $quantity;
                    $update_stock_query = "UPDATE cafe_product SET stock='$new_stock' WHERE prod_code='$code'";
                    if (!mysqli_query($con, $update_stock_query)) {
                        echo "Error updating stock: " . mysqli_error($con);
                    }
                } else {
                    echo "Error fetching stock: " . mysqli_error($con);
                }
            }
        }
    }
}

// Close the statement and connection
$stmt->close();
$con->close();

// Clear the cart session after successful order processing
unset($_SESSION["cart"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>AK HAUTE PAUSA</title>
  <!-- for navigation bar-->
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <!-- Google Fonts Link-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap">
  
  <style>
    .w3-dropdown-content {
      position: absolute;
      top: 45px;
      right: -5px;
      background-color: white;
      z-index: 1200;
    }

    body::-webkit-scrollbar {
      width: 0px;
      display: none;
    }  

    .receipt-container {
      background-color: #ffffff;
      width: 600px;
      padding: 20px;
      margin: 0 auto;
      margin-top: 50px;
      border-radius: 5px;
      display: grid;
      position: static;
      position: relative;
    }

    label {
      display: block;
      margin-bottom: 5px;
    }

    button {
      background-color: #000000;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      justify-self: end;
    }

    button:hover {
      background-color: #8663bb;
    }

    .back-button {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    /* Media screen queries for responsiveness */
    @media (max-width: 768px) {
      .receipt-container {
        width: 90%;
      }
    }

    ul {
      list-style-type: none;
      padding: 0;
    }

    ul li {
      margin-bottom: 10px;
    }
  </style>
</head>
<body style="background-color: #644F61">

<header>
  <!-- navigation bar-->
  <div class="wrapper">
    <nav>
      <input type="checkbox" id="show-menu">
      <label for="show-menu" class="menu-icon"><i class="fas fa-bars"></i></label>
      <div class="content">
        <div class="logo"><a>AK Haute Pausa</a></div>
        <ul class="links">
          <li><a href="home.php">Home</a></li>
          <li><a href="product.php">Order</a></li>
          <li><a href="menu.php">Menu</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="feedback.php">Tell Us</a></li>
          <li><a href="promotion.php">Promotions</a></li>
        </ul>
      </div>
    </nav>
    <div class="end-wrapper">
      <nav>
        <div class="cart_div">
          <a href="cart.php"><i class="fas fa-shopping-bag" style="color: white"></i></a>
        </div>
        <div class="w3-dropdown-hover w3-right">
        <label class="profile-icon"><i class="far fa-user-circle"></i></label>
        <div class="w3-dropdown-content w3-bar-block w3-border">
            <?php if($loggedIn): ?>
                <a href="myProfile.php" class="w3-bar-item w3-button w3-medium">My Profile</a>
                <a href="logout_user.php" class="w3-bar-item w3-button w3-medium">Logout</a>
            <?php else: ?>
                <a href="register.html" class="w3-bar-item w3-button w3-medium">Register</a>
                <a href="login_customer.php" class="w3-bar-item w3-button w3-medium">Login</a>
            <?php endif; ?>
        </div>
      </nav>
    </div>
  </div>
</header>
<!-- navigation bar end-->

<!-- receipt -->
<div class="w3-row w3-display-flex" style="padding:150px 0.5px">
  <div class="receipt-container">
    <button class="back-button" id="backButton">Back to Main Page</button>
    <h3><strong>Receipt</strong></h3>
    <p>Thank you for your order!</p>
    <h4><strong>Order details</strong></h4>
    <p><strong> # <?php echo $order_id; ?></strong></p>
    <ul>
      <li>Placed on: <?php echo $order_date; ?></li>
      <li>Name: <?php echo $customer_name; ?></li>
      <li>Email: <?php echo $customer_email; ?></li>
      <li>Phone number: <?php echo $customer_phoneno; ?></li>
      <li>Payment method: <?php echo $payment_method; ?></li>
      <li>Order items:</li>
      <ul>
        <?php foreach ($order_items as $item): ?>
          <li><?php echo $item['prod_name']; ?> <?php echo $item['quantity']; ?>x - RM<?php echo number_format($item['quantity'] * $item['prod_price'], 2); ?><br>
          Special Instructions: <?php echo $item['special_instructions']; ?>
          </li>
        <?php endforeach; ?>
        <li>Total price: RM <?php echo number_format($total_price, 2); ?></li>
      </ul>
    </ul>
    <br>
    <center>
      <p>Want to become a member? click here.</p>
      <a href="register.html"><button class="w3-right-center" style="margin:0 auto;" type="button">Register Now</button></a>
    </center>
  </div>
</div>

<script>
    // Redirect to home.php when the back button is clicked
    document.getElementById('backButton').addEventListener('click', function() {
        window.location.href = 'home.php';
    });
</script>
</body>
</html>
