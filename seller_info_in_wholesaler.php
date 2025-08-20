<?php
session_start();

// Direct DB connection (as requested)
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is a logged-in Wholesaler
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Wholesaler') {
    $_SESSION['msg'] = "You must log in as Wholesaler first";
    header('location: login.php');
    exit();
}

// Determine Wholesaler ID
$wholesaler_id = '';
if (isset($_SESSION['selected_wholesaler']['wholesaler_id'])) {
    $wholesaler_id = $_SESSION['selected_wholesaler']['wholesaler_id'];
} else {
    $username = $_SESSION['username'];
    $result = $conn->query("SELECT wholesaler_id FROM wholesaler WHERE name = '$username' LIMIT 1");
    $row = $result->fetch_assoc();
    $wholesaler_id = $row['wholesaler_id'] ?? '';
}

// Fetch unique farmer details from related received products
$farmers = [];
if ($wholesaler_id) {
    $query = "
        SELECT DISTINCT f.farmer_id, f.name, f.road, f.house, f.district, f.area, f.country, f.years_of_experience
        FROM shipment_agri_product sap
        JOIN shipment s ON sap.shipment_id = s.shipment_id
        JOIN warehouse w ON s.warehouse_id = w.warehouse_id
        JOIN production_data pd ON sap.product_id = pd.product_id
        JOIN farmer f ON pd.farmer_id = f.farmer_id
        WHERE w.wholesaler_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $wholesaler_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $farmers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Seller Information to Wholesaler</title>
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
    .search-section {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      align-items: center;
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
    <h3>Seller Information to Wholesaler</h3>
    <a href="wholesaler_dashboard.php" class="btn btn-success">← Back to Dashboard</a>
  </div>

  <div class="search-section">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by Farmer Name or ID..." style="max-width: 300px;">
    <button class="btn btn-primary" onclick="searchFarmer()">Search</button>
  </div>

  <div class="container-box">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>Farmer ID</th>
          <th>Name</th>
          <th>Road</th>
          <th>House</th>
          <th>District</th>
          <th>Area</th>
          <th>Country</th>
          <th>Years of Experience</th>
        </tr>
      </thead>
      <tbody id="farmerTableBody">
        <?php foreach ($farmers as $farmer): ?>
          <tr>
            <td><?= htmlspecialchars($farmer['farmer_id']) ?></td>
            <td><?= htmlspecialchars($farmer['name']) ?></td>
            <td><?= htmlspecialchars($farmer['road']) ?></td>
            <td><?= htmlspecialchars($farmer['house']) ?></td>
            <td><?= htmlspecialchars($farmer['district']) ?></td>
            <td><?= htmlspecialchars($farmer['area']) ?></td>
            <td><?= htmlspecialchars($farmer['country']) ?></td>
            <td><?= htmlspecialchars($farmer['years_of_experience']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  const originalRows = Array.from(document.querySelectorAll("#farmerTableBody tr"));

  function searchFarmer() {
    const searchValue = document.getElementById("searchInput").value.toLowerCase();
    const tbody = document.getElementById("farmerTableBody");
    tbody.innerHTML = '';

    const filtered = originalRows.filter(row => {
      const id = row.children[0].textContent.toLowerCase();
      const name = row.children[1].textContent.toLowerCase();
      return id.includes(searchValue) || name.includes(searchValue);
    });

    filtered.forEach(row => tbody.appendChild(row));
  }
</script>

</body>
</html>
