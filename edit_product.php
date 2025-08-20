<?php
session_start();

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch product details by product_id
if (isset($_GET['product_id'])) {
    $product_id = mysqli_real_escape_string($db, $_GET['product_id']);
    $query = "SELECT * FROM agri_product WHERE product_id='$product_id'";
    $result = mysqli_query($db, $query);
    $product = mysqli_fetch_assoc($result);
} else {
    die("Product ID is required.");
}

// Handle form submission to update the product
if (isset($_POST['update_product'])) {
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $seasonality = mysqli_real_escape_string($db, $_POST['seasonality']);
    $type = mysqli_real_escape_string($db, $_POST['type']);

    $update_query = "UPDATE agri_product SET name='$name', seasonality='$seasonality', type='$type' WHERE product_id='$product_id'";
    if (mysqli_query($db, $update_query)) {
        // Redirect to the product list page on successful update
        echo "<script>window.location.href='agriProduct_list.php';</script>";
    } else {
        echo "Error: " . mysqli_error($db);
    }
}

mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Product</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background-color: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
    }

    h1 {
      margin-bottom: 20px;
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input {
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 4px;
      border: 1px solid #ccc;
    }

    button {
      padding: 10px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      margin-top: 10px;
    }

    button.back {
      background-color: #007bff;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Edit Product</h1>
    <form method="POST">
      <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required />
      <input type="text" name="seasonality" value="<?php echo htmlspecialchars($product['seasonality']); ?>" required />
      <input type="text" name="type" value="<?php echo htmlspecialchars($product['type']); ?>" required />
      <button type="submit" name="update_product">Update Product</button>
      <a href="agriProduct_list.php"><button class="back" type="button">Back</button></a>
    </form>
  </div>
</body>
</html>
