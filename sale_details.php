<?php
// DATABASE CONNECTION
$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM sale_details WHERE sale_details_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    header("Location: sale_details.php");
    exit();
}

// ADD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $details_id = $_POST['saleDetailsId'];
    $quantity = $_POST['quantitySold'];
    $price = $_POST['unitPrice'];
    $sale_id = $_POST['saleId'];
    $product_id = $_POST['productId'];

    $stmt = $conn->prepare("INSERT INTO sale_details (sale_details_id, quantity_sold, unit_price, sale_id, product_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sddss", $details_id, $quantity, $price, $sale_id, $product_id);
    $stmt->execute();
}

$sales = $conn->query("SELECT sale_id FROM sale");
$products = $conn->query("SELECT product_id FROM agri_product");
$records = $conn->query("SELECT * FROM sale_details");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sale Details</title>
  <style>
    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 40px; background: #f1f5f9; color: #1e293b; }
    h2 { margin-bottom: 30px; color: #1e293b; }
    .form-section { background: #fff; padding: 24px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 20px; }
    .form-group { display: flex; flex-direction: column; width: calc(33.333% - 20px); }
    label { font-weight: 600; margin-bottom: 6px; }
    input, select { padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
    button { padding: 10px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; }
    .add-btn { background-color: #3b82f6; color: white; }
    .add-btn:hover { background-color: #2563eb; }
    .back-btn, .view-btn { background-color: #10b981; color: white; margin-right: 10px; }
    .view-btn:hover, .back-btn:hover { background-color: #059669; }
    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
    th, td { padding: 14px 16px; text-align: center; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
    th { background-color: #f8fafc; font-weight: 600; }
    .edit-btn { background: #f59e0b; color: white; }
    .delete-btn { background: #ef4444; color: white; }
  </style>
</head>
<body>

<h2>Sale Details</h2>

<div style="margin-bottom: 20px;">
  <a href="retailer_dashboard.php"><button class="back-btn">← Back to Dashboard</button></a>
  <a href="price_trends.php"><button class="view-btn">View Price Trends</button></a>
</div>

<form method="POST">
  <input type="hidden" name="add" value="1">
  <div class="form-section">
    <div class="form-group"><label>Sale Details ID</label><input type="text" name="saleDetailsId" required></div>
    <div class="form-group"><label>Quantity Sold</label><input type="number" name="quantitySold" required></div>
    <div class="form-group"><label>Unit Price</label><input type="number" step="0.01" name="unitPrice" required></div>
    <div class="form-group"><label>Sale ID</label>
      <select name="saleId" required>
        <option value="">-- Select --</option>
        <?php while($s = $sales->fetch_assoc()): ?>
          <option value="<?= $s['sale_id'] ?>"><?= $s['sale_id'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group"><label>Product ID</label>
      <select name="productId" required>
        <option value="">-- Select --</option>
        <?php while($p = $products->fetch_assoc()): ?>
          <option value="<?= $p['product_id'] ?>"><?= $p['product_id'] ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group" style="width: 100px;"><button class="add-btn" type="submit">Add</button></div>
  </div>
</form>

<table>
  <thead>
    <tr>
      <th>Sale Details ID</th>
      <th>Quantity Sold</th>
      <th>Unit Price</th>
      <th>Sale ID</th>
      <th>Product ID</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $records->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['sale_details_id']) ?></td>
      <td><?= htmlspecialchars($row['quantity_sold']) ?></td>
      <td><?= htmlspecialchars($row['unit_price']) ?></td>
      <td><?= htmlspecialchars($row['sale_id']) ?></td>
      <td><?= htmlspecialchars($row['product_id']) ?></td>
      <td>
        <a href="edit_sale_details.php?sale_details_id=<?= $row['sale_details_id'] ?>"><button class="edit-btn">Edit</button></a>
        <a href="?delete=<?= $row['sale_details_id'] ?>" onclick="return confirm('Are you sure?')"><button class="delete-btn">Delete</button></a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
