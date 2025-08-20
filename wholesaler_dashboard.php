<?php
include('server.php');

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Wholesaler') {
    $_SESSION['msg'] = "You must log in as Wholesaler first";
    header('location: login.php');
    exit();
}

$wholesaler = null;
if (isset($_SESSION['selected_wholesaler'])) {
    $wholesaler = $_SESSION['selected_wholesaler'];
} else {
    $username = $_SESSION['username'];
    $wholesaler_query = "SELECT * FROM wholesaler WHERE name = '$username' LIMIT 1"; 
    $wholesaler_result = mysqli_query($db, $wholesaler_query);
    $wholesaler = mysqli_fetch_assoc($wholesaler_result);
}

$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$wholesaler_id = $wholesaler['wholesaler_id'] ?? '';
$products = [];
if ($wholesaler_id) {
    $query = "
        SELECT 
            ap.product_id,
            ap.name,
            ap.seasonality,
            ap.type,
            GROUP_CONCAT(DISTINCT apv.variety SEPARATOR ', ') AS varieties,
            sap.quantity_shipped,
            COALESCE(SUM(tat.quantity), 0) AS quantity_sold
        FROM shipment_agri_product sap
        JOIN agri_product ap ON sap.product_id = ap.product_id
        LEFT JOIN agri_product_variety apv ON ap.product_id = apv.product_id
        JOIN shipment s ON sap.shipment_id = s.shipment_id
        JOIN warehouse w ON s.warehouse_id = w.warehouse_id
        LEFT JOIN track_agri_traders tat 
            ON tat.product_id = ap.product_id AND tat.wholesaler_id = w.wholesaler_id
        WHERE w.wholesaler_id = ?
        GROUP BY sap.shipment_id, sap.product_id
        HAVING quantity_shipped > quantity_sold
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $wholesaler_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

$shipmentLabels = [];
$shipmentQuantities = [];
$shipment_result = $conn->query("SELECT sap.shipment_id, p.name AS product_name, sap.quantity_shipped
    FROM SHIPMENT_AGRI_PRODUCT sap
    JOIN AGRI_PRODUCT p ON sap.product_id = p.product_id");
while ($row = $shipment_result->fetch_assoc()) {
    $shipmentLabels[] = "Shipment: {$row['shipment_id']} - Product: {$row['product_name']}";
    $shipmentQuantities[] = (int)$row['quantity_shipped'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Wholesaler Dashboard</title>
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
    
    h2 {
      font-size: 1.8rem;
      color: #2c3e50;
      margin-bottom: 20px;
    }

    .flex-container {
      display: flex;
      gap: 30px;
      align-items: flex-start;
      margin-bottom: 30px;
    }
    .info-card, .charts-box {
      background-color: #ffffff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .info-card {
      flex: 1;
    }
    .charts-box {
      flex: 1;
    }
    .products-box {
      background-color: #ffffff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      text-align: center;
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
      .flex-container {
        flex-direction: column;
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
        <li><a href="update_wholesaler_profile.php">Update Profile</a></li>
        <li><a href="Wholesaler.php">Join Database</a></li>
        <li><a href="inventory.php">Inventory</a></li>
        <li><a href="received_products_from_farmer.php">Received Products</a></li>
        <li><a href="w_shipment.php">Shipments</a></li>
        <li><a href="logistics.php">Logistics</a></li>
        <li><a href="track_sales.php">Track Sales</a></li>
        <li><a href="buyer_info_in_wholesaler.php">Buyer Info</a></li>
        <li><a href="seller_info_in_wholesaler.php">Seller Info</a></li>
        <li id="Logout"><a href="index.php">Logout</a></li>
    </ul>
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
  </nav>

  <div class="main-content">
    <div class="flex-container">
      <div class="info-card">
        <h2>Wholesaler Information</h2>
        <p><strong>Wholesaler ID:</strong> <?= htmlspecialchars($wholesaler['wholesaler_id'] ?? 'N/A') ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($wholesaler['name'] ?? $_SESSION['username']) ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($wholesaler['contact'] ?? 'Not Updated') ?></p>
        <p><strong>Location:</strong> 
          <?php
          if (isset($wholesaler['road'], $wholesaler['house'], $wholesaler['area'], $wholesaler['district'], $wholesaler['country'])) {
              echo htmlspecialchars($wholesaler['road']) . ', ' .
                   htmlspecialchars($wholesaler['house']) . ', ' .
                   htmlspecialchars($wholesaler['area']) . ', ' .
                   htmlspecialchars($wholesaler['district']) . ', ' .
                   htmlspecialchars($wholesaler['country']);
          } else {
              echo "Not Updated";
          }
          ?>
        </p>
      </div>

      <div class="charts-box">
        <h2>Shipment Status Chart</h2>
        <canvas id="shipmentChart"></canvas>
      </div>
    </div>

    <div class="products-box">
      <h2>Available Products to Wholesaler</h2>
      <div style="margin-bottom: 15px;">
        <input type="text" id="searchInput" placeholder="Search by Product Name or ID..." style="padding: 6px; width: 250px;">
        <button onclick="searchTable()" style="padding: 6px 12px;">Search</button>
      </div>
      <table id="productTable">
        <thead>
          <tr>
            <th>Product ID</th>
            <th>Name</th>
            <th>Seasonality</th>
            <th>Type</th>
            <th>Variety</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
              <tr>
                <td><?= htmlspecialchars($product['product_id']) ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['seasonality']) ?></td>
                <td><?= htmlspecialchars($product['type']) ?></td>
                <td><?= htmlspecialchars($product['varieties']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5">No available products found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
      
    <footer>
        <p>&copy; 2025 Wholesaler Dashboard | GreenHarvest</p>
    </footer>
  </div>

  <script>
    function searchTable() {
      const input = document.getElementById("searchInput").value.toLowerCase();
      const rows = document.querySelectorAll("#productTable tbody tr");

      rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        const match = [...cells].slice(0, 2).some(cell =>
          cell.textContent.toLowerCase().includes(input)
        );
        row.style.display = match ? "" : "none";
      });
    }

    const ctx = document.getElementById('shipmentChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($shipmentLabels) ?>,
        datasets: [{
          label: 'Quantity Shipped',
          data: <?= json_encode($shipmentQuantities) ?>,
          backgroundColor: 'rgba(255, 159, 64, 0.2)',
          borderColor: 'rgba(255, 159, 64, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
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