<?php
include('server.php'); // Handles session_start() and DB connection

if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'Retailer') {
    $_SESSION['msg'] = "You must log in as a Retailer first";
    header('location: login.php');
    exit();
}

if (!isset($_SESSION['selected_retailer']['retailer_id'])) {
    die("Retailer not selected. Please update your profile.");
}

$retailer_id = $_SESSION['selected_retailer']['retailer_id'];
$searchTerm = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch purchases for the current retailer
$sql = "
    SELECT 
        t.product_id, 
        p.name AS product_name,
        t.unit_cost, 
        t.quantity, 
        t.date, 
        t.wholesaler_id, 
        t.retailer_id
    FROM track_agri_traders t
    JOIN agri_product p ON t.product_id = p.product_id
    WHERE t.retailer_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $retailer_id);
$stmt->execute();
$result = $stmt->get_result();

$purchases = [];
while ($row = $result->fetch_assoc()) {
    if (
        $searchTerm === '' ||
        stripos($row['product_name'], $searchTerm) !== false
    ) {
        $purchases[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Products Purchased</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8fafc;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 20px;
    }
    .container-box {
      background-color: #ffffff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      margin-top: 20px;
    }
    .btn-success {
      font-weight: 600;
    }
    .table thead {
      background-color: #e2e8f0;
    }
    h3 {
      font-weight: bold;
      color: #1f2937;
    }
  </style>
</head>

<body>

  <div class="container">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>Products Purchased</h3>
      <a href="retailer_dashboard.php" class="btn btn-success">← Back to Dashboard</a>
    </div>

    <!-- Search Section -->
    <form method="GET" class="d-flex mb-4">
      <input type="text" name="search" class="form-control me-2" placeholder="Search Products" value="<?= htmlspecialchars($searchTerm) ?>" />
      <button class="btn btn-primary" type="submit">Search</button>
    </form>

    <!-- Table Section -->
    <div class="container-box">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>Product ID</th>
            <th>Name</th>
            <th>Unit Cost</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Wholesaler ID</th>
            <th>Retailer ID</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($purchases) > 0): ?>
            <?php foreach ($purchases as $product): ?>
              <tr>
                <td><?= htmlspecialchars($product['product_id']) ?></td>
                <td><?= htmlspecialchars($product['product_name']) ?></td>
                <td><?= htmlspecialchars($product['unit_cost']) ?></td>
                <td><?= htmlspecialchars($product['quantity']) ?></td>
                <td><?= htmlspecialchars($product['date']) ?></td>
                <td><?= htmlspecialchars($product['wholesaler_id']) ?></td>
                <td><?= htmlspecialchars($product['retailer_id']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center">No products found for your search.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
