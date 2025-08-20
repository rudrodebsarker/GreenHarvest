<?php
include('server.php');


$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Fetch product list for dropdown
$productQuery = "SELECT product_id, name FROM agri_product";
$productResult = $conn->query($productQuery);

$products = [];
while ($row = $productResult->fetch_assoc()) {
    $products[] = $row;
}

// Filters
$selectedProduct = $_GET['product'] ?? '';
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';
$salesData = [];

if ($selectedProduct && $fromDate && $toDate) {
    $sql = "
        SELECT 
            sd.sale_details_id,
            sd.unit_price,
            sd.product_id,
            s.sale_id,
            s.sale_date,
            s.time,
            p.name,
            r.district
        FROM sale_details sd
        JOIN sale s ON sd.sale_id = s.sale_id
        JOIN retailer r ON s.retailer_id = r.retailer_id
        JOIN agri_product p ON sd.product_id = p.product_id
        WHERE sd.product_id = ? AND s.sale_date BETWEEN ? AND ?
        ORDER BY s.sale_date ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $selectedProduct, $fromDate, $toDate);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $salesData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Price Trends</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f1f5f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
    }

    .filters {
      padding: 20px 30px;
      background-color: #fff;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 16px;
    }

    .filters select,
    .filters input,
    .filters button {
      padding: 8px 12px;
      font-size: 14px;
      border-radius: 5px;
      border: 1px solid #cbd5e1;
    }

    .filters .btn-primary {
      background-color: #3b82f6;
      color: white;
      font-weight: 600;
      border: none;
    }

    .filters .btn-primary:hover {
      background-color: #2563eb;
    }

    .filters .btn-success {
      font-weight: 600;
    }

    .filters .export-button {
      background-color: #10b981;
      color: white;
      border: none;
    }

    .filters .export-button:hover {
      background-color: #059669;
    }

    .charts-container {
      display: flex;
      justify-content: space-between;
      flex-wrap: nowrap;
      padding: 20px 30px;
      gap: 10px;
    }

    canvas {
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      width: 48%;
      height: 250px !important;
    }

    table {
      width: 96%;
      margin: 10px auto;
      background-color: white;
      border-collapse: collapse;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    th, td {
      padding: 12px;
      font-size: 14px;
      border-bottom: 1px solid #e2e8f0;
    }

    th {
      background-color: #f8fafc;
    }
  </style>
</head>

<body>
<div class="filters">
  <a href="admin_dashboard.php" class="btn btn-success">← Back to Dashboard</a>

  <form method="GET" class="d-flex flex-wrap align-items-center gap-2 mb-0">
    <select name="product" required>
      <option value="">Select Product</option>
      <?php foreach ($products as $p): ?>
        <option value="<?= $p['product_id'] ?>" <?= $selectedProduct == $p['product_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($p['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <input type="date" name="from" value="<?= htmlspecialchars($fromDate) ?>" required />
    <input type="date" name="to" value="<?= htmlspecialchars($toDate) ?>" required />
    <button type="submit" class="btn btn-primary">Apply Filters</button>
  </form>

  <button class="btn export-button" onclick="exportCSVFromCharts()">Export to CSV</button>
  <button class="btn btn-secondary" onclick="window.location.href='price_list.php'">Check Price List</button>
</div>

<div class="charts-container">
  <canvas id="priceTrendChart"></canvas>
  <canvas id="regionPriceChart"></canvas>
</div>

<table>
  <thead>
    <tr>
      <th>Product ID</th>
      <th>Name</th>
      <th>Sale Details ID</th>
      <th>Unit Price</th>
      <th>Sale ID</th>
      <th>Date</th>
      <th>Time</th>
      <th>District</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($salesData as $sale): ?>
      <tr>
        <td><?= htmlspecialchars($sale['product_id']) ?></td>
        <td><?= htmlspecialchars($sale['name']) ?></td>
        <td><?= htmlspecialchars($sale['sale_details_id']) ?></td>
        <td><?= htmlspecialchars($sale['unit_price']) ?></td>
        <td><?= htmlspecialchars($sale['sale_id']) ?></td>
        <td><?= htmlspecialchars($sale['sale_date']) ?></td>
        <td><?= htmlspecialchars($sale['time']) ?></td>
        <td><?= htmlspecialchars($sale['district']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<script>
  const salesData = <?= json_encode($salesData) ?>;
  const timeLabels = salesData.map(row => row.sale_date);
  const unitPrices = salesData.map(row => parseFloat(row.unit_price));

  const regionPriceMap = {};
  salesData.forEach(row => {
    if (!regionPriceMap[row.district]) regionPriceMap[row.district] = [];
    regionPriceMap[row.district].push(parseFloat(row.unit_price));
  });

  const regionLabels = Object.keys(regionPriceMap);
  const avgPricesByRegion = regionLabels.map(region => {
    const prices = regionPriceMap[region];
    const avg = prices.reduce((a, b) => a + b, 0) / prices.length;
    return avg.toFixed(2);
  });

  new Chart(document.getElementById('priceTrendChart'), {
    type: 'line',
    data: {
      labels: timeLabels,
      datasets: [{
        label: 'Unit Price Over Time',
        data: unitPrices,
        borderColor: '#3b82f6',
        tension: 0.3,
        fill: false
      }]
    },
    options: {
      plugins: { title: { display: true, text: 'Price Trends Over Time' }},
      scales: {
        x: { title: { display: true, text: 'Date' }},
        y: { title: { display: true, text: 'Price' }}
      }
    }
  });

  new Chart(document.getElementById('regionPriceChart'), {
    type: 'bar',
    data: {
      labels: regionLabels,
      datasets: [{
        label: 'Average Price by District',
        data: avgPricesByRegion,
        backgroundColor: '#10b981'
      }]
    },
    options: {
      plugins: { title: { display: true, text: 'Unit Prices by Region' }},
      scales: {
        x: { title: { display: true, text: 'District' }},
        y: { title: { display: true, text: 'Avg. Price' }}
      }
    }
  });

  function exportCSVFromCharts() {
    let csv = 'Price Trends Over Time\nDate,Unit Price\n';
    salesData.forEach(row => {
      csv += `${row.sale_date},${row.unit_price}\n`;
    });

    csv += '\nUnit Prices by Region\nDistrict,Average Unit Price\n';
    regionLabels.forEach((region, i) => {
      csv += `${region},${avgPricesByRegion[i]}\n`;
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'price_trends.csv';
    link.click();
  }
</script>
</body>
</html>