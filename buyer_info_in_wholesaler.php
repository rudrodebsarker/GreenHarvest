<?php
session_start();

// Direct DB connection (no server.php)
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Access control: Only Wholesaler
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Wholesaler') {
    $_SESSION['msg'] = "You must log in as Wholesaler first";
    header('location: login.php');
    exit();
}

// Determine wholesaler_id
$wholesaler_id = '';
if (isset($_SESSION['selected_wholesaler']['wholesaler_id'])) {
    $wholesaler_id = $_SESSION['selected_wholesaler']['wholesaler_id'];
} else {
    $username = $_SESSION['username'];
    $query = "SELECT wholesaler_id FROM wholesaler WHERE name = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $wholesaler_id = $row['wholesaler_id'];
    }
}

// Fetch retailers who bought from this wholesaler
$retailers = [];
if ($wholesaler_id) {
    $query = "
        SELECT DISTINCT r.retailer_id, r.name, r.contact, r.road, r.area, r.district, r.country
        FROM track_agri_traders tat
        JOIN retailer r ON tat.retailer_id = r.retailer_id
        WHERE tat.wholesaler_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $wholesaler_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $retailers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Buyer Information to Wholesaler</title>
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
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Buyer Information to Wholesaler</h3>
    <a href="wholesaler_dashboard.php" class="btn btn-success">← Back to Dashboard</a>
  </div>

  <!-- Search -->
  <div class="search-section">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by Retailer Name or ID..." style="max-width: 300px;">
    <button class="btn btn-primary" onclick="searchRetailer()">Search</button>
  </div>

  <!-- Table -->
  <div class="container-box">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>Retailer ID</th>
          <th>Name</th>
          <th>Contact</th>
          <th>Road</th>
          <th>Area</th>
          <th>District</th>
          <th>Country</th>
        </tr>
      </thead>
      <tbody id="retailerTableBody">
        <?php foreach ($retailers as $retailer): ?>
          <tr>
            <td><?= htmlspecialchars($retailer['retailer_id']) ?></td>
            <td><?= htmlspecialchars($retailer['name']) ?></td>
            <td><?= htmlspecialchars($retailer['contact']) ?></td>
            <td><?= htmlspecialchars($retailer['road']) ?></td>
            <td><?= htmlspecialchars($retailer['area']) ?></td>
            <td><?= htmlspecialchars($retailer['district']) ?></td>
            <td><?= htmlspecialchars($retailer['country']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- JavaScript -->
<script>
  const originalRows = Array.from(document.querySelectorAll("#retailerTableBody tr"));

  function searchRetailer() {
    const searchValue = document.getElementById("searchInput").value.toLowerCase();
    const tbody = document.getElementById("retailerTableBody");
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
