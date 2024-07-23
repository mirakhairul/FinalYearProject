<?php
// Display errors for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if login was successful and display an alert, then redirect to 'home2.php'
if (isset($_GET['login_success']) && $_GET['login_success'] == 1) {
    echo "<script>alert('Logged in!')</script>";
    echo "<script>window.location.assign('home2.php')</script>";
}

// Check if logout was successful and display an alert, then redirect to 'home.php'
if (isset($_GET['logout_success']) && $_GET['logout_success'] == 1) {
    echo "<script>alert('Logged out!')</script>";
    echo "<script>window.location.assign('home.php')</script>";
}

// Start the session
session_start();

// Count the number of items in the cart
if (!empty($_SESSION['cart'])) {
    $printCount = count($_SESSION['cart']);
} else {
    $printCount = 0;
}

// Get the customer's email if logged in, otherwise set to 'None'
$printUsername = isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : 'None';
$loggedIn = isset($_SESSION['customer_email']);

// Get the cart count
$cart_count = isset($_SESSION["cart"]) ? count(array_keys($_SESSION["cart"])) : 0;

// Connect to the database
$con = new mysqli("localhost", "root", "", "akcafe");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Handle feedback form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['feedback'])) {
    $feedback = trim($_POST['feedback']);
    if (empty($feedback)) {
        echo json_encode(['success' => false, 'message' => 'No comment submitted']);
    } else {
        $stmt = $con->prepare("INSERT INTO cafe_feedback (comment) VALUES (?)");
        $stmt->bind_param("s", $feedback);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error storing feedback']);
        }
        $stmt->close();
    }
    exit;
}

// Close the database connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <title>AK HAUTE PAUSA</title>
  
  <!-- CSS for navigation bar and layout -->
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

        .feedback-container {
            background-color: white;
            margin: 25px 280px;
            width: 60%;
            height: 70%;
            display: flex;
            flex-wrap: wrap;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            border-radius:2px;
        }

        .feedback-container img {
            flex: 1;
            max-width: 50%;
            max-height: 100%;
            object-fit: cover;
        }

        .feedback-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin-bottom: 300px;
        }

        .feedback-content h2 {
            margin-bottom: 10px;
        }

        .feedback-content form textarea {
            width: 100%;
            height: 250px;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .feedback-content form button {
            background-color:  black;
            color: white;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px; 
        }

        .contact-info {
            margin-top: 20px;
            font-size: 14px;
        }

        .contact-info a {
            text-decoration: none;
            color: black;
        }

        .contact-info a:hover {
            color: #B19CD9;
        }
    </style>

<!-- jQuery for handling feedback form submission -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert('Thank you for the feedback');
                        $('form')[0].reset();
                    } else {
                        alert(result.message);
                    }
                }
            });
        });
    });
  </script>
  </head>

<body style="background-color: #2F2C2F">
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
        // Cart count based on session
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

<!-- Feedback form -->
<div class ="w3-row" style="padding:100px 0.5px">
<div class="feedback-container">
<div class="feedback-content">
    <h3>Give us some feedback</h3>
    <form action="" method="post">
      <textarea name="feedback" placeholder="Enter your comment here..."></textarea>
      <button type="submit">Submit</button>
    </form>
    <br>
    <div class="contact-info">
        <p><strong>Contact Us</strong></p>
        <p><a href="https://www.google.com/search?gs_ssp=eJzj4tVP1zc0TCszNS5KqTI2YLRSNagwNjBJTDFMMUlMS0kzMU41tDKoSDRPtjRITDEyMzY2t0g0SPWSTMxWyEgsLUlVKEgsLU5USE5MS0sFkakAvTUYkw&q=ak+haute+pausa+caffe+cafe&oq=ak+hau&gs_lcrp=EgZjaHJvbWUqEggBEC4YJxivARjHARiABBiKBTIGCAAQRRg5MhIIARAuGCcYrwEYxwEYgAQYigUyBwgCEAAYgAQyBwgDEAAYgAQyBggEEEUYPTIGCAUQRRg9MgYIBhBFGD0yBggHEEUYQdIBCDkyMzBqMGo3qAIAsAIA&sourceid=chrome&ie=UTF-8#" target="_blank">Phone: 013-470 5188</a></p>
        <p><a href="https://maps.app.goo.gl/z7Le7WRV4KCXKM7q6" target="_blank">Location: 93, Jalan Dagangan 4, Pusat Bandar Bertam Perdana, 13200 Kepala Batas, Pulau Pinang</a></p>
    </div>
  </div>
  <img src="uploads/front.jpg" alt="Front Image">
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