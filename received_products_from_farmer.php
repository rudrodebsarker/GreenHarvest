<?php
include('server.php');

// Check if the user is logged in and is a Wholesaler
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Wholesaler') {
    $_SESSION['msg'] = "You must log in as Wholesaler first";
    header('location: login.php');
    exit();
}

$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get wholesaler ID from session
$wholesaler_id = '';
if (isset($_SESSION['selected_wholesaler']['wholesaler_id'])) {
    $wholesaler_id = $_SESSION['selected_wholesaler']['wholesaler_id'];
} else {
    $username = $_SESSION['username'];
    $result = $conn->query("SELECT wholesaler_id FROM wholesaler WHERE name = '$username' LIMIT 1");
    $row = $result->fetch_assoc();
    $wholesaler_id = $row['wholesaler_id'] ?? '';
}

// Fetch received products for this wholesaler
$products = [];
if ($wholesaler_id) {
    $query = "
        SELECT 
            sap.product_id,
            ap.name AS product_name,
            sap.cost,
            sap.quantity_shipped AS quantity,
            s.ship_date,
            pd.farmer_id,
            w.wholesaler_id
        FROM shipment_agri_product sap
        JOIN agri_product ap ON sap.product_id = ap.product_id
        JOIN shipment s ON sap.shipment_id = s.shipment_id
        JOIN warehouse w ON s.warehouse_id = w.warehouse_id
        JOIN production_data pd ON sap.product_id = pd.product_id
        WHERE w.wholesaler_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $wholesaler_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Received Products from Farmer</title>
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
    <h3>Received Products from Farmer</h3>
    <a href="wholesaler_dashboard.php" class="btn btn-success">← Back to Dashboard</a>
  </div>

  <div class="search-section">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by Product Name or Farmer ID..." style="max-width: 300px;">
    <button class="btn btn-primary" onclick="searchProduct()">Search</button>
  </div>

  <div class="container-box">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>Product ID</th>
          <th>Name</th>
          <th>Cost</th>
          <th>Quantity</th>
          <th>Date</th>
          <th>Farmer ID</th>
          <th>Wholesaler ID</th>
        </tr>
      </thead>
      <tbody id="receivedProductsTableBody">
        <?php foreach ($products as $product): ?>
          <tr>
            <td><?= htmlspecialchars($product['product_id']) ?></td>
            <td><?= htmlspecialchars($product['product_name']) ?></td>
            <td><?= htmlspecialchars($product['cost']) ?></td>
            <td><?= htmlspecialchars($product['quantity']) ?></td>
            <td><?= htmlspecialchars($product['ship_date']) ?></td>
            <td><?= htmlspecialchars($product['farmer_id']) ?></td>
            <td><?= htmlspecialchars($product['wholesaler_id']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  const allRows = Array.from(document.querySelectorAll("#receivedProductsTableBody tr"));

  function searchProduct() {
    const searchValue = document.getElementById("searchInput").value.toLowerCase();
    const tbody = document.getElementById("receivedProductsTableBody");
    tbody.innerHTML = '';

    const filtered = allRows.filter(row => {
      const name = row.children[1].textContent.toLowerCase();
      const farmerId = row.children[5].textContent.toLowerCase();
      return name.includes(searchValue) || farmerId.includes(searchValue);
    });

    filtered.forEach(row => tbody.appendChild(row));
  }
</script>

</body>
</html>
