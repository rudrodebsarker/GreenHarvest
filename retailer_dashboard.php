<?php
include('server.php');

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Retailer') {
    $_SESSION['msg'] = "You must log in as Retailer first";
    header('location: login.php');
    exit();
}

// Get the user_id from session
$user_id = $_SESSION['user_id'];

$retailer = null;
if (isset($_SESSION['selected_retailer'])) {
    $retailer = $_SESSION['selected_retailer'];
} else {
    // Try to find retailer data based on user_id
    $conn = new mysqli("localhost", "root", "", "agriculture");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    
    $sql = "SELECT * FROM retailer WHERE retailer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $retailer = $result->fetch_assoc();
        $_SESSION['selected_retailer'] = $retailer;
    }
}

$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$availableProducts = [];
if (isset($retailer['retailer_id'])) {
    $retailer_id = $retailer['retailer_id'];
    $sql = "
        SELECT 
            t.product_id,
            SUM(t.quantity) AS total_purchased,
            p.name,
            p.seasonality,
            p.type,
            v.variety
        FROM track_agri_traders t
        JOIN agri_product p ON t.product_id = p.product_id
        LEFT JOIN agri_product_variety v ON t.product_id = v.product_id
        WHERE t.retailer_id = ?
        GROUP BY t.product_id
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $retailer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $purchased_quantity = (int) $row['total_purchased'];

        $sold_sql = "
            SELECT SUM(sd.quantity_sold) AS total_sold
            FROM sale s
            JOIN sale_details sd ON s.sale_id = sd.sale_id
            WHERE s.retailer_id = ? AND sd.product_id = ?
        ";
        $sold_stmt = $conn->prepare($sold_sql);
        $sold_stmt->bind_param("ss", $retailer_id, $product_id);
        $sold_stmt->execute();
        $sold_result = $sold_stmt->get_result();
        $sold_row = $sold_result->fetch_assoc();
        $sold_quantity = (int) ($sold_row['total_sold'] ?? 0);

        $available_quantity = $purchased_quantity - $sold_quantity;

        if ($available_quantity > 0) {
            $availableProducts[] = [
                'product_id' => $product_id,
                'name' => $row['name'],
                'seasonality' => $row['seasonality'],
                'type' => $row['type'],
                'variety' => $row['variety'] ?? 'N/A',
                'quantity' => $available_quantity
            ];
        }
    }
}

$productQuery = "SELECT product_id, name FROM agri_product";
$productResult = $conn->query($productQuery);
$products = [];
while ($row = $productResult->fetch_assoc()) {
    $products[] = $row;
}

$selectedProduct = $_GET['product'] ?? '';
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';
$salesData = [];
if ($selectedProduct && $fromDate && $toDate) {
    $sql = "
        SELECT 
            sd.unit_price,
            s.sale_date
        FROM sale_details sd
        JOIN sale s ON sd.sale_id = s.sale_id
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Retailer Dashboard</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* General Reset & Body Styling */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0f2f5;
      color: #333;
      height: 100%;
    }

    /* The sidebar itself */
    .navbar {
      width: 250px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background: linear-gradient(180deg, #2c3e50, #34495e);
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
      display: flex;
      flex-direction: column;
      z-index: 1000;
      overflow-y: auto;
    }

    /* Logo and branding at the top of the sidebar */
    .navbar-left {
      padding: 25px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .navbar .logo-img {
      height: 80px;
      width: 80px;
      border-radius: 50%;
      border: 3px solid #ecf0f1;
      object-fit: cover;
    }

    .navbar .logo {
      font-size: 1.4rem;
      font-weight: 600;
      color: #ecf0f1;
      text-decoration: none;
      text-align: center;
    }

    /* Navigation links container */
    .nav-links {
      list-style: none;
      padding: 0;
      margin: 0;
      flex-grow: 1; /* Pushes logout to the bottom */
      display: flex;
      flex-direction: column;
    }

    .nav-links li {
      width: 100%;
    }

    /* Individual navigation links */
    .nav-links a {
      display: block;
      padding: 16px 30px;
      color: #ecf0f1;
      text-decoration: none;
      font-size: 1rem;
      font-weight: 500;
      transition: background 0.3s ease, color 0.3s ease, padding-left 0.3s ease;
      border-left: 5px solid transparent;
    }

    .nav-links a:hover,
    .nav-links li.active a { /* Style for active page link */
      background: #3498db;
      color: #fff;
      padding-left: 35px;
      border-left-color: #ecf0f1;
    }

    /* Logout Button */
    #Logout {
      margin-top: auto; /* Stick to the bottom */
    }

    #Logout a {
      background-color: rgba(231, 76, 60, 0.8);
      border-left: 5px solid transparent;
    }

    #Logout a:hover {
      background-color: #e74c3c;
      border-left-color: #c0392b;
      padding-left: 35px;
    }

    /* Main Content Area */
    .main-content {
      margin-left: 250px; /* Same as sidebar width */
      padding: 40px;
      transition: margin-left 0.3s ease;
    }
    
    .container {
        padding: 0;
    }

    h2 {
        font-size: 1.8rem;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 20px 0;
      margin-top: 40px;
      color: #95a5a6;
    }
    
    .menu-toggle {
        display: none;
    }

    .dashboard-content {
      display: flex;
      gap: 40px;
      flex-wrap: wrap;
    }
    .retailer-info, .charts-box {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .retailer-info {
      flex: 1;
      min-width: 280px;
    }
    .charts-box {
      flex: 2;
      min-width: 300px;
    }
    canvas {
      width: 100% !important;
      height: 300px !important;
    }
    .products-box {
      background-color: #ffffff;
      padding: 25px;
      border-radius: 10px;
      margin-top: 30px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
      .navbar {
        width: 100%;
        height: auto;
        position: relative;
        flex-direction: row;
        justify-content: space-between;
        padding: 0 20px;
      }
      .navbar-left {
        flex-direction: row;
        border-bottom: none;
        padding: 10px 0;
      }
      .logo {
        margin-left: 15px;
      }
      .nav-links {
        display: none; /* Hide links for a mobile toggle */
        flex-direction: column;
        width: 100%;
        position: absolute;
        top: 70px;
        left: 0;
        background: #34495e;
      }
      .main-content {
        margin-left: 0;
        padding: 20px;
      }
      .menu-toggle {
        display: block; /* Show hamburger */
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
      }
      .nav-links.active {
        display: flex;
      }
      #Logout {
        margin-top: 0;
      }
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-left">
      <img class="logo-img" src="images/pic2.png" alt="Logo">
      <a href="#" class="logo">GreenHarvest</a>
    </div>
    <ul class="nav-links">
        <li><a href="price_trends.php">Price Trends</a></li>
        <li><a href="update_retailer_profile.php">Update Profile</a></li>
        <li><a href="Retailer.php?retailer_id=<?php echo isset($retailer['retailer_id']) ? $retailer['retailer_id'] : $user_id; ?>">Join Database</a></li>
        <li><a href="sale_information.php">Record Sale</a></li>
        <li><a href="Purchased_by_retailer.php">Purchases</a></li>
        <li><a href="buyers_in_retailer.php">Buyer Info</a></li>
        <li id="Logout"><a href="index.php">Logout</a></li>
    </ul>
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
  </nav>

  <div class="main-content">
    <div class="dashboard-content">
      <div class="retailer-info">
        <h2>Retailer Information</h2>
        <?php if ($retailer): ?>
          <p><strong>Retailer ID:</strong> <?= htmlspecialchars($retailer['retailer_id']) ?></p>
          <p><strong>Name:</strong> <?= htmlspecialchars($retailer['name']) ?></p>
          <p><strong>Contact:</strong> <?= htmlspecialchars($retailer['contact']) ?></p>
          <p><strong>Location:</strong> <?= htmlspecialchars($retailer['road']) ?>, <?= htmlspecialchars($retailer['area']) ?>, <?= htmlspecialchars($retailer['district']) ?>, <?= htmlspecialchars($retailer['country']) ?></p>
        <?php else: ?>
          <p><strong>Retailer ID:</strong> <?= htmlspecialchars($user_id) ?></p>
          <p><strong>Name:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
          <p><strong>Contact:</strong> Not Updated</p>
          <p><strong>Location:</strong> Not Updated</p>
        <?php endif; ?>
      </div>

      <div class="charts-box">
        <h2>Price Trends Over Time</h2>
        <form method="GET" style="margin-bottom: 20px;">
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
          <button type="submit">Apply Filters</button>
        </form>
        <canvas id="priceTrendChart"></canvas>
      </div>
    </div>

    <div class="products-box">
      <h2>Available Agricultural Products</h2>
      <table>
        <thead>
          <tr>
            <th>Product ID</th>
            <th>Name</th>
            <th>Seasonality</th>
            <th>Type</th>
            <th>Variety</th>
            <th>Quantity</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($availableProducts) > 0): ?>
            <?php foreach ($availableProducts as $product): ?>
              <tr>
                <td><?= htmlspecialchars($product['product_id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['seasonality']) ?></td>
                <td><?= htmlspecialchars($product['type']) ?></td>
                <td><?= htmlspecialchars($product['variety']) ?></td>
                <td><?= htmlspecialchars($product['quantity']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6">No available products found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
      
    <footer>
        <p>&copy; 2025 Retailer Dashboard | GreenHarvest</p>
    </footer>
  </div>

  <script>
    const salesData = <?= json_encode($salesData) ?>;
    const timeLabels = salesData.map(row => row.sale_date);
    const unitPrices = salesData.map(row => parseFloat(row.unit_price));

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

    // Mobile menu toggle
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    if(menuToggle) {
      menuToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
      });
    }
  </script>
</body>
</html>
