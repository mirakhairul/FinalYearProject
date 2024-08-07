<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Connect to the database (replace dbname, username, password, and host with your actual database details) 
$pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', ''); 
 
// Check for any errors during connection 
if (!$pdo) { 
    die("Connection failed: " . $pdo->errorInfo()); 
} 
 
if (isset($_POST['add_customer'])) { 
    // Fetch values from form inputs  
    $customer_name = $_POST['customer_name'];
    $customer_gender = $_POST['customer_gender'];
    $customer_phoneno = $_POST['customer_phoneno'];
    $customer_email = $_POST['customer_email'];
    $customer_dob = $_POST["customer_dob"];
    
    // Prepare SQL statement to insert new user into the database 
    $stmt = $pdo->prepare("INSERT INTO cafe_customer (customer_name, customer_gender, customer_phoneno, customer_email, customer_dob) VALUES (?, ?, ?, ?, ?)"); 
    $stmt->execute([$customer_name, $customer_gender, $customer_phoneno, $customer_email, $customer_dob]); 
 
    // Redirect back to the customerPage.php page after adding the user 
    header("Location: customerPageStaff.php"); 
    exit(); 
} 
?>

<span style="font-family: verdana, geneva, sans-serif;">
    <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8" />
          <title>ADD CUSTOMER</title>
          <link rel="stylesheet" href="stylef.css" />
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
                display: inline-block; /* Make the button inline */
                text-align: center; /* Ensure text alignment is centered */
                width: 150px; /* Set a fixed width for both buttons */
                box-sizing: border-box; /* Include padding and border in the element's total width and height */
            }
            
            .btn-submit { 
                background-color: green; 
                color: #fff; 
            } 
      
            .btn-close-popup { 
                margin-top: -10px; 
                background-color: #e74c3c; 
                color: #fff; 
                text-decoration: none; /* Remove default underline for anchor tag */
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
                   <h2>Add New Customer</h2>
                </section>
           
                <div class="tableStaff">
                    <form class="form-container" method="post" onsubmit="return validateForm()"> 
                        <label class="form-label" for="name">Customer Name:</label> 
                        <input class="form-input" type="text" id="customer_name" name="customer_name" required> 

                        <label class="form-label" for="gender">Customer Gender.:</label> 
                        <input class="form-input" type="text" id="customer_gender" name="customer_gender" required> 

                        <label class="form-label" for="phoneno">Customer Phone No.:</label> 
                        <input class="form-input" type="text" id="customer_phoneno" name="customer_phoneno" required> 

                        <label class="form-label" for="email">Customer Email:</label> 
                        <input class="form-input" type="email" id="customer_email" name="customer_email" required> 

                        <label class="form-label" for="dob">Customer D.O.B:</label> 
                        <input class="form-input" type="date" id="customer_dob" name="customer_dob" required> 

                        <center><button class="btn-submit" type="submit" name="add_customer" onclick="togglePopup()"> Submit </button>
                        <a href="customerPageStaff.php" class="btn-close-popup">Close</a></center>
                    </form> 
                </div>
                </div> 

                <script>
                  function validateForm() {
                      // Get the values of form fields
                      var name = document.getElementById("customer_name").value;
                      var gender = document.getElementById("customer_gender").value;
                      var phone = document.getElementById("customer_phoneno").value;
                      var email = document.getElementById("customer_email").value;
                      var dob = document.getElementById("customer_dob").value;

                      // Check if any of the fields are empty
                      if (name.trim() == '' || gender.trim() == '' || phone.trim() == '' || email.trim() == '' || dob.trim() == '') {
                          alert("Please fill in all fields.");
                          return false; // Prevent form submission
                      }

                      // If all fields are filled, allow form submission
                      return true;
                  }

                  function closeForm() {
                      // Redirect back to staffPage.php when the Close button is clicked
                      window.location.href = "customerPageStaff.php";
                  }
              </script>
        </body>
    </html>
</span>