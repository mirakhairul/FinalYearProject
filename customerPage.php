<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
?>

<span style="font-family: verdana, geneva, sans-serif;">
    <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8" />
          <title>DASHBOARD ADMIN</title>
          <link rel="stylesheet" href="stylef.css" />
          <!-- Font Awesome Cdn Link -->
          <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>
          <style>
            .container2nd {
                margin-left: 300px; /* Adjust this based on the width of your sidebar */
                margin-top: 30px;
                padding: 20px;
                height: 80vh;
                overflow: hidden; /* Hide the scrollbar for the container */
                position: fixed;
            }

            .tableStaff {
            width: 70%; /* Adjust the width as needed */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: auto; /* Adjust height based on the header height */
            overflow-y: scroll;
            margin-left: auto;
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
                  <li><a href="admin.php">
                    <span class="nav-item">Dashboard</span>
                  </a></li>
                  <li><a href="staffPage.php">
                      <span class="nav-item">Staffs</span>
                  </a></li>
                  <li><a href="customerPage.php">
                    <span class="nav-item">Reg. Customers</span>
                  </a></li>
                  <li><a href="categoryPage.php">
                    <span class="nav-item">Product Category</span>
                  </a></li>
                  <li><a href="productPage.php">
                    <span class="nav-item">Products</span>
                  </a></li>
                  <li><a href="stock.php">
                    <span class="nav-item">Stock</span>
                  </a></li>
                  <li><a href="orderPage.php">
                    <span class="nav-item">Orders</span>
                  </a></li>
                  <li><a href="feedbackReviews.php">
                      <span class="nav-item">Customer Feedback</span>
                  </a></li>
                  <li><a href="salesReport.php">
                    <span class="nav-item">Sales Report</span>
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
                <h1 style="margin-right:10px; margin-left:-30px; margin-top:-40px;">Customers</h1> <p style="margin-right:1200px; margin-top:-40px;">admin</p>
            </div>
            </section>

            <div class="tableStaff">
              <!-- Trigger the modal with a button -->
              <strong><a class="btn btn-secondary" style="color: rgb(89, 112, 227); box-sizing: 20px" href="addCustomer.php" role="button">+ Add Customer</a></strong>
                <h3>Customer list</h3>
                <table class="table1">
                  <thead>
                    <tr>
                      <th class="text-center">Name</th>
                      <th class="text-center">Gender</th>
                      <th class="text-center">Phone No.</th>
                      <th class="text-center">Email</th>
                      <th class="text-center">D.O.B</th>
                      <th class="text-center" colspan="2">Action</th>
                    </tr>
                  </thead>
                  <tr>

                    <?php
                    // Connect to the database (replace dbname, username, password, and host with your actual database details)
                    $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');
        
                    // Check for any errors during connection
                    if (!$pdo) {
                        die("Connection failed: " . $pdo->errorInfo());
                    }
        
                    // Prepare and execute SQL query to select all records from a table (replace tablename with your actual table name)
                    $stmt = $pdo->query('SELECT * FROM cafe_customer');
                    
                    // Loop through each row in the result set and display the data in table rows
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>{$row['customer_name']}</td>";
                        echo "<td>{$row['customer_gender']}</td>";
                        echo "<td>{$row['customer_phoneno']}</td>";
                        echo "<td>{$row['customer_email']}</td>";
                        echo "<td>{$row['customer_dob']}</td>";
                        // Add more table data for other columns if needed

                        // Add action buttons in the last column
                        echo "<td>";
                        echo "<div style='text-align: center;'>"; // Center the button
                        echo "<form action='editCustomer.php' method='post'>";
                        echo "<input type='hidden' name='customer_name' value='{$row['customer_name']}'>"; // Place the hidden input field here
                        echo "<button type='submit' class='btn btn-primary' style='height:30px; background-color:rgb(71, 182, 71); padding: 4px; border-radius: 2px; color: white; margin-bottom:5px;'>Edit</button>";
                        echo "</form>";
                        echo "</div>"; // Close the div
                        echo "</td>";

                     }
                    ?>
                </table>
            </div>
        </div>
        </body>
      </html>
    </span>