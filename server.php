<?php
session_start();

// Initialize variables
$username = "";
$email    = "";
$user_type = "";
$errors = array();

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// REGISTER USER
if (isset($_POST['reg_user'])) {
    // Receive all input values from the form
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
    $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);
    $user_type = mysqli_real_escape_string($db, $_POST['user_type']);

    // Form validation
    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($email)) { array_push($errors, "Email is required"); }
    if (empty($password_1)) { array_push($errors, "Password is required"); }
    if ($password_1 != $password_2) {
        array_push($errors, "The two passwords do not match");
    }

    // Check if the username or email already exists in the database
    $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
    $result = mysqli_query($db, $user_check_query);
    $user = mysqli_fetch_assoc($result);
    
    if ($user) { // If user exists
        if ($user['username'] === $username) {
            array_push($errors, "Username already exists");
        }
        if ($user['email'] === $email) {
            array_push($errors, "Email already exists");
        }
    }

    // Register user if there are no errors in the form
    if (count($errors) == 0) {
        $password = md5($password_1); // Encrypt the password before saving in the database
        
        // Generate user_id based on user_type
        $prefix = '';
        switch($user_type) {
            case 'Farmer': $prefix = 'FARM'; break;
            case 'Agricultural_Officer': $prefix = 'AGRI'; break;
            case 'Admin': $prefix = 'ADM'; break;
            case 'Retailer': $prefix = 'RET'; break;
            case 'Wholesaler': $prefix = 'WHOLE'; break;
            case 'Consumer': $prefix = 'CON'; break;
            case 'Warehouse_manager': $prefix = 'WARE'; break;
            default: $prefix = 'USER';
        }
        
        $user_id = $prefix . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        // Insert into the database including user type and user_id
        $query = "INSERT INTO users (user_id, username, email, password, user_type) 
                  VALUES('$user_id', '$username', '$email', '$password', '$user_type')";
        mysqli_query($db, $query);

        // Store success message
        $_SESSION['success'] = "Registration successful! You can now log in.";

        // Redirect to the login page
        header('location: login.php');
        exit();
    }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = mysqli_real_escape_string($db, $_POST['password']);
    $user_type = mysqli_real_escape_string($db, $_POST['user_type']); 

    if (empty($username)) { array_push($errors, "Username is required"); }
    if (empty($password)) { array_push($errors, "Password is required"); }

    if (count($errors) == 0) {
        $password = md5($password); // Encrypt password
        $query = "SELECT * FROM users WHERE username='$username' AND password='$password' AND user_type='$user_type'";
        $results = mysqli_query($db, $query);

        if (mysqli_num_rows($results) == 1) {
            $user = mysqli_fetch_assoc($results);
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['user_id']; // Store user_id in session
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['success'] = "You are now logged in";

            // Redirect to the appropriate dashboard based on user type
            switch($user['user_type']) {
                case 'Admin':
                    header('location: admin_dashboard.php');
                    break;
                case 'Farmer':
                    header('location: farmer_dashboard.php');
                    break;
                case 'Agricultural_Officer':
                    header('location: agriOfficer_dashboard.php');
                    break;
                case 'Retailer':
                    header('location: retailer_dashboard.php');
                    break;
                case 'Wholesaler':
                    header('location: wholesaler_dashboard.php');
                    break;
                case 'Consumer':
                    header('location: consumer_dashboard.php');
                    break;
                case 'Warehouse_manager':
                    header('location: Warehouse_manager_dashboard.php');
                    break;
                default:
                    header('location: user_dashboard.php');
            }
        } else {
            array_push($errors, "Wrong username/password combination or invalid user type");
        }
    }
}
?>