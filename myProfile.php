<?php
// Display errors for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "akcafe");

// Check connection
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

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

// Get the customer's email if logged in, otherwise set to 'None'
$printUsername = isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : 'None';
$loggedIn = isset($_SESSION['customer_email']);

// Get the current customer's username from the session
$current_customer_username = isset($_SESSION['customer_username']) ? $_SESSION['customer_username'] : 0;  // Check if user ID exists in session

// Flag to indicate if the profile was updated
$profile_updated = false;

// Handle form submission to update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $customer_phoneno = $_POST['customer_phoneno'];
    $customer_gender = $_POST['customer_gender'];
    $customer_dob = $_POST['customer_dob'];

    // Update the profile information in the database
    $update_sql = "UPDATE cafe_customer SET customer_name = ?, customer_email = ?, customer_phoneno = ?, customer_gender = ?, customer_dob = ? WHERE customer_username = ?";
    $update_stmt = mysqli_prepare($con, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "ssssss", $customer_name, $customer_email, $customer_phoneno, $customer_gender, $customer_dob, $current_customer_username);

    if (mysqli_stmt_execute($update_stmt)) {
        $profile_updated = true;
    } else {
        echo "Error updating profile: " . mysqli_error($con);
    }
}

// Fetch the current profile information from the database
$sql = "SELECT * FROM cafe_customer WHERE customer_username = ?"; // Assuming customer_username is the correct field
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "s", $current_customer_username); // Bind user username as parameter

// If connection successful, get user data
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
  
    // Extract user details from the retrieved data
    $customer_name = $row['customer_name'];
    $customer_email = $row['customer_email'];
    $customer_phoneno = $row['customer_phoneno'];
    $customer_dob = $row['customer_dob'];
    $customer_gender = $row['customer_gender'];
} else {
    echo "Error fetching profile: " . mysqli_error($con);
}


// Count the number of items in the cart
if (!empty($_SESSION['cart'])) {
    $printCount = count($_SESSION['cart']);
}
else {
    $printCount = 0;
}

// Get the cart count
$cart_count = isset($_SESSION["cart"]) ? count(array_keys($_SESSION["cart"])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MY PROFILE</title>

    <!-- for navigation bar-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

    <!-- Google Fonts Link-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap">

<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        text-decoration: none;
        font-family: 'Poppins', sans-serif;
    }

    .w3-dropdown-content {
        position: absolute;
        top: 45px;
        right: -5px;
        background-color: white;
        z-index: 1200;
    }

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

    body::-webkit-scrollbar {
        width: 0px;
        display: none;
    }  

    .wrapper{
        background: #000000;
        position: fixed;
        width: 100%;
        justify-content: space-between;
        align-items: center;
    }

    .wrapper nav{
        position: relative;
        max-width: calc(100% - 200px);
        margin: 0 auto;
        height: 70px;
        display:flex;
        justify-content: space-around;
        float: left;
        padding-left: 20px;
        align-items: center;
    }

    .end-wrapper{
        width: 100%;
    }

    .end-wrapper nav{
        position: relative;
        float: right;
        font-size: 30px;
        color: whitesmoke;
        margin: 0 auto;
        display:flex;
        height: 70px;
        align-items: center; 
        padding-right: 20px;
        gap: 10px;
    }

    nav .content .end-wrapper label,
    nav .content{
        display: flex;
        align-items: center;
    }

    nav .content .links{
        margin-left: 80px;
        display: flex;
    }

    .content .logo a{
        color: #fff;
        font-size: 30px;
        font-weight: 600;
    }

    .content .links li{
        list-style: none;
        line-height: 70px;
    }

    .content .end-wrapper label,
    .content .links li a,
    .content .links li label{
        color: #fff;
        font-size: 18px;
        font-weight: 500;
        padding: 9px 17px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .content .links li label{
        display: none;
    }

    .content .end-wrapper label,
    .content .links li a i:hover,
    .content .links li i label:hover{
        background: #323c4e;
    }

    .wrapper .menu-icon{
    color: #fff;
    font-size: 18px;
    cursor: pointer;
    line-height: 100px;
    width: 70px;
    text-align: center;
    }

    .wrapper .menu-icon{
    display: none;
    }

    .wrapper input[type="checkbox"]{
    display: none;
    }

    /* Dropdown Menu code start */
    .content .links ul{
        position: absolute;
        background: #171c24;
        top: 80px;
        z-index: -1;
        opacity: 0;
        visibility: hidden;
    }

    .content .links li i:hover > ul{
        top: 70px;
        opacity: 1;
        visibility: visible;
        transition: all 0.3s ease;
    }

    .content .links ul li a {
        display: block;
        width: 100%;
        line-height: 30px;
        border-radius: 0px!important;
    }

    .content .links ul ul {
        position: absolute;
        top: 0;
        right: calc(-100% + 8px);
    }

    .content .links ul li {
        position: relative;
    }

    .content .links ul li:hover ul{
        top: 0;
    }

    /* The sidebar menu */
    .sidenav {
        height: calc(100vh - 5rem); /* Full-height */
        width: 170px; /* Set the width of the sidebar */
        position:fixed; /* Fixed Sidebar (stay in place on scroll) */
        z-index: -20; /* Stay on top */
        top: 70px; /* Stay at the top */
        left: 0;
        background-color: #D9D9D9;
        overflow-x: hidden; /* Disable horizontal scroll */
        padding-top: 15px;
        padding-bottom: 100px;
        min-height: 100vh;
        flex-direction: column;
    }

    /* The navigation menu links */
    .sidenav b {
        text-decoration: none;
        font-size: 16px;
        color:#000000;
        display: block;
    }

    .sidenav a {
        padding: 2px 8px 6px 16px;
        text-decoration: none;
        font-size: 14px;
        color:#000000;
        display: block;
    }

    /* When you mouse over the navigation links, change their color */
    .sidenav a:hover {
        color: #f1f1f1;
    }

    .form-container {
        display:flex;
        flex-direction:column;
        align-items: center;
        width: 580px;
        margin: 50px auto;
        border: 3px solid #ddd;
        padding: 20px;
        border-radius: 5px;
        position:fixed;
        top: 50px;
        right: 10px;
        left: 20px;
        background-color: #e9e5e5;
        max-height: 655px;
        padding-top: 10px;
    }

    .open-button {
        background-color: #000000;
        color: white;
        padding: 6px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        margin-left: 460px;
    }

   /*--------------------
    Form
    ---------------------*/
    select.form-control{
        height: 35px;
    }

    .form-control {
        display:flex;
        width: 100%;
        font-size: 14px;
        line-height: 1.42857143;
        color: #71748d;
        background-color: #fff;
        background-image: none;
        border: 1px solid #d2d2e4;
        border-radius: 2px;
    }

    .form-control:focus {
        color: #71748d;
        background-color: #fff;
        border-color: #414161;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(214, 214, 255, .75);
    }

    .form-control-lg {
        padding: 8px;
    }

    /* Modal styles */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1; 
        left: 0;
        top: 0;
        width: 100%;
        height: 100%; 
        overflow: auto; 
        background-color: rgb(0,0,0); 
        background-color: rgba(0,0,0,0.4); 
        padding-top: 60px; 
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; 
        padding: 20px;
        border: 1px solid #888;
        width: 80%; 
        max-width: 300px;
        position: relative;
        text-align: center;
    }

    .close {
        color: #aaa;
        position: absolute;
        right: 10px;
        top: 10px;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
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
            <?php if($loggedIn): ?>
            <div class="w3-dropdown-content w3-bar-block w3-border">
            <a href="myProfile.php" class="w3-bar-item w3-button w3-medium">My Profile</a>
            <a href="logout_user.php" class="w3-bar-item w3-button w3-medium">Logout</a>
            </div>
            <?php endif; ?>
            </div>
          </nav>
        </div>
        </div>
    </header>
    <!-- navigation bar end-->

    <!-- Sidebar -->
    <div class="sidenav">
        <div class="dropdown">
          <div class="dropdown-content">
            <a href="myProfile.php">My Profile</a>
          </div>
        </div>
    </div>

     <!-- Profile form container -->
    <div class="form-container">
        <h4 class="form-title">My Profile </h4>
        <p>Customer information details</p><br>
        <form action="myProfile.php" method="post">
            <label for="name">Full Name:
            <input class="form-control form-control-lg" type="text" name="customer_name" id="name" value="<?php  echo htmlspecialchars($customer_name);?>"></label><br>
            <label for="email">Email Address:
            <input class="form-control form-control-lg" type="email" name="customer_email" id="email" value="<?php echo htmlspecialchars($customer_email);?>"></label><br>
            <label for="phoneno">Phone Number:
            <input class="form-control form-control-lg" type="text" name="customer_phoneno" id="phoneno" value="<?php  echo htmlspecialchars($customer_phoneno);?>"></label><br>
            <label for="gender">Gender:
            <select class="form-control" id="gender" name="customer_gender">
                <option value="female" <?php if ($customer_gender == 'female') echo 'selected'; ?>>Female</option>
                <option value="male" <?php if ($customer_gender == 'male') echo 'selected'; ?>>Male</option>
            </select></label><br>
            <label for="customer_dob">Date of Birth:
            <input class="form-control form-control-lg" type="date" name="customer_dob" id="dob" value="<?php  echo htmlspecialchars($customer_dob);?>"></label><br>
            <button class="open-button" type="submit">Update</button>
        </form>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Profile updated successfully!</p>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Show the modal if the profile was updated
        <?php if ($profile_updated): ?>
            modal.style.display = "block";
        <?php endif; ?>
    </script>
</body>
</html>
