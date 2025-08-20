<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$query = "SELECT * FROM agri_product";
$result = $conn->query($query);

$products = [];
while ($product = $result->fetch_assoc()) {
  $productId = $product['product_id'];

  $priceQuery = "
    SELECT sd.unit_price, s.sale_date 
    FROM sale_details sd
    JOIN sale s ON sd.sale_id = s.sale_id
    WHERE sd.product_id = ?
    ORDER BY s.sale_date DESC
  ";

  $stmt = $conn->prepare($priceQuery);
  $stmt->bind_param("s", $productId);
  $stmt->execute();
  $priceResult = $stmt->get_result();

  $prices = [];
  while ($row = $priceResult->fetch_assoc()) {
    $prices[] = (float)$row['unit_price'];
  }

  $currentPrice = count($prices) > 0 ? $prices[0] : "N/A";
  $historicalPrices = array_slice($prices, 1);
  $historicalAvg = count($historicalPrices) > 0 ? number_format(array_sum($historicalPrices) / count($historicalPrices), 2) : "N/A";

  $products[] = [
    'id' => $product['product_id'],
    'name' => $product['name'],
    'seasonality' => $product['seasonality'],
    'type' => $product['type'],
    'historicalPrice' => $historicalAvg,
    'currentPrice' => $currentPrice
  ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Price List</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 40px;
      background-color: #f1f5f9;
      color: #1e293b;
    }

    h2 {
      margin-bottom: 24px;
      text-align: center;
    }

    .search-section {
      display: flex;
      justify-content: center;
      margin-bottom: 30px;
    }

    .search-section input {
      padding: 10px;
      width: 300px;
      border: 1px solid #cbd5e1;
      border-radius: 6px 0 0 6px;
      font-size: 14px;
    }

    .search-section button {
      padding: 10px 16px;
      border: none;
      border-radius: 0 6px 6px 0;
      background-color: #3b82f6;
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.2s ease-in-out;
    }

    .search-section button:hover {
      background-color: #2563eb;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    th, td {
      padding: 14px 16px;
      text-align: center;
      border-bottom: 1px solid #e2e8f0;
      font-size: 14px;
    }

    th {
      background-color: #f8fafc;
      font-weight: 600;
    }
  </style>
</head>
<body>
  <h2>Product Price List</h2>

  <div class="search-section">
    <input type="text" id="searchInput" placeholder="Search product by name...">
    <button onclick="searchProduct()">Search</button>
  </div>

  <table id="priceTable">
    <thead>
      <tr>
        <th>Product ID</th>
        <th>Name</th>
        <th>Seasonality</th>
        <th>Type</th>
        <th>Historical Price</th>
        <th>Current Price</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $product): ?>
        <tr>
          <td><?= htmlspecialchars($product['id']) ?></td>
          <td><?= htmlspecialchars($product['name']) ?></td>
          <td><?= htmlspecialchars($product['seasonality']) ?></td>
          <td><?= htmlspecialchars($product['type']) ?></td>
          <td><?= htmlspecialchars($product['historicalPrice']) ?></td>
          <td><?= htmlspecialchars($product['currentPrice']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <script>
    const products = <?= json_encode($products) ?>;

    function renderTable(data) {
      const tbody = document.querySelector('#priceTable tbody');
      tbody.innerHTML = '';
      data.forEach(product => {
        tbody.innerHTML += `
          <tr>
            <td>${product.id}</td>
            <td>${product.name}</td>
            <td>${product.seasonality}</td>
            <td>${product.type}</td>
            <td>${product.historicalPrice}</td>
            <td>${product.currentPrice}</td>
          </tr>
        `;
      });
    }

    function searchProduct() {
      const searchValue = document.getElementById('searchInput').value.toLowerCase();
      const filtered = products.filter(p => p.name.toLowerCase().includes(searchValue));
      renderTable(filtered);
    }
  </script>
</body>
</html>
