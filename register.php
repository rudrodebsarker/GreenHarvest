<?php
include('server.php'); 
?>

<!DOCTYPE html>
<html>
<head>
  <title>Registration system PHP and MySQL</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-size: 120%;
      background: #b4dfef;
      background-image: url('images/registerbg.avif');
    }

    .header {
      width: 30%;
      margin: 50px auto 0px;
      color: white;
      background: #5F9EA0;
      text-align: center;
      border: 1px solid #B0C4DE;
      border-bottom: none;
      border-radius: 10px 10px 0px 0px;
      padding: 20px;
    }

    form, .content {
      width: 30%;
      margin: 0px auto;
      padding: 20px;
      border: 1px solid #B0C4DE;
      background: white;
      border-radius: 0px 0px 10px 10px;
    }

    .input-group {
      margin: 10px 0px 10px 0px;
    }

    .input-group label {
      display: block;
      text-align: left;
      margin: 3px;
    }

    .input-group input, .input-group select {
      height: 30px;
      width: 93%;
      padding: 5px 10px;
      font-size: 16px;
      border-radius: 5px;
      border: 1px solid gray;
      margin-bottom: 5px;
    }

    .btn {
      padding: 10px;
      font-size: 15px;
      color: white;
      background: #5F9EA0;
      border: none;
      border-radius: 5px;
    }

    .error {
      width: 92%;
      margin: 0px auto;
      padding: 10px;
      border: 1px solid #a94442;
      color: #a94442;
      background: #f2dede;
      border-radius: 5px;
      text-align: left;
    }

    .success {
      color: #3c763d;
      background: #dff0d8;
      border: 1px solid #3c763d;
      margin-bottom: 20px;
    }

    small {
      color: #666;
      font-size: 12px;
    }
  </style>
  <script>
    // Display success message as a pop-up when registration is successful
    <?php if (isset($_SESSION['success'])): ?>
        window.onload = function() {
            alert("<?php echo $_SESSION['success']; ?>"); // Show the success message
            window.location.href = 'login.php'; // Redirect to the login page after the alert
        };
        <?php unset($_SESSION['success']); ?> // Clear the success message from the session
    <?php endif; ?>

    // Display error message if any
    <?php if (isset($_SESSION['error'])): ?>
        window.onload = function() {
            alert("<?php echo $_SESSION['error']; ?>"); // Show the error message
        };
        <?php unset($_SESSION['error']); ?> // Clear the error message from the session
    <?php endif; ?>
  </script>
</head>
<body>
  <div class="header">
    <h2>Register</h2>
  </div>
  
  <form method="post" action="register.php">
    <?php include('errors.php'); ?>
    
    <div class="input-group">
      <label>User ID</label>
      <input type="text" name="user_id" required>
      <small>Format: RET123 (Retailer), FARM456 (Farmer), AGRI789 (Officer), etc.</small>
    </div>
    <div class="input-group">
      <label>Username</label>
      <input type="text" name="username" required>
    </div>
    <div class="input-group">
      <label>Email</label>
      <input type="email" name="email" required>
    </div>
    <div class="input-group">
      <label>Password</label>
      <input type="password" name="password_1" required>
    </div>
    <div class="input-group">
      <label>Confirm password</label>
      <input type="password" name="password_2" required>
    </div>

    <div class="input-group">
      <label>User Type</label>
      <select name="user_type" required>
        <option value="">Select User Type</option>
        <option value="Farmer">Farmer</option>
        <option value="Agricultural_Officer">Agricultural Officer</option>
        <option value="Admin">Admin</option>
        <option value="Retailer">Retailer</option>
        <option value="Wholesaler">Wholesaler</option>
        <option value="Consumer">Consumer</option>
        <option value="Warehouse_manager">Warehouse Manager</option>
      </select>
    </div>

    <div class="input-group">
      <button type="submit" class="btn" name="reg_user">Register</button>
    </div>
    <p>
      Already a member? <a href="login.php">Sign in</a>
    </p>
  </form>
</body>
</html>