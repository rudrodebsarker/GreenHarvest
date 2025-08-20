<?php
include('server.php'); // Assumes server.php handles DB connection and session_start()

if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'Retailer') {
    $_SESSION['msg'] = "You must log in as a Retailer first";
    header('location: login.php');
    exit();
}

// Get retailer_id from session-stored profile
if (!isset($_SESSION['selected_retailer']['retailer_id'])) {
    die("Retailer not selected. Please update your profile.");
}

$retailer_id = $_SESSION['selected_retailer']['retailer_id'];
$searchTerm = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';

$availableProducts = [];

$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Step 1: Get products purchased by retailer
$sql = "
    SELECT 
        t.product_id,
        SUM(t.quantity) AS total_purchased,
        p.name,
        p.seasonality,
        p.type,
        v.variety
    FROM track_agri_traders t
    JOIN agri_product p ON t.product_id = p.product_id
    LEFT JOIN agri_product_variety v ON t.product_id = v.product_id
    WHERE t.retailer_id = ?
    GROUP BY t.product_id
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $retailer_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $product_id = $row['product_id'];
    $purchased_quantity = (int) $row['total_purchased'];

    // Step 2: Get total quantity sold
    $sold_sql = "
        SELECT SUM(sd.quantity_sold) AS total_sold
        FROM sale s
        JOIN sale_details sd ON s.sale_id = sd.sale_id
        WHERE s.retailer_id = ? AND sd.product_id = ?
    ";
    $sold_stmt = $conn->prepare($sold_sql);
    $sold_stmt->bind_param("ss", $retailer_id, $product_id);
    $sold_stmt->execute();
    $sold_result = $sold_stmt->get_result();
    $sold_row = $sold_result->fetch_assoc();
    $sold_quantity = (int) ($sold_row['total_sold'] ?? 0);

    $available_quantity = $purchased_quantity - $sold_quantity;

    // Only include products that still have quantity remaining
    if ($available_quantity > 0) {
        if (
            $searchTerm === '' ||
            stripos($product_id, $searchTerm) !== false ||
            stripos($row['name'], $searchTerm) !== false
        ) {
            $availableProducts[] = [
                'product_id' => $product_id,
                'name' => $row['name'],
                'seasonality' => $row['seasonality'],
                'type' => $row['type'],
                'variety' => $row['variety'] ?? 'N/A',
                'quantity' => $available_quantity
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Available Agricultural Products</title>
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
    .search-section {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      align-items: center;
    }
    h3 {
      font-weight: bold;
      color: #1f2937;
    }
  </style>
</head>
<body>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Available Agricultural Products</h3>
    <a href="retailer_dashboard.php" class="btn btn-success">← Back to Dashboard</a>
  </div>

  <form method="GET" class="search-section">
    <input type="text" name="search" class="form-control" placeholder="Search by Product ID or Name..." style="max-width: 300px;" value="<?= htmlspecialchars($searchTerm) ?>">
    <button type="submit" class="btn btn-primary">Search</button>
  </form>

  <div class="container-box">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>Product ID</th>
          <th>Name</th>
          <th>Seasonality</th>
          <th>Type</th>
          <th>Variety</th>
          <th>Quantity</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($availableProducts) > 0): ?>
          <?php foreach ($availableProducts as $product): ?>
            <tr>
              <td><?= htmlspecialchars($product['product_id']) ?></td>
              <td><?= htmlspecialchars($product['name']) ?></td>
              <td><?= htmlspecialchars($product['seasonality']) ?></td>
              <td><?= htmlspecialchars($product['type']) ?></td>
              <td><?= htmlspecialchars($product['variety']) ?></td>
              <td><?= htmlspecialchars($product['quantity']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center">No available products found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
