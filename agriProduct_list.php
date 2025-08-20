<?php
// Start the session
session_start();

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if a search is being performed
if (isset($_GET['search_id']) && $_GET['search_id'] !== '') {
    $search_id = mysqli_real_escape_string($db, $_GET['search_id']);
    $query = "SELECT * FROM agri_product WHERE product_id = '$search_id'";
} else {
    $query = "SELECT * FROM agri_product";
}

$result = mysqli_query($db, $query);

// Check if there are products in the table
if (mysqli_num_rows($result) > 0) {
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $products = [];
}

mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Products List</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      padding: 8px 12px;
      border: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    .btn {
      padding: 8px 16px;
      background-color: #28a745;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }

    .btn-back {
      background-color: #007bff;
    }

    .search-form {
      margin-bottom: 20px;
    }

    .search-form input {
      padding: 8px;
      margin-right: 10px;
    }

    .no-result {
      color: red;
      font-weight: bold;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div>
    <h1>Products List</h1>

    <!-- Search Form -->
    <form method="GET" action="" class="search-form">
      <label for="search_id">Search by Product ID:</label>
      <input type="text" id="search_id" name="search_id" placeholder="Enter Product ID" required>
      <button type="submit" class="btn">Search</button>
     
    </form>

    <?php if (count($products) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Product ID</th>
          <th>Name</th>
          <th>Seasonality</th>
          <th>Type</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $product): ?>
          <tr>
            <td><?php echo htmlspecialchars($product['product_id']); ?></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><?php echo htmlspecialchars($product['seasonality']); ?></td>
            <td><?php echo htmlspecialchars($product['type']); ?></td>
            <td>
              <a href="edit_product.php?product_id=<?php echo urlencode($product['product_id']); ?>"><button class="btn">Edit</button></a>
              <a href="delete_product.php?product_id=<?php echo urlencode($product['product_id']); ?>"><button class="btn">Delete</button></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <div class="no-result">No products found.</div>
    <?php endif; ?>

    <br>
    <a href="add_AgriProduct_form.php"><button class="btn">Add New Product</button></a>
    <a href="admin_dashboard.php"><button class="btn btn-back">Back</button></a>
  </div>
</body>
</html>
