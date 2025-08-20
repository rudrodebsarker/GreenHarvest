<?php
include('server.php'); // Contains session_start() and DB connection

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

// Get consumers for this retailer
$sql = "
    SELECT s.sale_id, c.consumer_id, c.name, c.contact, c.email
    FROM sale s
    JOIN consumer c ON s.consumer_id = c.consumer_id
    WHERE s.retailer_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $retailer_id);
$stmt->execute();
$result = $stmt->get_result();

$buyers = [];

while ($row = $result->fetch_assoc()) {
    if (
        $searchTerm === '' ||
        stripos($row['name'], $searchTerm) !== false ||
        stripos($row['contact'], $searchTerm) !== false ||
        stripos($row['email'], $searchTerm) !== false ||
        stripos($row['consumer_id'], $searchTerm) !== false
    ) {
        $buyers[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Buyers List</title>

  <!-- Bootstrap CSS -->
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
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Buyers List</h3>
    <a href="retailer_dashboard.php" class="btn btn-success">← Back to Dashboard</a>
  </div>

  <!-- Search -->
  <form method="GET" class="d-flex mb-4">
    <input type="text" name="search" class="form-control me-2" placeholder="Search buyers by name, contact, email..." value="<?= htmlspecialchars($searchTerm) ?>" />
    <button class="btn btn-primary" type="submit">Search</button>
  </form>

  <!-- Table -->
  <div class="container-box">
    <table class="table table-bordered table-hover align-middle">
      <thead>
        <tr>
          <th>Sale ID</th>
          <th>Consumer ID</th>
          <th>Name</th>
          <th>Contact</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($buyers) > 0): ?>
          <?php foreach ($buyers as $buyer): ?>
            <tr>
              <td><?= htmlspecialchars($buyer['sale_id']) ?></td>
              <td><?= htmlspecialchars($buyer['consumer_id']) ?></td>
              <td><?= htmlspecialchars($buyer['name']) ?></td>
              <td><?= htmlspecialchars($buyer['contact']) ?></td>
              <td><?= htmlspecialchars($buyer['email']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center">No buyers found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
