<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch old data
if (isset($_GET['product_id']) && isset($_GET['wholesaler_id']) && isset($_GET['retailer_id'])) {
    $product_id = $conn->real_escape_string($_GET['product_id']);
    $wholesaler_id = $conn->real_escape_string($_GET['wholesaler_id']);
    $retailer_id = $conn->real_escape_string($_GET['retailer_id']);

    $stmt = $conn->prepare("SELECT * FROM track_agri_traders WHERE product_id=? AND wholesaler_id=? AND retailer_id=?");
    $stmt->bind_param("iii", $product_id, $wholesaler_id, $retailer_id);
    $stmt->execute();
    $oldData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    header("Location: track_agri_traders.php");
    exit();
}

// Update record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $new_product_id = $conn->real_escape_string($_POST['product_id']);
    $new_wholesaler_id = $conn->real_escape_string($_POST['wholesaler_id']);
    $new_retailer_id = $conn->real_escape_string($_POST['retailer_id']);
    $unit_cost = $conn->real_escape_string($_POST['unit_cost']);
    $quantity = $conn->real_escape_string($_POST['quantity']);
    $date = $conn->real_escape_string($_POST['date']);

    $stmt = $conn->prepare("UPDATE track_agri_traders 
                            SET product_id=?, wholesaler_id=?, retailer_id=?, unit_cost=?, quantity=?, date=?
                            WHERE product_id=? AND wholesaler_id=? AND retailer_id=?");
    $stmt->bind_param("iiisdssii", $new_product_id, $new_wholesaler_id, $new_retailer_id, $unit_cost, $quantity, $date, $product_id, $wholesaler_id, $retailer_id);
    $stmt->execute();
    $stmt->close();

    header("Location: track_agri_traders.php");
    exit();
}

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
  <title>Edit Agri Trader</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f1f5f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; }
    .container-box { background-color: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); margin-top: 20px; }
    .form-label { font-weight: 600; }
    .btn-primary { padding: 8px 20px; }
    .btn-success { font-weight: 600; }
  </style>
</head>
<body>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Edit Agri Trader</h3>
    <a href="track_agri_traders.php" class="btn btn-success">← Back to List</a>
  </div>

  <div class="container-box">
    <form method="POST">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Product</label>
          <select name="product_id" class="form-select" required>
            <?php while($product = $products->fetch_assoc()): ?>
              <option value="<?= $product['product_id'] ?>" <?= ($product['product_id'] == $oldData['product_id']) ? 'selected' : '' ?>>
                <?= $product['name'] ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Wholesaler</label>
          <select name="wholesaler_id" class="form-select" required>
            <?php while($wholesaler = $wholesalers->fetch_assoc()): ?>
              <option value="<?= $wholesaler['wholesaler_id'] ?>" <?= ($wholesaler['wholesaler_id'] == $oldData['wholesaler_id']) ? 'selected' : '' ?>>
                <?= $wholesaler['name'] ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Retailer</label>
          <select name="retailer_id" class="form-select" required>
            <?php while($retailer = $retailers->fetch_assoc()): ?>
              <option value="<?= $retailer['retailer_id'] ?>" <?= ($retailer['retailer_id'] == $oldData['retailer_id']) ? 'selected' : '' ?>>
                <?= $retailer['name'] ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="col-md-4">
          <label class="form-label">Unit Cost</label>
          <input type="number" step="0.01" name="unit_cost" class="form-control" value="<?= $oldData['unit_cost'] ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Quantity</label>
          <input type="number" name="quantity" class="form-control" value="<?= $oldData['quantity'] ?>" required>
        </div>

        <div class="col-md-4">
          <label class="form-label">Date</label>
          <input type="date" name="date" class="form-control" value="<?= $oldData['date'] ?>" required>
        </div>

        <div class="col-md-12 d-flex justify-content-end">
          <button type="submit" name="update" class="btn btn-primary">Update</button>
        </div>
      </div>
    </form>
  </div>
</div>

</body>
</html>
<?php $conn->close(); ?>
