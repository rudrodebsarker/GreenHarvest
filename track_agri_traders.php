<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
// Add Record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $product_id = $conn->real_escape_string($_POST['product_id']);
    $wholesaler_id = $conn->real_escape_string($_POST['wholesaler_id']);
    $retailer_id = $conn->real_escape_string($_POST['retailer_id']);
    $unit_cost = $conn->real_escape_string($_POST['unit_cost']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $date = $conn->real_escape_string($_POST['date']);

    $stmt = $conn->prepare("INSERT INTO track_agri_traders (product_id, unit_cost, quantity, date, wholesaler_id, retailer_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idissi", $product_id, $unit_cost, $quantity, $date, $wholesaler_id, $retailer_id);
    $stmt->execute();
    $stmt->close();
}

// Delete Record
if (isset($_GET['delete'])) {
    $product_id = $conn->real_escape_string($_GET['product_id']);
    $wholesaler_id = $conn->real_escape_string($_GET['wholesaler_id']);
    $retailer_id = $conn->real_escape_string($_GET['retailer_id']);

    $stmt = $conn->prepare("DELETE FROM track_agri_traders WHERE product_id=? AND wholesaler_id=? AND retailer_id=?");
    $stmt->bind_param("iii", $product_id, $wholesaler_id, $retailer_id);
    $stmt->execute();
    $stmt->close();
    header("Location: track_agri_traders.php");
    exit();
}

// Search Functionality
$search = '';
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $searchQuery = " AND (t.product_id LIKE '%$search%' 
                   OR p.name LIKE '%$search%'
                   OR w.name LIKE '%$search%'
                   OR r.name LIKE '%$search%')";
} else {
    $searchQuery = '';
}

// Fetch Data
$query = "SELECT t.*, p.name AS product_name, w.name AS wholesaler_name, r.name AS retailer_name 
          FROM track_agri_traders t
          JOIN agri_product p ON t.product_id = p.product_id
          JOIN wholesaler w ON t.wholesaler_id = w.wholesaler_id
          JOIN retailer r ON t.retailer_id = r.retailer_id
          WHERE 1 $searchQuery";
$result = $conn->query($query);

// Fetch dropdown data
$products = $conn->query("SELECT * FROM agri_product");
$wholesalers = $conn->query("SELECT * FROM wholesaler");
$retailers = $conn->query("SELECT * FROM retailer");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Track Agri Traders</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
    .container-box { background-color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); margin-top: 20px; }
    .form-label { font-weight: 600; }
    .btn-primary { padding: 8px 20px; }
    .btn-success { font-weight: 600; }
    .action-btn { margin-right: 5px; }
  </style>
</head>
<body>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Track Agri Traders</h3>
    <a href="admin_dashboard.php" class="btn btn-success">← Back to Dashboard</a>
  </div>

  <!-- Search Section -->
  <div class="container-box">
    <form method="GET">
      <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search by ID or Name" value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-primary" type="submit">Search</button>
      </div>
    </form>
  </div>

  <!-- Input Form -->
  <div class="container-box mt-4">
    <form method="POST">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Product</label>
          <select class="form-select" name="product_id" required>
            <option value="">Select Product</option>
            <?php while($product = $products->fetch_assoc()): ?>
              <option value="<?= $product['product_id'] ?>"><?= $product['name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Wholesaler</label>
          <select class="form-select" name="wholesaler_id" required>
            <option value="">Select Wholesaler</option>
            <?php while($wholesaler = $wholesalers->fetch_assoc()): ?>
              <option value="<?= $wholesaler['wholesaler_id'] ?>"><?= $wholesaler['name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Retailer</label>
          <select class="form-select" name="retailer_id" required>
            <option value="">Select Retailer</option>
            <?php while($retailer = $retailers->fetch_assoc()): ?>
              <option value="<?= $retailer['retailer_id'] ?>"><?= $retailer['name'] ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Unit Cost</label>
          <input type="number" step="0.01" name="unit_cost" class="form-control" placeholder="Enter Unit Cost" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Quantity</label>
          <input type="number" name="quantity" class="form-control" placeholder="Enter Quantity" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Date</label>
          <input type="date" name="date" class="form-control" required>
        </div>

        <div class="col-md-12 d-flex justify-content-end">
          <button type="submit" name="add" class="btn btn-primary">Add</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Table Section -->
  <div class="container-box mt-4">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Product</th>
          <th>Unit Cost</th>
          <th>Quantity</th>
          <th>Date</th>
          <th>Wholesaler</th>
          <th>Retailer</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['product_name']) ?></td>
          <td><?= htmlspecialchars($row['unit_cost']) ?></td>
          <td><?= htmlspecialchars($row['quantity']) ?></td>
          <td><?= htmlspecialchars($row['date']) ?></td>
          <td><?= htmlspecialchars($row['wholesaler_name']) ?></td>
          <td><?= htmlspecialchars($row['retailer_name']) ?></td>
          <td>
            <a href="edit_track_agri_traders.php?product_id=<?= $row['product_id'] ?>&wholesaler_id=<?= $row['wholesaler_id'] ?>&retailer_id=<?= $row['retailer_id'] ?>" class="btn btn-warning btn-sm action-btn">Edit</a>
            <a href="track_agri_traders.php?delete=1&product_id=<?= $row['product_id'] ?>&wholesaler_id=<?= $row['wholesaler_id'] ?>&retailer_id=<?= $row['retailer_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
<?php $conn->close(); ?>