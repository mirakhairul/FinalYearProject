<?php
// Display all PHP errors (useful for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();
$status = "";

// Handle login success
if (isset($_GET['login_success']) && $_GET['login_success'] == 1) {
  echo "<script>alert('Logged in!')</script>";
  echo "<script>window.location.assign('home2.php')</script>";
}

// Handle logout success
if (isset($_GET['logout_success']) && $_GET['logout_success'] == 1) {
  echo "<script>alert('Logged out!')</script>";
  echo "<script>window.location.assign('home.php')</script>";
}

// Check if the user is logged in
$printUsername = isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : 'None';
$loggedIn = isset($_SESSION['customer_email']);

// Handle actions related to the cart
if (isset($_POST['action'])) {
  // Remove a product from the cart
    if ($_POST['action'] == "remove" && !empty($_SESSION["cart"])) {
        foreach ($_SESSION["cart"] as $key => $value) {
            if (isset($_POST["prod_code"]) && $_POST["prod_code"] == $value["prod_code"]) {
                unset($_SESSION["cart"][$key]);
                $status = "<div class='box' style='color:red;'>Product is removed from your cart!</div>";
            }
        }
        // If the cart is empty, unset the session variable
        if (empty($_SESSION["cart"])) {
            unset($_SESSION["cart"]);
        }
    }

    // Change the quantity of a product in the cart
    if ($_POST['action'] == "change") {
        foreach ($_SESSION["cart"] as &$value) {
            if (isset($value['prod_code']) && $value['prod_code'] === $_POST["prod_code"]) {
                $value['quantity'] = $_POST["quantity"];
                break;
            }
        }
    }

    // Clear the entire cart
    if ($_POST['action'] == "clear") {
        unset($_SESSION["cart"]);
        $status = "<div class='box' style='color:red;'>All products have been removed from your cart!</div>";
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
  <title>AK HAUTE PAUSA</title>

  <!-- Stylesheets for navigation bar and fonts -->
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap">

  <style>
    .cart-summary-container {
      width: 55%; /* Adjust width as needed */
      margin: 0 auto; /* Center the container horizontally */
      padding: 20px;
      border-radius: 5px;
      background-color: #fff;
      position:fixed;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .cart-summary-container h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      text-align: center;
      border-bottom: 1px solid #ddd;
    }

    th {
      font-weight: bold;
    }

    .cart-summary-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
    }

    .checkout-btn-container {
      display: flex;
      justify-content: flex-end;
      margin-top: 10px;
    }

    .checkout-btn {
      background-color: #2B67D1;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .shop-btn {
      background-color: #333;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer; 
    }

    .clear-cart-btn {
      background-color: #e74c3c;
      color: #fff;
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .message_box .box {
      margin: 5px 0px;
      border: 1px solid #2b772e;
      text-align: center;
      font-weight: bold;
      color: #2b772e;
      font-size: 10px;
    }

    .table td {
      border-bottom: #F0F0F0 1px solid;
      padding: 10px;
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

    .cart .remove {
      color: #0067ab;
      cursor: pointer;
      padding: 0px;
    }

    .cart .remove:hover {
      text-decoration:underline;
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

    .quantity-container {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .quantity-btn {
      background-color: #ccc;
      border: none;
      padding: 10px;
      cursor: pointer;
      font-size: 12px;
    }

    .quantity-btn:hover {
      background-color: #aaa;
    }

    .quantity-input {
      width: 30px;
      text-align: center;
      border: none;
      font-size: 16px;
      margin: 0 10px;
    }  
  </style>
</head>

<body style="background-color: #644F61">

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

  <!-- Cart icon and profile dropdown -->
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
  <!-- End of navigation bar -->
</div>

<!-- Cart summary container -->
<div class="cart-summary-container">
  <?php
  if(isset($_SESSION["cart"])){
    $total_price = 0;
  ?>

  <h2>Your Cart Summary</h2>
  <table id="cart-items-table">
    <tbody>
      <tr>
        <td>PRODUCT</td>
        <td>NOTES</td>
        <td>QUANTITY</td>
        <td>UNIT PRICE</td>
        <td>TOTAL</td>
      </tr>

  <?php   
  // Categories that do not have special instructions
  $categories_with_no_notes = array('dessert', 'patisseries', 'smoothie');

  foreach ($_SESSION["cart"] as $product){
    // Ensure special_instructions is set
    $product["special_instructions"] = $product["special_instructions"] ?? '';
    $category = strtolower($product["category"] ?? '');

    if (in_array($category, $categories_with_no_notes)) {
        $notes = '';
    } else {
        // Split special instructions into parts
        $notes_parts = explode(". ", $product["special_instructions"], 3);
        $hotcold = $notes_parts[0] ?? '';
        $ice_level = $notes_parts[1] ?? '';
        $special_instructions = $notes_parts[2] ?? '';
        $notes = $hotcold . "<br>" . $ice_level . "<br>" . $special_instructions;
    }
  ?>

  <!-- Display each product in the cart -->
  <tr>
    <td><?php echo $product["prod_code"]; ?> <?php echo $product["prod_name"]; ?><br/>
    <form method='post' action=''>
      <input type='hidden' name='prod_code' value="<?php echo $product["prod_code"]; ?>" />
      <input type='hidden' name='action' value="remove" />
      <button type='submit' class='remove'>Remove</button>
    </form>
    </td>

    <td>
      <?php echo $notes; ?>
    </td>
      
    <td>
        <div class="quantity-container">
            <form method='post' action='' class="quantity-form">
                <input type='hidden' name='prod_code' value="<?php echo $product["prod_code"]; ?>" />
                <input type='hidden' name='action' value="change" />
                <button type="button" class="quantity-btn decrease">-</button>
                <input type="text" name="quantity" class="quantity-input" value="<?php echo $product["quantity"]; ?>" readonly />
                <button type="button" class="quantity-btn increase">+</button>
            </form>
        </div>
    </td>

    <td><?php echo "RM".number_format($product["prod_price"], 2); ?></td>
    <td><?php echo "RM".number_format($product["prod_price"]*$product["quantity"], 2); ?></td>
  </tr>

  <?php
    $total_price += ($product["prod_price"]*$product["quantity"]);
  }
  ?>
  </tbody>
  </table>
  <div class="cart-summary-total" style="margin-top: 10px; text-align: right; margin-right: 10px">
    <strong>SUBTOTAL: <?php echo "RM".number_format($total_price, 2); ?></strong>
  </div>

  <!-- Cart summary actions -->
  <div class="cart-summary-actions" style="justify-content: space-between;">
    <div>
      <a href="product.php"><button class="shop-btn">Continue shopping</button></a>
      <form method='post' action='' style="display:inline;">
        <input type='hidden' name='action' value="clear" />
        <button type='submit' class='clear-cart-btn'>Clear Cart</button>
      </form>
    </div>
  </div>
  <div class="checkout-btn-container">
    <a href="checkout.php"><button class="checkout-btn">Checkout</button></a>
  </div>

<?php
}else{
  echo "<h3>Your cart is empty!</h3>";
?>

  <!-- Actions when the cart is empty -->
  <div class="cart-summary-actions" style="justify-content: space-between;">
    <div>
      <a href="product.php"><button class="shop-btn">Continue shopping</button></a>
      <form method='post' action='' style="display:inline;">
        <input type='hidden' name='action' value="clear" />
        <button type='submit' class='clear-cart-btn'>Clear Cart</button>
      </form>
    </div>
  </div>
  <div class="checkout-btn-container">
    <a href="checkout.php"><button class="checkout-btn">Checkout</button></a>
  </div>

<?php
}
?>
</div>

<div style="clear:both;"></div>

<!-- JavaScript to handle quantity changes -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const quantityForms = document.querySelectorAll(".quantity-form");

        quantityForms.forEach(form => {
            const decreaseBtn = form.querySelector(".decrease");
            const increaseBtn = form.querySelector(".increase");
            const quantityInput = form.querySelector(".quantity-input");

            decreaseBtn.addEventListener("click", () => {
                let quantity = parseInt(quantityInput.value);
                if (quantity > 1) {
                    quantityInput.value = quantity - 1;
                    form.submit();
                }
            });

            increaseBtn.addEventListener("click", () => {
                let quantity = parseInt(quantityInput.value);
                quantityInput.value = quantity + 1;
                form.submit();
            });
        });
    });
</script>
</body>
</html>
