<?php
// Display errors for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Handle login success by displaying an alert and redirecting to 'home2.php'
if (isset($_GET['login_success']) && $_GET['login_success'] == 1) {
    echo "<script>alert('Logged in!')</script>";
    echo "<script>window.location.assign('home2.php')</script>";
}

// Handle logout success by displaying an alert and redirecting to 'home.php'
if (isset($_GET['logout_success']) && $_GET['logout_success'] == 1) {
    echo "<script>alert('Logged out!')</script>";
    echo "<script>window.location.assign('home.php')</script>";
}

// Retrieve the username from the session if logged in, otherwise set it to 'None'
$printUsername = isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : 'None';
$loggedIn = isset($_SESSION['customer_email']);

try {
    // Connect to the MySQL database using PDO
    $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve customer details from the form
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_email = $_POST['customer_email'] ?? '';
    $customer_phoneno = $_POST['customer_phoneno'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    // Store customer details in the session
    $_SESSION['customer_name'] = $customer_name;
    $_SESSION['customer_email'] = $customer_email;
    $_SESSION['customer_phoneno'] = $customer_phoneno;
    $_SESSION['payment_method'] = $payment_method;

    // Calculate the total price of the cart items
    $total_price = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $total_price += $item['prod_price'] * $item['quantity'];
        }
    }
    $_SESSION['total_price'] = number_format($total_price, 2);

    try {
        // Insert order details into the database
        $order_id = uniqid();
        $sql = "INSERT INTO cafe_order (order_id, customer_name, customer_email, customer_phoneno, payment_method, total_price) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$order_id, $customer_name, $customer_email, $customer_phoneno, $payment_method, $total_price]);

        // Redirect to the receipt page
        header("Location: receipt.php");
        exit();
    } catch (PDOException $e) {
        // Handle error during data insertion
        die("Error inserting data: " . $e->getMessage());
    }
}

// Count the number of items in the cart
$cart_count = isset($_SESSION["cart"]) ? count(array_keys($_SESSION["cart"])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>AK HAUTE PAUSA</title>
  
  <!-- Styles and fonts for navigation bar and page layout -->
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap">

   <style>
    .checkout-container{
        background-color: #ffffff;
        width: 500px;
        padding: 12px;
        margin-left: 30px;
        margin-right: auto;
        margin-top: 50px;
        border-radius: 5px;
        display: grid;
        grid-template-columns: auto 1fr auto;
    }

    .order-container{
        background-color: #ffffff;
        width: 60%; /* Adjusted width */
        padding: 10px 20px 15px 20px; /* Adjusted padding */
        border-radius: 5px;
        text-align: left; /* Ensure left alignment */
    }

    .col-25 {
        -ms-flex: 25%; 
        flex: 25%;
        padding: 0 16px;
    }
  
    h2 {
        text-align: left;
        margin-bottom: 10px;
        text-decoration: underline;
        grid-row: 1;
    }

    .order-container h4{
        text-decoration: underline;
        text-align: center;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        display:block;
        margin-bottom: 5px;
    }

    input, textarea {
        width: 150%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .customer-details {
        border: 1px solid #ddd;
        padding: 20px;
        margin-bottom: 20px;
    }

    .payment-method p {
        margin-bottom: 10px;
        margin-top: 5px;
        font-size: 14px;
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

    .payment-options{
        display:inline-block;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .payment-options label {
        display:inline-flex;
        align-items: center;
    }

    input[type="radio"] {
        margin-right: 10px;
    }

    .error{
        color: red;
    }

    /* Media screen queries for responsiveness */
    @media (max-width: 768px) {
    .checkout-container {
        width: 90%;
      }

    .order-container{
        margin-left: 15px;
        width: 500px;
    }

    .col-25 {
        margin-bottom: 20px;
    }
    }

    .cart_div {
        float:right;
        font-weight:bold;
        position:relative;
	}

    .cart_div a {
        color:#000;
    }	

    .cart_div span {
        font-size: 12px;
        line-height: 14px;
        background: #7a4cb0;
        padding: 2px;
        border: 2px solid #fff;
        border-radius: 50%;
        position: absolute;
        top: -1px;
        left: 13px;
        color: #fff;
        width: 20px;
        height: 20px;
        text-align: center;
    }

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
    <?php
         $cart_count = isset($_SESSION["cart"]) ? count(array_keys($_SESSION["cart"])) : 0;
    ?>
    <div class="cart_div">
        <a href="cart.php"><i class="fas fa-shopping-bag" style="color: white"></i>
        <?php if ($cart_count > 0): ?>
            <span><?php echo $cart_count; ?></span>
        <?php endif; ?>
        </a>
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

<!-- checkout form-->
<div class="w3-row w3-display-flex" style="padding:50px 0.5px; margin-left: 20%;">
    <div class="checkout-container w3-half">
        <form action="receipt.php" method="post">
            <h3><strong>Checkout</strong></h3>
            <h5 style="text-decoration: underline;">Customer Details</h5>
            <p>Please complete this form.<span class="error">*</span></p><br>

            <div class="form-group">
                <label for="full-name">Full Name<span class="error">*</span></label>
                <input type="text" id="full-name" name="customer_name" value="<?php echo isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email <span class="error">*</span></label>
                <input type="email" id="email" name="customer_email" value="<?php echo isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="phone-number">Phone Number<span class="error">*</span></label>
                <input type="tel" id="phone-number" name="customer_phoneno" value="<?php echo isset($_SESSION['customer_phoneno']) ? $_SESSION['customer_phoneno'] : ''; ?>" required>
            </div>
            <section class="payment-method">
                <h5>Payment Method</h5>
                <p>Choose your preferred payment method<span class="error">*</span></p>
                <div class="payment-options">
                    <label for="pay-at-cashier">
                        <p><input class="w3-radio w3-margin-left" type="radio" id="pay-at-cashier" name="payment_method" value="Pay at cashier" required>Pay at Cashier</p>
                    </label>
                    <label for="online-banking">
                        <p><input class="w3-radio w3-margin-left" type="radio" id="online-banking" name="payment_method" value="Online Transfer" required>Online Transfer</p>
                    </label>
                </div>
            </section>
            <button class="w3-right" style="margin-right: -160px;" type="submit">Place Order</button>

            <!-- Store cart items in a hidden field -->
            <?php if(isset($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $key => $item): ?>
                    <input type="hidden" name="cart[<?php echo $key; ?>][prod_name]" value="<?php echo $item['prod_name']; ?>">
                    <input type="hidden" name="cart[<?php echo $key; ?>][prod_price]" value="<?php echo number_format($item['prod_price'], 2); ?>">
                    <input type="hidden" name="cart[<?php echo $key; ?>][quantity]" value="<?php echo $item['quantity']; ?>">
                    <input type="hidden" name="cart[<?php echo $key; ?>][special_instructions]" value="<?php echo $item['special_instructions']; ?>">
                <?php endforeach; ?>
            <?php endif; ?>
        </form>
    </div>

    <div class="col-25 w3-half" style="padding:10px; margin-top: 40px;">
        <div class="order-container">
        <h4><strong><center>Order Summary</center></strong></h4>
        
        <?php	
          if(isset($_SESSION["cart"])){
            $total_price = 0;
            foreach ($_SESSION["cart"] as $product){
              // Ensure special_instructions is set
              $product["special_instructions"] = $product["special_instructions"] ?? '';
              $notes_parts = explode(". ", $product["special_instructions"], 3);
              $notes = implode("<br>&nbsp;&nbsp;&nbsp;&nbsp;", array_filter([$notes_parts[0] ?? '', $notes_parts[1] ?? '', $notes_parts[2] ?? '']));
        ?>

        <td>
        <form style="text-align: left; padding-top: 2px;" method="get" action="cart.php">
        <div style="display: table-row; align-items: justify;">
            <div style="display: table-cell; width: 150px; padding-left: 15px;"><?php echo $product["prod_name"]; ?></div>
            <div style="display: table-cell; width: 50px; padding-left: 20px;"><?php echo $product["quantity"]; ?>x</div>
            <div style="display: table-cell; width: 100px; padding-left: 20px;">RM<?php echo number_format($product["prod_price"] * $product["quantity"], 2); ?></div>
        </div>
        <div style="display: table-row; align-items: justify;">
            <div style="display: table-cell; width: 300px; padding-left: 15px;"><?php echo "Notes:<br>&nbsp;&nbsp;&nbsp;&nbsp;" . $notes; ?></div>
        </div>
        <input type='hidden' name='prod_code' value="<?php echo $product["prod_code"]; ?>" />
        <input type='hidden' name='action' value="change" />
        <input type='hidden' name='quantity' value="<?php echo $product["quantity"]; ?>"/>
        </form>
        </td>
        <hr>
        <?php
          $total_price += $product["prod_price"] * $product["quantity"];
          }
        ?>
        <tr><strong style="text-align: right; display: block; margin-right: 20px;">TOTAL: <?php echo "RM".number_format($total_price, 2); ?></strong></tr>
        
        <?php
        }
        ?>
        </div>
    </div>
</div>

<footer>
<!-- Footer -->
<div class="footer">
  <div class="socialIcons">
    <a href="https://api.whatsapp.com/send?phone=60134705188"><i class="fab fa-whatsapp"></i></a>
    <a href="https://www.facebook.com/profile.php?id=61552651635626&mibextid=ZbWKwL"><i class="fab fa-facebook"></i></a>
    <a href="https://www.instagram.com/ak_hautepausacaffe?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="><i class="fab fa-instagram"></i></a>
    <a href="https://www.tiktok.com/@akhautepausacafe?is_from_webapp=1&sender_device=pc"><i class="fab fa-tiktok"></i></a>
  </div>
  <p>Copyright © 2024 AK Haute Pausa Caffe. All rights reserved.</p>
</div>
</footer>
</body>
</html>
