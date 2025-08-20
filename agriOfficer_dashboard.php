<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Agricultural_Officer') {
    $_SESSION['msg'] = "You must log in as Agricultural Officer first";
    header('location: login.php');
    exit();
}


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agriculture";

$conn = new mysqli($servername, $username, $password, $dbname);

// Fetch sales data for Consumer Demand
function getSalesData($conn) {
    $sql = "SELECT s.sale_id, sd.product_id, sd.quantity_sold as quantity, 
                   sd.unit_price as price, DATE(s.sale_date) as date
            FROM sale s
            JOIN sale_details sd ON s.sale_id = sd.sale_id";
    
    $result = $conn->query($sql);
    
    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

// Fetch Price Elasticity Data
function getElasticityData($conn) {
    $sql = "SELECT 
                p.product_id, 
                p.name,
                AVG(sd.unit_price) as avg_price,
                SUM(sd.quantity_sold) as total_quantity
            FROM agri_product p
            LEFT JOIN sale_details sd ON p.product_id = sd.product_id
            GROUP BY p.product_id, p.name
            ORDER BY p.product_id";
    
    $result = $conn->query($sql);
    
    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    // Calculate price elasticity (simplified example)
    $elasticityData = [];
    foreach ($products as $product) {
        if ($product['avg_price'] > 0 && $product['total_quantity'] > 0) {
            $price_change = rand(5, 20); 
            $quantity_change = -rand(3, 15); 
            $elasticity = $quantity_change / $price_change;
            
            $elasticityData[] = [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'price_change' => $price_change,
                'quantity_change' => $quantity_change,
                'elasticity' => round($elasticity, 2)
            ];
        }
    }
    
    return $elasticityData;
}

// Fetch Current Unit Prices by Region
function getUnitPricesByRegion($conn) {
    $sql = "SELECT 
                r.district, 
                AVG(sd.unit_price) as avg_price
            FROM sale_details sd
            JOIN sale s ON sd.sale_id = s.sale_id
            JOIN retailer r ON s.retailer_id = r.retailer_id
            GROUP BY r.district";
    
    $result = $conn->query($sql);
    
    $regionData = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $regionData[] = $row;
        }
    }
    
    return $regionData;
}

// Get initial data
$salesData = getSalesData($conn);
$elasticityData = getElasticityData($conn);
$regionData = getUnitPricesByRegion($conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricultural Officer Dashboard</title>
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

    h1 {
        font-size: 2.5rem;
        color: #2c3e50;
        margin-bottom: 10px;
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

    .chart-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .chart-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    canvas {
        width: 100% !important;
        height: 300px !important;
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
        <li><a href="agriOficer.php">Crop Information</a></li>
        <li><a href="officer_view_production_list.php">Crop Production Report</a></li>
        <li><a href="Recomendation_form.php">Recommendations</a></li>
        <li id="Logout"><a href="index.php?logout='1'">Logout</a></li>
    </ul>
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <section class="container">
        <h1>Welcome, Agricultural Officer</h1>
        <p>Assist farmers and provide agricultural recommendations.</p>

        <div class="chart-container">
            <div class="chart-box">
                <h3>Consumer Demand Over Time</h3>
                <canvas id="consumptionChart"></canvas>
            </div>

            <div class="chart-box">
                <h3>Price Elasticity by Product</h3>
                <canvas id="elasticityChart"></canvas>
            </div>

            <div class="chart-box">
                <h3>Price Trends Over Time</h3>
                <canvas id="priceTrendChart"></canvas>
            </div>

            <div class="chart-box">
                <h3>Current Unit Prices by Region</h3>
                <canvas id="regionPriceChart"></canvas>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Agricultural Officer Dashboard | GreenHarvest</p>
    </footer>
  </div>

  <script>
    // Prepare data
    var salesData = <?php echo json_encode($salesData); ?>;
    var salesLabels = salesData.map(item => item.date);
    var salesQuantities = salesData.map(item => item.quantity);

    var elasticityData = <?php echo json_encode($elasticityData); ?>;
    var elasticityLabels = elasticityData.map(item => item.name);
    var elasticityValues = elasticityData.map(item => item.elasticity);

    var priceTrendsLabels = salesData.map(item => item.date);
    var priceTrendsPrices = salesData.map(item => parseFloat(item.price));

    var regionData = <?php echo json_encode($regionData); ?>;
    var regionLabels = regionData.map(item => item.district);
    var regionPrices = regionData.map(item => parseFloat(item.avg_price));

    // Create the Consumer Demand Chart
    var ctx1 = document.getElementById('consumptionChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Quantity Sold',
                data: salesQuantities,
                backgroundColor: 'rgba(0, 123, 255, 0.5)',
                borderColor: 'blue',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Product Consumption Over Time'
                }
            }
        }
    });

    // Create the Price Elasticity Chart
    var ctx2 = document.getElementById('elasticityChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: elasticityLabels,
            datasets: [{
                label: 'Price Elasticity',
                data: elasticityValues,
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Price Elasticity by Product'
                }
            }
        }
    });

    // Create the Price Trends Over Time Chart
    var ctx3 = document.getElementById('priceTrendChart').getContext('2d');
    new Chart(ctx3, {
        type: 'line',
        data: {
            labels: priceTrendsLabels,
            datasets: [{
                label: 'Unit Price Over Time',
                data: priceTrendsPrices,
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

    // Create the Unit Prices by Region Chart
    var ctx4 = document.getElementById('regionPriceChart').getContext('2d');
    new Chart(ctx4, {
        type: 'bar',
        data: {
            labels: regionLabels,
            datasets: [{
                label: 'Avg Price by Region',
                data: regionPrices,
                backgroundColor: '#10b981'
            }]
        },
        options: {
            plugins: { title: { display: true, text: 'Current Unit Prices by Region' }},
            scales: {
                x: { title: { display: true, text: 'District' }},
                y: { title: { display: true, text: 'Avg Price' }}
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