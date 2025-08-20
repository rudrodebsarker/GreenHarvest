<?php 
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM retailer WHERE retailer_id = '$delete_id'";
    if ($conn->query($sql)) {
        echo "<script>alert('Retailer deleted successfully!');</script>";
        echo "<script>window.location.href='Retailer_list.php';</script>";
    } else {
        echo "<script>alert('Error deleting retailer: " . $conn->error . "');</script>";
    }
}

// Filter functionality
$filterQuery = "";
if(isset($_GET['filter_id']) && !empty($_GET['filter_id'])) {
    $filter_id = $conn->real_escape_string($_GET['filter_id']);
    $filterQuery = " WHERE retailer_id = '$filter_id'";
}

// Calculate statistics
$totalRetailers = 0;
// First check if retailer table exists and count total records
$result = $conn->query("SELECT COUNT(*) as retailer_count FROM retailer");
if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
    $totalRetailers = $stats['retailer_count'];
    
    // Get the count of distinct districts
    $districtResult = $conn->query("SELECT COUNT(DISTINCT district) as district_count FROM retailer");
    if ($districtResult && $districtResult->num_rows > 0) {
        $districtStats = $districtResult->fetch_assoc();
        $activeCities = $districtStats['district_count'];
    } else {
        $activeCities = 0;
    }
} else {
    $totalRetailers = 0;
    $activeCities = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retailer Table</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 40px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .hero-section {
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(52, 152, 219, 0.8)),
                        url('https://images.unsplash.com/photo-1556740734-9f9ca6cbbb69?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 250px;
            border-radius: 15px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section h1 {
            color: white;
            font-size: 2.8em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            z-index: 2;
        }

        .stats-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 25px 0;
            display: flex;
            align-items: center;
            gap: 20px;
            border-left: 5px solid #3498db;
        }

        .stats-icon {
            font-size: 28px;
            background: #3498db;
            color: white;
            padding: 18px;
            border-radius: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        th {
            background: #3498db;
            color: white;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            background: #27ae60;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn {
            background: #e74c3c;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        button, .btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }

        button:hover, .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .decorative-icons {
            position: absolute;
            opacity: 0.1;
            font-size: 100px;
            color: white;
        }

        .filter-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .filter-input {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .filter-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
        }
        
        .filter-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .clear-filter {
            background: #f3f4f6;
            color: #4b5563;
            border: 1px solid #d1d5db;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .clear-filter:hover {
            background: #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="hero-section">
            <i class="fas fa-store decorative-icons icon-left"></i>
            <h1>🛒 Retailer Table</h1>
            <i class="fas fa-shopping-basket decorative-icons icon-right"></i>
        </div>

        <div class="stats-card">
            <i class="fas fa-chart-pie stats-icon"></i>
            <div>
                <h3>Retailer Statistics</h3>
                <p>Total Retailers: <span id="totalRetailers"><?php echo $totalRetailers; ?></span></p>
                <p>Active Cities: <span id="activeCities"><?php echo $activeCities; ?></span></p>
            </div>
        </div>

        <form class="filter-form" method="GET" action="">
            <input type="text" name="filter_id" placeholder="Filter by Retailer ID" class="filter-input" value="<?php echo isset($_GET['filter_id']) ? htmlspecialchars($_GET['filter_id']) : ''; ?>">
            <button type="submit" class="filter-btn"><i class="fas fa-filter"></i> Filter</button>
            <?php if(isset($_GET['filter_id']) && !empty($_GET['filter_id'])): ?>
                <a href="Retailer_list.php" class="clear-filter"><i class="fas fa-times"></i> Clear</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM retailer" . $filterQuery . " ORDER BY retailer_id";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . htmlspecialchars($row['retailer_id']) . "</td>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['contact']) . "</td>
                            <td>" . (isset($row['road']) ? htmlspecialchars($row['road']) : '') . ", " . 
                                    (isset($row['area']) ? htmlspecialchars($row['area']) : '') . ", " . 
                                    (isset($row['district']) ? htmlspecialchars($row['district']) : '') . ", " . 
                                    (isset($row['country']) ? htmlspecialchars($row['country']) : '') . "</td>
                            <td>
                                <div class='action-buttons'>
                                    <a href='Retailer.php?edit_id=" . $row['retailer_id'] . "' class='edit-btn'>
                                        <i class='fas fa-edit'></i>Edit
                                    </a>
                                    <a href='javascript:void(0)' class='delete-btn' 
                                       onclick='deleteRetailer(" . $row['retailer_id'] . ")'>
                                        <i class='fas fa-trash'></i>Delete
                                    </a>
                                </div>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>

        <div class="nav-buttons">
            <a href="Retailer.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Form</a>
            <a href="index.php" class="btn"><i class="fas fa-home"></i> Home</a>
        </div>
    </div>

    <script>
    function deleteRetailer(id) {
        if(confirm('Are you sure you want to delete this retailer?')) {
            window.location.href = 'Retailer_list.php?delete_id=' + id;
        }
    }
    </script>
</body>
</html>
