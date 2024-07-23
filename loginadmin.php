<?php
session_start();

// Sample database connection (replace this with your actual connection code)
$con = mysqli_connect("localhost", "root", "", "akcafe") or die("Cannot connect to server: " . mysqli_error($con));

// Check the connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Assuming you have received the user details from the registration form
$staff_email = isset($_POST['staff_email']) ? mysqli_real_escape_string($con, $_POST['staff_email']) : '';
$staff_password = isset($_POST['staff_password']) ? mysqli_real_escape_string($con, $_POST['staff_password']) : '';

// Check if the email and password match a staff record
$checkQuery = "SELECT * FROM cafe_staff WHERE staff_email='$staff_email' && staff_password='$staff_password'";
$checkResult = mysqli_query($con, $checkQuery);

if (mysqli_num_rows($checkResult) == 1) {
  // Login successful, user found (staff in this case)
  $_SESSION['success_message'] = "Login successful.";
  // Assuming you have a session variable to store staff information
  // set it here using data retrieved from the successful query result
  header("Location: admin.php"); // Redirect to admin page
  exit();
} else {
  // Login failed, email or password incorrect
  $_SESSION['error_message'] = '<span class="error-message">Login failed. Please try again.</span>';
}

// Close the connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AK Haute Pausa Coffee - Point of Sale System</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #DBBEBE;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background-color: #ECE2E2;
            border-radius: 5px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 350px;
            height: 150px;
            text-align: center;
        }

        .login-input {
            width: 80%;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .button {
            background-color: #9A8080;
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 10px; /* Adjust based on your layout */
        }

        .header-container {
        display:flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        font-size: 18px;
        margin-bottom: 10px;
        text-align: center;
        flex-direction: column;
        color: white;
        }

        .header-line {
            background-color: black;
            width:300%;
            align-items: center;
            margin-bottom: 20px;
        }
        /* Checkbox Styling */
        input[type="checkbox"] {
            width: 15px;
            height: 15px;
            -webkit-appearance: none;
            appearance: none;
            background-color: #fff;
            border: 2px solid #d2d2e4;
            border-radius: 2px;
            cursor: pointer;
            position: relative;
            outline: none;
            margin-left: 10px; /* Adjust based on your layout */
            margin-top: 5px; /* Adjust based on your layout */
        }

        input[type="checkbox"]:checked {
            background-color: black; 
            border: 2px solid #c8a2c8;
        }

        input[type="checkbox"]:checked::after {
            content: '\2714';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 10px;
        }

        .show-password {
            font-size: 12px;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-left: 20px; /* Adjust based on your layout */
            margin-top: -8px; /* Adjust based on your layout */
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="header-line">
                <h2>AK HAUTE PAUSA CAFFE</h2>
                <h2>Point Of Sale System</h2>
            </div>
        </div>
        <center><h3>Welcome Admin!</h3></center>
    <div class="login-container">
        <form method="post" action="admin.php" onsubmit="return formSubmit();">
            <input type="email" name="email" id="email" class="login-input" placeholder="Email" required>
            <input type="password" id="password" name="password" class="login-input" placeholder="Password" required>
            <div class="show-password">
                <input type="checkbox" class="toggle" onclick="togglePassword()"> 
                <label for="toggle">Show Password</label>
            </div>
            <button type="submit" class="button">Log In</button>
        </form>
    </div>
 </header>

 <script>
//Email address validation function    
function isValidEmail(email) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(email);
};

//Function to execute on form submit
function formSubmit(){
    let email = document.getElementById('email');
    if(email.value==''){
        alert("Please enter your email address!");
        email.focus();
        return false;
    }else if(!isValidEmail(email.value)){
        alert("Provided email address is incorrect!");
        email.focus();
        email.select();
        return false;
    }
}

function togglePassword() {
    var passwordField = document.getElementById("password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
    } else {
        passwordField.type = "password";
    }
}
</script>
</body>
</html>