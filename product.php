<?php
// Display errors for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session
session_start();

// Connect to the database
$con = mysqli_connect("localhost", "root", "", "akcafe");
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
}

// Initialize status message
$status = "";

// Handle form submission to add product to cart
if (isset($_POST['prod_code']) && $_POST['prod_code'] != "") {
    $code = $_POST['prod_code'];
    $result = mysqli_query($con, "SELECT * FROM cafe_product WHERE prod_code='$code'");
    $row = mysqli_fetch_assoc($result);

    // Extract product details
    $name = $row['prod_name'];
    $desc = $row['prod_desc'];
    $code = $row['prod_code'];
    $price = $row['prod_price'];
    $image = $row['img'];
    $stock = $row['stock'];
    $category = strtolower($row['category_name']);

    // Get additional product preferences
    $hotcold = isset($_POST['hotcold']) ? $_POST['hotcold'] : ' ';
    $ice_level = isset($_POST['ice_level']) ? $_POST['ice_level'] : ' ';
    $special_instructions = isset($_POST['special_instructions']) ? $_POST['special_instructions'] : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Combine all special instructions
    $combined_instructions = trim("$hotcold\n$ice_level\n$special_instructions");

    // Prepare cart array
    $cartArray = array(
        $code => array(
            'prod_name' => $name,
            'prod_desc' => $desc,
            'prod_code' => $code,
            'prod_price' => $price,
            'quantity' => $quantity,
            'img' => $image,
            'special_instructions' => $combined_instructions
        )
    );

    // Check if stock is sufficient or if product is a non-stock category
    if ($stock >= $quantity || in_array($category, ['noncoffee', 'coffee', 'signature', 'frappe', 'sparkling', 'cheese foam', 'matcha', 'non coffee'])) {
        if ($stock >= $quantity) {
            // Update the stock in the database
            $new_stock = $stock - $quantity;
            $update_query = "UPDATE cafe_product SET stock='$new_stock' WHERE prod_code='$code'";
            mysqli_query($con, $update_query);
        }

        // Add to cart
        if (empty($_SESSION["cart"])) {
            $_SESSION["cart"] = $cartArray;
            $status = "<div class='box'>Product is added to your cart!</div>";
        } else {
            $array_keys = array_keys($_SESSION["cart"]);
            if (in_array($code, $array_keys)) {
                $_SESSION["cart"][$code]['quantity'] += $quantity;
                $status = "<div class='box' style='color:red;'>Product quantity updated in your cart!</div>";
            } else {
                $_SESSION["cart"] = array_merge($_SESSION["cart"], $cartArray);
                $status = "<div class='box'>Product is added to your cart!</div>";
            }
        }
    } else {
        $status = "<div class='box' style='color:red;'>Low stock! Cannot add to cart.</div>";
    }
}

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

// Get the customer's email if logged in, otherwise set to 'None'
$printUsername = isset($_SESSION['customer_email']) ? $_SESSION['customer_email'] : 'None';
$loggedIn = isset($_SESSION['customer_email']);

// Get the category name from URL parameters
$category_name = isset($_GET['category_name']) ? $_GET['category_name'] : '';

// Determine container class based on category
$containerClass = '';
if ($category_name == 'Drinks') {
    $containerClass = 'drinks-container';
}

// Count the number of items in the cart
$cart_count = isset($_SESSION["cart"]) ? count(array_keys($_SESSION["cart"])) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>AK HAUTE PAUSA</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800;900&family=Poppins:wght@400;500;600;700;800;900&display=swap">
    <style>
        .product_wrapper {
            flex-shrink: 0;
            margin: 5px;
            padding: 8px;
            text-align: center;
            background-color: #D9D9D9;
            border-radius: 5px;
            box-sizing: border-box;
            height: auto;
            max-width: 180px;
            width: 100%;
            display: flex;
            flex-direction: column;
            font-weight: bold;
            overflow: hidden;
            flex: 0 0 calc(20% - 10px);
        }

        .product_content {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product_wrapper .image img {
            max-width: 100%;
            max-height: 150px;
            height: 120px;
            width: 150px;
            object-fit: cover;
        }

        .product_wrapper .details {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 120px; /* Set a fixed height to align items */
        }

        .details {
            flex-grow: 1;
        }

        .name {
          font-size: 14px;
          color: black;
          text-align: center;
          padding: 5px;
          margin-bottom: 2px;
          overflow: hidden;
          white-space: normal; /* Allow text to wrap */
          text-overflow: unset; /* Remove ellipsis */
        }

        .price {
            font-size: 14px;
            color: black;
            text-align: center;
            padding: 5px;
            margin-bottom: 2px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .desc {
            font-size: 11px;
            font-weight: normal;
            color: black;
            text-align: center;
            padding: 5px;
            overflow: auto; /* Allow scrolling */
            max-height: 50px; /* Adjust height as needed */
        }

        .desc::-webkit-scrollbar {
            width: 0px;  /* Remove scrollbar space */
            background: transparent;  /* Optional: just make scrollbar invisible */
        }

        .buy {
            text-transform: uppercase;
            background: black;
            cursor: pointer;
            color: #fff;
            padding: 8px 10px;
            font-size: 12px;
            border: none;
            border-radius: 3px;
        }

        .button_container {
            margin-top: auto;
            text-align: center;
        }

        .buy:hover {
            background: black;
        }

        .prod-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 10px;
            width: 100%;
            max-width: calc(100% - 20px);
            margin-left: 80px;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .prod-container::-webkit-scrollbar {
            display: none;
        }

        body::-webkit-scrollbar {
            width: 0px;
        }

        .category-title {
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            color: white;
            font-size: 18px;
            margin-top: 40px;
            margin-bottom: 20px;
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

        .horizontal-nav {
            display: flex;
            justify-content: center;
            margin-bottom: 0;
            background-color: black;
            margin-top: -40px;
            position: fixed;
            top: 70;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .horizontal-nav .wrapper {
            display: flex;
            justify-content: center;
            width: 100%;
        }

        .category-tab {
            padding: 10px 25px;
            color: white;
            text-decoration: none;
            margin: 0 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            position: relative;
        }

        .w3-dropdown-content {
            position: absolute;
            top: 45px;
            right: -5px;
            background-color: white;
            z-index: 1200;
        }

        .category-tab:hover {
            background-color: #333;
        }

        .category-tab::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: white;
            bottom: 0;
            left: 0;
            border-radius: 5px;
        }

        .category-container {
            position: fixed;
            width: 100%;
            padding: 10px 0;
            background-color: black;
            z-index: 1001;
        }

        .active-tab {
            background-color: transparent;
        }

        .overlay {
            background-color: white;
            position: relative;
            z-index: 998;
        }

        .overlay::before,
        .overlay::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 1px;
            background-color: black;
            top: 50%;
        }

        .overlay::before {
            left: 0;
        }

        .overlay::after {
            right: 0;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            background-color: rgba(0, 0, 0, 0.4);
            left: 50;
            top: 18%;
            width: auto;
            height: auto;
        }

        .modal-content {
            margin: 80 auto;
            padding: 10px;
            padding-left: 20px;
            border: 1px solid #888;
            width: 30%;
            max-width: 600px;
            border-radius: 8px;
            position: fixed;
            background-color: white;
        }

        .close {
            color: black;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: rgb(207, 159, 255);
            text-decoration: none;
            cursor: pointer;
        }

        .modal-image {
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-image img {
            width: auto;
            max-width: 300px;
            display: inline-block;
        }

        .form-group {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .add-to-cart {
            background-color: black;
            color: white;
            padding: 6px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 85%;
        }

        .add-to-cart:hover {
            background-color: #333;
        }

        .quantity-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
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

        #quantityValue {
            display: inline-block;
            width: 10px;
            text-align: center;
            font-size: 12px;
            margin: 0 10px;
        }
    </style>
</head>

<body style="background-color: #644F61">
<header>
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
                        <a href="login_user.html" class="w3-bar-item w3-button w3-medium">Login</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <div class="overlay"></div>
        <div class="w3-row" style="padding-top:100px; padding-bottom: 20px;">
        <div class="horizontal-nav">
                <div class="wrapper">
                    <?php
                    // Get categories from the database
                    $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');
                    $stmt = $pdo->query('SELECT * FROM cafe_category');
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $category = $row['category_name'];
                        $activeClass = ($category == $category_name) ? 'active-tab' : '';
                        echo "<a class='category-tab $activeClass' href='product.php?category_name=" . urlencode($row['category_name']) . "'>" . $row['category_name'] . "</a>";
                    }
                    // Get products based on the selected category
                    if (empty($category_name)) {
                        $stmt = $pdo->query('SELECT * FROM cafe_product WHERE availability = "Available"');
                    } else {
                        $stmt = $pdo->prepare('SELECT * FROM cafe_product WHERE category_name = ? AND availability = "Available"');
                        $stmt->execute([$category_name]);
                    }
                    ?>
                </div>
            </div>
    </div>
</header>
<div class="w3-row" style="padding:40px; padding-top: 170px;">
<?php
    // Fetch products based on the selected category
    $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');
    if (!empty($category_name)) {
        if ($category_name == 'Drinks') {
            $stmt = $pdo->query("SELECT * FROM cafe_product WHERE category_name IN ('Non Coffee', 'coffee', 'signature', 'frappe', 'sparkling', 'cheese foam', 'matcha') AND availability = 'Available'");
        } else {
            $stmt = $pdo->prepare('SELECT * FROM cafe_product WHERE category_name = ? AND availability = "Available"');
            $stmt->execute([$category_name]);
        }
    } else {
        $stmt = $pdo->query('SELECT * FROM cafe_product WHERE availability = "Available"');
    }
    echo "<div class='prod-container'>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<div class='product_wrapper'>";
        echo "<div class='product_content'>";
        echo "<div class='image'><img src='admin/" . $row['img'] . "' alt='Product Image'/></div>";
        echo "<div class='details'>";
        echo "<div class='name'>" . $row['prod_name'] . "</div>";
        echo "<div class='desc'>" . $row['prod_desc'] . "</div>";
        echo "<div class='price'>RM" . number_format($row['prod_price'], 2) . "</div>";
        if (strtolower($row['category_name']) == 'dessert' || strtolower($row['category_name']) == 'pattisseries') {
            echo "<div class='stock' style='display:none'>Stock: " . $row['stock'] . "</div>";
        }
        echo "</div>";
        echo "</div>";
        echo "<div class='button_container'>";
        if ((strtolower($row['category_name']) == 'dessert' || strtolower($row['category_name']) == 'pattisseries') && $row['stock'] <= 0) {
            echo "<button class='buy' type='button' disabled>Out of stock</button>";
        } else {
            echo "<button class='buy' type='button' data-category='" . $row['category_name'] . "' data-stock='" . $row['stock'] . "'>Add to cart</button>";
        }
        echo "<input type='hidden' name='prod_code' value='" . $row['prod_code'] . "' />";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";

?>
</div>
<div style="clear:both;"></div>

<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-image">
            <img id="modalProductImage" src="" alt="Product Image">
        </div>
        <h4 style="font-weight: bold; font-size:18px;" id="modalProductName"></h4>
        <p style="font-size:14px;" id="modalProductDesc"></p>
        <p style="font-size:14px;" id="modalProductPrice"></p>
        <p style="font-size:14px;" id="modalProductStock"></p>
        <form id="modalForm" method="post" action="">
            <input type="hidden" id="modalProdCode" name="prod_code" value="">
            <input type="hidden" id="modalQuantity" name="quantity" value="1">
            <div id="drinkPreferences" style="display: none;">
                <div class="form-group">
                    <label for="hotcold">Choose:</label><br>
                    <input type="radio" id="hot" name="hotcold" value="Hot">
                    <label for="hot">Hot</label>
                    <input type="radio" id="cold" name="hotcold" value="Cold">
                    <label for="cold">Cold</label><br>
                </div>
                <div class="form-group">
                    <label style="text-decoration:underline;" for="iceLevel">Ice Level</label><br>
                    <input type="radio" id="normalIce" name="ice_level" value="Normal Ice">
                    <label for="normalIce">Normal Ice</label><br>
                    <input type="radio" id="halfIce" name="ice_level" value="Half Ice">
                    <label for="halfIce">Half Ice</label><br>
                    <input type="radio" id="noIce" name="ice_level" value="No Ice">
                    <label for="noIce">No Ice</label>
                </div>
                <div class="form-group">
                    <label style="text-decoration:underline;" for="specialInstructions">Special Instructions</label>
                    <textarea id="specialInstructions" name="special_instructions" rows="3" cols="50"></textarea>
                </div>
            </div>
            <div class="quantity-container">
                <button type="button" class="quantity-btn" id="decreaseQuantity">-</button>
                <span id="quantityValue">1</span>
                <button type="button" class="quantity-btn" id="increaseQuantity">+</button>
            </div>
            <button type="submit" class="add-to-cart">Buy</button>
        </form>
    </div>
</div>

<footer>
    <div class="footer">
        <p>Copyright © 2024 AK Haute Pausa Caffe. All rights reserved.</p>
    </div>
</footer>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("productModal");
    const closeBtn = document.getElementsByClassName("close")[0];
    const decreaseQuantityBtn = document.getElementById("decreaseQuantity");
    const increaseQuantityBtn = document.getElementById("increaseQuantity");
    const quantityValue = document.getElementById("quantityValue");
    const drinkPreferences = document.getElementById("drinkPreferences");
    const productWrappers = document.querySelectorAll(".product_wrapper");
    const stockCategories = ['dessert', 'pattisseries'];
    const drinksCategories = ['noncoffee', 'coffee', 'signature', 'frappe', 'sparkling', 'cheese foam', 'matcha', 'non coffee'];
    const modalForm = document.getElementById("modalForm");

    // Add event listeners to each product wrapper for opening the modal
    productWrappers.forEach(wrapper => {
        wrapper.addEventListener("click", function () {
            const productImage = this.querySelector(".image img").src;
            const productName = this.querySelector(".name").innerText;
            const productDesc = this.querySelector(".desc").innerText;
            const productPrice = this.querySelector(".price").innerText;
            const productCode = this.querySelector("input[name='prod_code']").value;
            const productStock = this.querySelector(".stock") ? this.querySelector(".stock").innerText.split(": ")[1] : '';
            const productCategory = this.querySelector(".buy").dataset.category.toLowerCase();

            document.getElementById("modalProductImage").src = productImage;
            document.getElementById("modalProductName").innerText = productName;
            document.getElementById("modalProductDesc").innerText = productDesc;
            document.getElementById("modalProductPrice").innerText = productPrice;
            document.getElementById("modalProdCode").value = productCode;

            if (stockCategories.includes(productCategory)) {
                document.getElementById("modalProductStock").innerText = `Stock: ${productStock}`;
                document.getElementById("modalProductStock").style.display = 'block';
            } else {
                document.getElementById("modalProductStock").innerText = '';
                document.getElementById("modalProductStock").style.display = 'none';
            }

            if (drinksCategories.includes(productCategory)) {
                drinkPreferences.style.display = 'block';
            } else {
                drinkPreferences.style.display = 'none';
            }

            modal.style.display = "block";
        });
    });

    // Close the modal
    closeBtn.onclick = function () {
        modal.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Adjust the quantity in the modal
    decreaseQuantityBtn.onclick = function () {
        let currentValue = parseInt(quantityValue.innerText);
        if (currentValue > 1) {
            quantityValue.innerText = currentValue - 1;
            document.getElementById("modalQuantity").value = currentValue - 1;
        }
    }

    increaseQuantityBtn.onclick = function () {
        let currentValue = parseInt(quantityValue.innerText);
        quantityValue.innerText = currentValue + 1;
        document.getElementById("modalQuantity").value = currentValue + 1;
    }

    // Handle form submission to add product to cart
    modalForm.onsubmit = function(event) {
        event.preventDefault();
        const formData = new FormData(modalForm);
        const prodCode = document.getElementById("modalProdCode").value;
        const quantity = parseInt(document.getElementById("modalQuantity").value);
        const productCategory = document.querySelector(`.product_wrapper input[name='prod_code'][value='${prodCode}']`).closest('.product_wrapper').querySelector('.buy').dataset.category.toLowerCase();
        let stock = Infinity;

        if (!drinksCategories.includes(productCategory)) {
            stock = parseInt(document.querySelector(`.product_wrapper input[name='prod_code'][value='${prodCode}']`).closest('.product_wrapper').querySelector('.stock').innerText.split(": ")[1]);
        }

        if (stock >= quantity) {
            fetch('product.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                modal.style.display = "none";
                setTimeout(function() {
                    location.reload(); // Reload the page to update the cart count
                }, 1000); // 1 second delay before reloading
            })
            .catch(error => console.error('Error:', error));
        } else {
            alert('Low stock! Cannot add to cart.');
        }
    }
});


document.addEventListener("DOMContentLoaded", function () {
    const categoryTabs = document.querySelectorAll(".category-tab");
    const productContainers = document.querySelectorAll(".prod-container");
    const categoryTitle = document.getElementById("category-title");

    function centerCategoryTitle() {
        const containerWidth = document.querySelector('.prod-container').offsetWidth;
        const titleWidth = categoryTitle.offsetWidth;
        categoryTitle.style.left = `${(containerWidth - titleWidth) / 2}px`;
    }

    centerCategoryTitle();

    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category_name');

    if (category) {
        const matchingContainer = document.querySelector(`#${category}`);
        if (matchingContainer) {
            productContainers.forEach(container => container.style.display = "none");
            matchingContainer.style.display = "block";
            categoryTabs.forEach(tab => tab.classList.remove("active-tab"));
            categoryTabs.forEach(tab => {
                if (tab.getAttribute("href").substring(1) === category) {
                    tab.classList.add("active-tab");
                    categoryTitle.textContent = category;
                    categoryTitle.style.display = "block";
                    centerCategoryTitle();
                }
            });
        }
    }

    categoryTabs.forEach(tab => {
        tab.addEventListener("click", function () {
            const clickedCategory = this.getAttribute("href").substring(1);

            productContainers.forEach(container => container.style.display = "none");

            const matchingContainer = document.querySelector(`#${clickedCategory}`);
            if (matchingContainer) {
                matchingContainer.style.display = "block";
            }

            categoryTabs.forEach(tab => tab.classList.remove("active-tab"));
            this.classList.add("active-tab");

            categoryTitle.textContent = clickedCategory;
            categoryTitle.style.display = "block";
            centerCategoryTitle();
        });
    });
});
</script>
</body>
</html>
