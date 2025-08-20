<?php
include('server.php');

if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'Wholesaler') {
    $_SESSION['msg'] = "You must log in as Wholesaler first";
    header('location: login.php');
    exit();
}

// Get wholesaler ID from session
if (isset($_SESSION['selected_wholesaler'])) {
    $wholesaler = $_SESSION['selected_wholesaler'];
} else {
    $username = $_SESSION['username'];
    $result = mysqli_query($db, "SELECT * FROM wholesaler WHERE name = '$username' LIMIT 1");
    $wholesaler = mysqli_fetch_assoc($result);
}

$wholesaler_id = $wholesaler['wholesaler_id'] ?? null;
$salesData = [];

if ($wholesaler_id) {
    $conn = new mysqli("localhost", "root", "", "agriculture");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $query = "
        SELECT product_id, unit_cost, quantity, date, wholesaler_id, retailer_id
        FROM track_agri_traders
        WHERE wholesaler_id = ?
        ORDER BY date DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $wholesaler_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $salesData[] = $row;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Sales</title>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>Track Sales</h3>
      <a href="wholesaler_dashboard.php" class="btn btn-success">&larr; Back to Dashboard</a>
    </div>

    <div class="container-box">
      <table class="table table-bordered table-hover align-middle">
        <thead>
          <tr>
            <th>Product ID</th>
            <th>Unit Cost</th>
            <th>Quantity</th>
            <th>Date</th>
            <th>Wholesaler ID</th>
            <th>Retailer ID</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($salesData)): ?>
            <?php foreach ($salesData as $sale): ?>
              <tr>
                <td><?= htmlspecialchars($sale['product_id']) ?></td>
                <td><?= htmlspecialchars($sale['unit_cost']) ?></td>
                <td><?= htmlspecialchars($sale['quantity']) ?></td>
                <td><?= htmlspecialchars($sale['date']) ?></td>
                <td><?= htmlspecialchars($sale['wholesaler_id']) ?></td>
                <td><?= htmlspecialchars($sale['retailer_id']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center">No sales data available.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
