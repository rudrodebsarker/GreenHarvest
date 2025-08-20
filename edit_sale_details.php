<?php
$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = $_GET['sale_details_id'];
$detail = $conn->query("SELECT * FROM sale_details WHERE sale_details_id = '$id'")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = $_POST['quantitySold'];
    $price = $_POST['unitPrice'];
    $sale_id = $_POST['saleId'];
    $product_id = $_POST['productId'];

    $stmt = $conn->prepare("UPDATE sale_details SET quantity_sold=?, unit_price=?, sale_id=?, product_id=? WHERE sale_details_id=?");
    $stmt->bind_param("ddsss", $quantity, $price, $sale_id, $product_id, $id);
    $stmt->execute();
    header("Location: sale_details.php");
    exit();
}

$sales = $conn->query("SELECT sale_id FROM sale");
$products = $conn->query("SELECT product_id FROM agri_product");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Sale Details</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 40px; }
    .container { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { text-align: center; color: #333; }
    label { display: block; margin-bottom: 10px; font-weight: bold; }
    input, select { width: 100%; padding: 8px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 5px; }
    button { padding: 10px 20px; border: none; background-color: #3498db; color: white; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
    button:hover { background-color: #2980b9; }
    .back-btn { margin-top: 15px; background-color: #2ecc71; }
    .back-btn:hover { background-color: #27ae60; }
  </style>
</head>
<body>

<div class="container">
  <h2>Edit Sale Details</h2>
  <form method="POST">
    <label>Sale Details ID</label>
    <input type="text" value="<?= $detail['sale_details_id'] ?>" disabled>

    <label>Quantity Sold</label>
    <input type="number" name="quantitySold" value="<?= $detail['quantity_sold'] ?>" required>

    <label>Unit Price</label>
    <input type="number" step="0.01" name="unitPrice" value="<?= $detail['unit_price'] ?>" required>

    <label>Sale ID</label>
    <select name="saleId" required>
      <?php while ($s = $sales->fetch_assoc()): ?>
        <option value="<?= $s['sale_id'] ?>" <?= $s['sale_id'] === $detail['sale_id'] ? 'selected' : '' ?>><?= $s['sale_id'] ?></option>
      <?php endwhile; ?>
    </select>

    <label>Product ID</label>
    <select name="productId" required>
      <?php while ($p = $products->fetch_assoc()): ?>
        <option value="<?= $p['product_id'] ?>" <?= $p['product_id'] === $detail['product_id'] ? 'selected' : '' ?>><?= $p['product_id'] ?></option>
      <?php endwhile; ?>
    </select>

    <button type="submit">Save Changes</button>
    <a href="sale_details.php"><button type="button" class="back-btn">Back to Sale Details</button></a>
  </form>
</div>

</body>
</html>
