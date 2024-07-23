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

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<style>
    header{
        margin-top: -820px;
    }

    html,
    body {
        height: 100%;
    }

    body {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    /*--------------------
    Form
    ---------------------*/

    label {
        display: inline-block;
        max-width: 100%;
        margin-bottom: 5px;
        font-size: 15px;
        color: #71748d;
    }

    .form-control {
        display: block;
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
        border-color: #a7a7f0;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(214, 214, 255, .75);
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-control-lg {
        padding: 12px;
    }

    /*-----------------------
    Splash Container / Wrapper
    -------------------------*/

    .splash-container {
        width: 100%;
        max-width: 375px;
        padding: 15px;
        margin: auto;
    }

    .splash-container .card-header {
        padding: 20px;
    }

    .splash-container .card-footer-item {
    padding: 12px 28px;
    }
    
    .text-primary {
    color: #000000 !important;
    }
    .footer-link{
        color: #2d60c5;
        font-size: 10px;
    }
    @media screen and (max-width: 900px){
        header{
        margin-top: -700px;
    }
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
        margin-top: 10px;
        margin-left: 5px;
    }

    input[type="checkbox"]:checked {
        background-color: #c8a2c8; /* Lilac purple color */
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
            <div class="logo" style="color: white;"><a>AK Haute Pausa</a>
            </div>
            </div>
          </nav>
        </div>
    </header>

    <!-- ============================================================== -->
    <!-- login page  -->
    <!-- ============================================================== -->
    <div class="splash-container">
        <div class="card ">
            <div class="card-header text-center"><a href="#"><h2 class="text-primary">AK HAUTE PAUSA</h2></a><span class="splash-description"> Welcome Back! <br> Fill your login details.</span></div>
            <div class="card-body">
                <?php
                session_start();
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>
                <form id="form" data-parsley-validate="" method="post" action="login_user.php">
                    <div class="form-group">
                        <input class="form-control form-control-lg" type="text" name="customer_username" data-parsley-trigger="change" required="" placeholder="Username" autocomplete="off">
                    </div>
                    <div class="form-group" style ="font-size: 12px;">
                        <input class="form-control form-control-lg" id="pass1" type="password" required="" placeholder="Password" name="customer_password">
                        <input type="checkbox" onclick="togglePassword()"> Show Password
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
                </form>
            </div>
            <div class="card-footer bg-white p-0 ">
                <div class="card-footer-item card-footer-item-bordered">
                    <h3><a href="register.html" class="footer-link">Create An Account</a></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.bundle.js"></script>
    <script src="js/parsley.js"></script>
    <script src="js/jquery.slimscroll.js"></script>
    <script src="js/main-js.js"></script>
    <script>
        // Initialize Parsley for form validation
        $('#form').parsley();
    
        // Function to toggle password visibility
        function togglePassword() {
            var passwordField = document.getElementById("pass1");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }

        // Redirect to home.php when the back button is clicked
        window.addEventListener('popstate', function(event) {
            window.location.href = 'home.php';
        });
    </script>
    
</body>
</html>
