<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Farmer') {
    $_SESSION['msg'] = "You must log in as Farmer first";
    header('location: login.php');
    exit();
}

// Example dummy data (replace with actual DB queries)
$farmer_name = " Farmer Dashboard";
$crop_count = 5;
$sales_total = 8000;
$weather_station = "Dhaka Central";
$monthly_rainfall = "220 mm";

// Database connection
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch crop data for Pie chart (crop distribution)
$query_crop = "SELECT name, COUNT(*) AS count FROM agri_product GROUP BY name";
$result_crop = mysqli_query($db, $query_crop);
$crops = [];
while ($row = mysqli_fetch_assoc($result_crop)) {
    $crops[] = $row;
}

// Fetch production data for Bar chart (production statistics)
$query_production = "SELECT product_id, SUM(yield) AS total_yield FROM production_data GROUP BY product_id";
$result_production = mysqli_query($db, $query_production);
$production_data = [];
while ($row = mysqli_fetch_assoc($result_production)) {
    $production_data[] = $row;
}

mysqli_close($db);

$product_names = [];
$product_prices = [];
foreach ($crops as $product) {
    $product_names[] = $product['name'];
    $product_prices[] = $product['count'];
}

$production_labels = [];
$production_yields = [];
foreach ($production_data as $data) {
    $production_labels[] = $data['product_id'];  // You can modify this to fetch the actual product names
    $production_yields[] = $data['total_yield'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Farmer Dashboard</title>
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
        <li><a href="f_agriProduct_list.php">Crop Information</a></li>
        <li><a href="f_production_list.php">Production Data</a></li>
        <li><a href="wholesaleListTable.php">Buyer Info</a></li>
        <li><a href="f_product_shipment_details.php">Shipment Agri_Product</a></li>
        <li><a href="farmers_recommendation.php">View Recomendation</a></li>
        <li id="Logout"><a href="index.php?logout='1'">Logout</a></li>
    </ul>
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
      <section class="container">
        <h1>Welcome, <?php echo htmlspecialchars($farmer_name); ?></h1>
        <p>Monitor your crops and manage sales in the market.</p>

        <!-- Chart Section -->
        <div class="chart-container">
          <div class="chart-box">
            <canvas id="pieChart"></canvas>
          </div>
          <div class="chart-box">
            <canvas id="barChart"></canvas>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer>
        <p>&copy; 2025 Farmer Dashboard | GreenHarvest</p>
      </footer>
  </div>

  <script>
    // Prepare Pie Chart Data
    const pieData = {
      labels: <?php echo json_encode(array_column($crops, 'name')); ?>,
      datasets: [{
        data: <?php echo json_encode(array_column($crops, 'count')); ?>,
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800']
      }]
    };

    // Create Pie Chart
    new Chart(document.getElementById('pieChart'), {
      type: 'pie',
      data: pieData,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                return tooltipItem.label + ': ' + tooltipItem.raw + ' products';
              }
            }
          }
        }
      }
    });

    // Prepare Bar Chart Data (Production Yield)
    const barData = {
      labels: <?php echo json_encode($production_labels); ?>,
      datasets: [{
        label: 'Total Yield (kg)',
        data: <?php echo json_encode($production_yields); ?>,
        backgroundColor: '#36A2EB',
        borderColor: '#36A2EB',
        borderWidth: 1
      }]
    };

    // Create Bar Chart
    new Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: barData,
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        },
        plugins: {
          legend: {
            position: 'top',
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                return tooltipItem.label + ': ' + tooltipItem.raw + ' kg';
              }
            }
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