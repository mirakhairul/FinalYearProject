<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check for login success and redirect to home2.php if successful
if (isset($_GET['login_success']) && $_GET['login_success'] == 1) {
    echo "<script>alert('Logged in!')</script>";
    echo "<script>window.location.assign('home2.php')</script>";
}

// Check for logout success and redirect to home.php if successful
if (isset($_GET['logout_success']) && $_GET['logout_success'] == 1) {
    echo "<script>alert('Logged out!')</script>";
    echo "<script>window.location.assign('home.php')</script>";
}

// Start session to manage user data
session_start();

// Check if cart is not empty and count the items
if (!empty($_SESSION['cart'])) {
    $printCount = count($_SESSION['cart']);
}
else {
    $printCount = 0;
}

// Get the username and login status from session
$printUsername = isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : 'None';
$loggedIn = isset($_SESSION['customer_email']);

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
  
  <!-- for navigation bar-->
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap">
   
   <style>
        .cart_div {
            float: right;
            font-weight: bold;
            position: relative;
        }

        .cart_div a {
            color: #000;
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
        // Display the number of items in the cart
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

<!-- Content -->
<div class ="w3-row" style="padding:100px 0.5px">
  <div class="w3-content">
    <h2 style="color:whitesmoke; font-size: 25px; text-decoration-line: underline"><b><center>Promotions</center></b></h2>
  </div>

  <div class="w3-padding-16">
      <div class="w3-content">
              <center><p style="font-family: Arial, Helvetica, sans-serif; color: white">There are no promotions or vouchers available. Please come back later.</p></center>
  </div>
</div>

<!-- Footer -->
<footer>
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