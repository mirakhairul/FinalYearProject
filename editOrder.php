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

if (isset($_POST['update_order'])) {
    // Fetch values from form inputs
    $customer_name = $_POST['customer_name'];
    $customer_phoneno = $_POST['customer_phoneno'];
    $customer_email = $_POST['customer_email'];
    $payment_method = $_POST["payment_method"];
    $update_id = $_POST["update_id"];

    // Update order information in the database for customer details
    $stmt = $pdo->prepare("UPDATE cafe_order SET customer_name=?, customer_phoneno=?, customer_email=?, payment_method=? WHERE order_id=?");
    $stmt->execute([$customer_name, $customer_phoneno, $customer_email, $payment_method, $update_id]);

    // Redirect back to the orderPage.php page after updating the order
    header("Location: orderPageStaff.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>EDIT ORDER</title>
    <link rel="stylesheet" href="stylef.css"/>
    <!-- Font Awesome Cdn Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>

    <style>
        .form-container {
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: relative;
            margin-bottom: -20px;
        }

        .form-label {
            margin-bottom: 10px;
            font-size: 16px;
            color: #444;
            text-align: left;
        }

        .form-input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
        }

        .btn-submit,
        .btn-close-popup {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
            display: inline-block;
            text-align: center;
            width: 150px;
            box-sizing: border-box;
        }

        .btn-submit {
            background-color: green;
            color: #fff;
        }

        .btn-close-popup {
            margin-top: -10px;
            background-color: #e74c3c;
            color: #fff;
            text-decoration: none;
        }

        .btn-submit:hover,
        .btn-close-popup:hover {
            background-color: #000000;
            color: white;
        }

        /* Keyframes for fadeInUp animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
        <br>
        <h2>Edit Order</h2>
    </section>

    <?php
    // Check if 'update_id' is set in POST
    if (isset($_POST['update_id']) && !empty($_POST['update_id'])) {
        $update_id = $_POST['update_id'];

        // Fetch order details from the database
        $stmt = $pdo->prepare("SELECT * FROM cafe_order WHERE order_id = ?");
        $stmt->execute([$update_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if order record exists
        if ($order) {
            ?>
            <div class="tableStaff">
                <form class="form-container" method="post" onsubmit="return validateForm()">
                    <label class="form-label" for="name">Customer Name:</label>
                    <input class="form-input" type="text" id="customer_name" name="customer_name"
                           value="<?php echo htmlspecialchars($order['customer_name']); ?>" required>

                    <label class="form-label" for="phoneno">Customer Phone No:</label>
                    <input class="form-input" type="text" id="customer_phoneno" name="customer_phoneno"
                           value="<?php echo htmlspecialchars($order['customer_phoneno']); ?>" required>

                    <label class="form-label" for="email">Customer Email:</label>
                    <input class="form-input" type="email" id="customer_email" name="customer_email"
                           value="<?php echo htmlspecialchars($order['customer_email']); ?>" required>

                    <label class="form-label" for="payment_method">Payment Method:</label>
                    <select id="payment_method" name="payment_method" class="form-input" required>
                        <option value="Pay at cashier" <?php if ($order['payment_method'] == 'Pay at cashier') echo 'selected'; ?>>Pay at Cashier</option>
                        <option value="Online Transfer" <?php if ($order['payment_method'] == 'Online Transfer') echo 'selected'; ?>>Online Transfer</option>
                    </select>

                    <input type='hidden' name='update_id' value='<?php echo htmlspecialchars($order['order_id']); ?>'>

                    <center>
                        <button class="btn-submit" type="submit" name="update_order"> Update </button>
                        <a href="orderPageStaff.php" class="btn-close-popup">Close</a>
                    </center>

                </form>
            </div>
        <?php
        } else {
            // Display a message indicating order not found
            echo "Order not found!";
        }
    } else {
        // Display a message indicating that 'update_id' is missing
        echo "Order id is missing!";
    }
    ?>
</div>

<script>
    function validateForm() {
        // Get the values of form fields
        var name = document.getElementById("customer_name").value;
        var phone = document.getElementById("customer_phoneno").value;
        var email = document.getElementById("customer_email").value;
        var payment = document.getElementById("payment_method").value;

        // Check if any of the fields are empty
        if (name.trim() == '' || phone.trim() == '' || email.trim() == '' || payment.trim() == '') {
            alert("Please fill in all fields.");
            return false; // Prevent form submission
        }

        // If all fields are filled, allow form submission
        return true;
    }
</script>

</body>
</html>