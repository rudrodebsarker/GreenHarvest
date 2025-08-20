<?php
  session_start(); 

  if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
  }
  if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: login.php");
  }

  // Include database connection
  include 'db_config.php';

  // SQL query to join production_data and agri_product table using product_id
  $sql = "SELECT ap.name AS product_name, pd.yield, pd.acreage, pd.cost, ap.seasonality, ap.type
          FROM production_data pd
          INNER JOIN agri_product ap ON pd.product_id = ap.product_id";
  $result = $conn->query($sql);
  $products = [];
  while($row = $result->fetch_assoc()) {
    $products[] = $row;
  }

  // For chart data
  $product_names = [];
  $product_prices = [];
  foreach ($products as $product) {
    $product_names[] = $product['product_name'];
    $product_prices[] = $product['cost'];
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - GreenHarvest</title>
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

    /* Dropdown Menu Styling */
    .dropdown-menu {
      position: static;
      display: none; /* Hidden by default */
      background-color: rgba(0, 0, 0, 0.25);
      box-shadow: none;
      border-radius: 0;
      width: 100%;
    }

    .dropdown:hover .dropdown-menu {
      display: block; /* Show on hover */
    }

    .dropdown-menu a {
      padding: 14px 30px 14px 45px; /* Indented dropdown links */
      font-size: 0.95rem;
      color: #bdc3c7;
      border-left: none;
    }

    .dropdown-menu a:hover {
      background: #3498db;
      color: #fff;
      padding-left: 50px;
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

    /* Homepage Section */
    .home-section {
      text-align: center;
      padding: 50px 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      margin-bottom: 40px;
    }

    .home-section h1 {
      font-size: 2.5rem;
      color: #2c3e50;
      margin-bottom: 10px;
    }

    .home-section p {
      font-size: 1.1rem;
      color: #7f8c8d;
    }

    /* Chart Container */
    .chart-container {
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      margin-bottom: 40px;
      height: 400px;
    }

    /* Table Styling */
    h2 {
      font-size: 1.8rem;
      color: #2c3e50;
      margin-bottom: 20px;
    }

    #productTable {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.05);
      overflow: hidden; /* For border-radius */
    }

    #productTable th, #productTable td {
      padding: 15px 20px;
      text-align: left;
      border-bottom: 1px solid #ecf0f1;
    }

    #productTable thead {
      background-color: #34495e;
      color: #ecf0f1;
      font-weight: 600;
    }

    #productTable tbody tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    #productTable tbody tr:hover {
      background-color: #f0f5f9;
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
      <li class="dropdown">
          <a href="#">Agri Officer</a>
          <ul class="dropdown-menu">
            <li><a href="agriOficers_List.php">Agriofficer's List</a></li>
            <li><a href="farmers_recommendation.php">Agriofficer's Recomendation</a></li> 
          </ul>
        </li>
        <li class="dropdown">
          <a href="#">Product</a>
          <ul class="dropdown-menu">
            <li><a href="agriProduct_list.php">Agri-Product Info</a></li>
            <li><a href="production_list.php">Production Info</a></li> 
          </ul>
        </li>
        <li class="dropdown">
            <a href="#">Sales & Market Price</a>
            <ul class="dropdown-menu">
              <li><a href="sale_information.php">Sale Info</a></li>
              <li><a href="price_trends_A.php">Price Trends</a></li>
              <li><a href="track_agri_traders.php">Track Agri_traders</a></li>
              <li><a href="cd_pe.php">Demand & Price Elasticity</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#">Buyer Seller Directories</a>
            <ul class="dropdown-menu">
              <li><a href="Farmer_list.php">Farmer</a></li>
              <li><a href="WholeSaler_list.php">Wholesaler</a></li>
              <li><a href="Retailer_list.php">Retailer</a></li>
              <li><a href="Consumer_list.php">Consumer</a></li>
            </ul>
        </li>
        <li class="dropdown">
            <a href="#">Supply Level</a>
            <ul class="dropdown-menu">
              <li><a href="inventory.php">Inventory</a></li>
              <li><a href="storage.php">Storage Status</a></li>
              <li><a href="shipment.php">Shipment</a></li>
              <li><a href="warehouse_management.php">Warehouse</a></li>
              <li><a href="logistics.php">Logistics Tracking</a></li>
            </ul>
        </li>
        <li id="Logout"><a href="index.php?logout='1'">Logout</a></li>
    </ul>
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Homepage Content -->
    <section class="home-section">
      <div class="container">
        <h1>Welcome to Demand & Supply Analysis for Agricultural Products</h1>
        <p>Analyze the dynamics of agricultural products and make informed decisions!</p>
      </div>
    </section>

    <!-- Bar Chart -->
    <div class="chart-container">
      <canvas id="productChart"></canvas>
    </div>

    <!-- Product Table -->
    <h2>Product Information</h2>
    <table id="productTable">
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Yield</th>
          <th>Acreage</th>
          <th>Price</th>
          <th>Seasonality</th>
          <th>Type</th>
        </tr>
      </thead>
      <tbody>
        <?php
        foreach ($products as $product) {
          echo "<tr>
                  <td>{$product['product_name']}</td>
                  <td>{$product['yield']}</td>
                  <td>{$product['acreage']}</td>
                  <td>{$product['cost']}</td>
                  <td>{$product['seasonality']}</td>
                  <td>{$product['type']}</td>
                </tr>";
        }
        ?>
      </tbody>
    </table>

    <footer>
      <p>&copy; 2025 Demand & Supply Analysis for Agricultural Products</p>
    </footer>
  </div>

  <script>
    // Data for the chart
    const labels = <?php echo json_encode($product_names); ?>;
    const data = <?php echo json_encode($product_prices); ?>;

    // Create chart
    const ctx = document.getElementById('productChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Product Prices',
                data: data,
                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                borderColor: 'rgba(41, 128, 185, 1)',
                borderWidth: 1,
                hoverBackgroundColor: 'rgba(41, 128, 185, 0.9)',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                      color: '#ecf0f1'
                    }
                },
                x: {
                  grid: {
                    display: false
                  }
                }
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                backgroundColor: '#2c3e50',
                titleFont: {
                  size: 14,
                  weight: '600'
                },
                bodyFont: {
                  size: 12
                },
                padding: 10,
                cornerRadius: 5
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
