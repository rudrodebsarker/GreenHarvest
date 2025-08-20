<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Warehouse_manager') {
    $_SESSION['msg'] = "You must log in as Warehouse Manager first";
    header('location: login.php');
    exit();
}

$db = mysqli_connect('localhost', 'root', '', 'agriculture');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM WAREHOUSE";
$result = $db->query($sql);

$query_donut = "SELECT name, SUM(available_stock_of_product) AS total_stock FROM WAREHOUSE GROUP BY name";
$result_donut = mysqli_query($db, $query_donut);
$donut_data = [];
while ($row = mysqli_fetch_assoc($result_donut)) {
    $donut_data[] = $row;
}

$message = '';  // Default message variable

// Handle form submission to edit warehouse data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $warehouse_id = $_POST['warehouse_id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $contact_num = $_POST['contact_num'];
    $available_stock = $_POST['available_stock_of_product'];
    $last_updated = $_POST['last_updated'];

    // Update query to save the changes
    $update_sql = "UPDATE WAREHOUSE 
                   SET name='$name', location='$location', contact_num='$contact_num', available_stock_of_product='$available_stock', last_updated='$last_updated' 
                   WHERE warehouse_id='$warehouse_id'";

    if ($db->query($update_sql) === TRUE) {
        $message = "Record updated successfully.";  // Success message
    } else {
        $message = "Error updating record: " . $db->error;  // Error message
    }
}

// Close the connection
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Manager Dashboard</title>
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

    h2 {
      font-size: 1.8rem;
      color: #2c3e50;
      margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        overflow: hidden; /* For border-radius */
    }

    th, td {
        padding: 15px 20px;
        text-align: left;
        border-bottom: 1px solid #ecf0f1;
    }

    th {
        background-color: #34495e;
        color: #ecf0f1;
        font-weight: 600;
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
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        margin-bottom: 40px;
        height: 400px;
    }

    .form-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        margin-top: 40px;
    }

    .form-container input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .form-container button {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        border: none;
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
        <li><a href="inventory.php">Inventory</a></li>
        <li><a href="storage.php">Storage</a></li>
        <li><a href="w_Shipment_details.php">Shipped product info</a></li>
        <li><a href="w_shipment_date.php">Ship Date</a></li>
        <li><a href="M_Warehouse_management.php">Warehouse</a></li>
        <li id="Logout"><a href="index.php?logout='1'">Logout</a></li>
    </ul>
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
</nav>

<!-- Main Content -->
<div class="main-content">
    <section class="container">
        <h1>Welcome, Warehouse Manager</h1>
        <p>Manage warehouse data and monitor stock levels here.</p>

        <!-- Donut Chart Section -->
        <h2>Warehouse Stock Distribution</h2>
        <div class="chart-container">
            <canvas id="donutChart"></canvas>
        </div>

        <!-- Success Message -->
        <?php if (isset($message)) { echo "<p style='color: green;'>$message</p>"; } ?>

        <!-- Warehouse Table -->
        <h2 id="tableArea">Existing Warehouses</h2>
        <table>
            <thead>
                <tr>
                    <th>Warehouse ID</th>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Contact Number</th>
                    <th>Available Stock</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $db = mysqli_connect('localhost', 'root', '', 'agriculture');
                $sql = "SELECT * FROM WAREHOUSE";
                $result = $db->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['warehouse_id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['location']}</td>
                                <td>{$row['contact_num']}</td>
                                <td>{$row['available_stock_of_product']}</td>
                                <td>{$row['last_updated']}</td>
                                <td class='actions'>
                                    <a href='?edit_id={$row['warehouse_id']}#editForm'>Edit</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No warehouses available.</td></tr>";
                }
                mysqli_close($db);
                ?>
            </tbody>
        </table>
    </section>

    <!-- Edit Form Section -->
    <?php
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];

        $db = mysqli_connect('localhost', 'root', '', 'agriculture');
        $sql = "SELECT * FROM WAREHOUSE WHERE warehouse_id='$edit_id'";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        mysqli_close($db);
    ?>
        <!-- Edit Form -->
        <div id="editForm" class="form-container">
            <h2>Edit Warehouse Information</h2>
            <form method="POST" action="warehouse_manager_dashboard.php">
                <input type="hidden" name="warehouse_id" value="<?php echo $row['warehouse_id']; ?>">

                <label for="name">Warehouse Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $row['name']; ?>" required><br>

                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo $row['location']; ?>" required><br>

                <label for="contact_num">Contact Number:</label>
                <input type="text" id="contact_num" name="contact_num" value="<?php echo $row['contact_num']; ?>" required><br>

                <label for="available_stock_of_product">Available Stock:</label>
                <input type="number" id="available_stock_of_product" name="available_stock_of_product" value="<?php echo $row['available_stock_of_product']; ?>" required><br>

                <label for="last_updated">Last Updated:</label>
                <input type="date" id="last_updated" name="last_updated" value="<?php echo $row['last_updated']; ?>" required><br>

                <button type="submit" name="edit">Save Changes</button>
            </form>
        </div>
    <?php } ?>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Warehouse Manager Dashboard | AgriInsights</p>
    </footer>
</div>

<script>
    // Prepare Donut Chart Data
    const donutData = {
        labels: <?php echo json_encode(array_column($donut_data, 'name')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($donut_data, 'total_stock')); ?>,
            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50', '#FF9800'],
            borderWidth: 1
        }]
    };

    // Create Donut Chart
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: donutData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + ' units';
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