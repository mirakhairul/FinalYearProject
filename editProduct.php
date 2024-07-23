<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Connect to the database (replace dbname, username, password, and host with your actual database details) 
$pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', ''); 
 
// Check for any errors during connection 
if (!$pdo) { 
    die("Connection failed: " . $pdo->errorInfo()[2]); 
} 

if (isset($_POST['update_product'])) {
  $prod_id = $_POST['prod_id']; // Ensure prod_id is correctly retrieved
  $prod_code = $_POST['prod_code'];
  $prod_name = $_POST['prod_name'];
  $prod_desc = $_POST["prod_desc"];
  $prod_category = $_POST['category_name'];
  $prod_price = $_POST["prod_price"];

  // Check if a new image file is uploaded
  if ($_FILES['img']['size'] > 0) {
    // File upload configuration
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["img"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["img"]["tmp_name"]);
    if ($check !== false) {
        // Check file size
        if ($_FILES["img"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        // If everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                // Update database with new image path
                $stmt = $pdo->prepare("UPDATE cafe_product SET prod_code=?, prod_name=?, prod_desc=?, category_name=?, prod_price=?, img=? WHERE prod_id=?");
                $stmt->execute([$prod_code, $prod_name, $prod_desc, $prod_category, $prod_price, $target_file, $prod_id]);
                echo "The file ". htmlspecialchars(basename( $_FILES["img"]["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }
} else {
    // If no new image is uploaded, update other fields without changing the image path
    $stmt = $pdo->prepare("UPDATE cafe_product SET prod_code=?, prod_name=?, prod_desc=?, category_name=?, prod_price=? WHERE prod_id=?");
    $stmt->execute([$prod_code, $prod_name, $prod_desc, $prod_category, $prod_price, $prod_id]);
}

// Redirect to product page after update
header("Location: productPage.php");
exit();
}
?>

<span style="font-family: verdana, geneva, sans-serif;">
  <!DOCTYPE html>
      <html lang="en">
      <head>
        <meta charset="UTF-8" />
        <title>EDIT PRODUCT</title>
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
          .container2nd {
            margin-left: 280px; /* Adjust this based on the width of your sidebar */
            margin-top: 6px; 
            padding: 40px;
            padding-top: 2px;
            overflow: hidden; /* Hide the scrollbar for the container */
            position: fixed;
            }
            .tableStaff {
            width: 70%; /* Adjust the width as needed */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            height: auto; /* Adjust height based on the header height */
            margin-left: 30px;
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
                 <br>
                 <h2>Edit Product</h2>
              </section>


              <?php
              // Check if 'prod_id' is set in POST
              if(isset($_POST['prod_id']) && !empty($_POST['prod_id'])) {
                  $prod_id = $_POST['prod_id'];
                  
                  // Fetch product details from the database
                  $stmt = $pdo->prepare("SELECT * FROM cafe_product WHERE prod_id = ?");
                  $stmt->execute([$prod_id]);
                  $product = $stmt->fetch(PDO::FETCH_ASSOC);
                  
                  // Check if product record exists
                  if($product) {
              ?>
         
              <div class="tableStaff">
                <form class="form-container" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                      
                      <label class="form-label" for="code">Product Code:</label> 
                      <input class="form-input" type="text" id="prod_code" name="prod_code" value="<?php echo htmlspecialchars($product['prod_code']); ?>" required> 

                      <label class="form-label" for="name">Product Name:</label> 
                      <input class="form-input" type="text" id="prod_name" name="prod_name" value="<?php echo htmlspecialchars($product['prod_name']); ?>" required> 

                      <label class="form-label" for="desc">Product Description:</label> 
                      <input class="form-input" type="text" id="prod_desc" name="prod_desc" value="<?php echo htmlspecialchars($product['prod_desc']); ?>" required> 

                      <label class="form-label" for="category_name">Product Category:</label>
                      <select class="form-input" id="category_name" name="category_name" required>
                          <option value="">Select Category</option>
                          <?php
                          $stmt = $pdo->query('SELECT category_name FROM cafe_category');
                          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                              $selected = $product['category_name'] == $row['category_name'] ? 'selected' : '';
                              echo "<option value='" . $row['category_name'] . "' $selected>" . $row['category_name'] . "</option>";
                          }
                          ?>
                      </select>

                      <label class="form-label" for="price">Product Price:</label> 
                      <input class="form-input" type="double" id="prod_price" name="prod_price" value="<?php echo htmlspecialchars($product['prod_price']); ?>" required> 

                      <label class="form-label" for="img">Product Image:</label>
                      <input class="form-input" type="file" id="img" name="img">
                      <input type="hidden" name="prod_id" value="<?php echo $prod_id; ?>"> 

                      <center><button class="btn-submit" type="submit" name="update_product" onclick="togglePopup()"> Update </button>
                      <a href="productPage.php" class="btn-close-popup">Close</a></center>
                  </form> 
              </div>

              <?php
                    } else {
                        // Display a message indicating product not found
                        echo "Product not found!";
                    }
                } else {
                    // Display a message indicating that 'prod_id' is missing
                    echo "Product ID is missing!";
                }
              ?>
              </div> 


<script>
        // JavaScript form validation
        function validateForm() {
            // Check if all fields are filled
            const prodCode = document.getElementById('prod_code').value;
            const prodName = document.getElementById('prod_name').value;
            const categoryName = document.getElementById('category_name').value;
            const prodPrice = document.getElementById('prod_price').value;

            if (!prodCode || !prodName || !categoryName || !prodPrice) {
                alert('Please fill out all required fields.');
                return false;
            }

            // Check if price is a valid number
            if (isNaN(prodPrice) || parseFloat(prodPrice) <= 0) {
                alert('Please enter a valid price.');
                return false;
            }

            return true; // Form is valid
        }
</script>

      </body>
    </html>
  </span>