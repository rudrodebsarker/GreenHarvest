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

// Check for all required columns and add them if missing
$houseCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'house'");
$roadCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'road'");
$areaCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'area'");
$districtCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'district'");
$countryCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'country'");
$weatherIdCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'weather_id'");

// Add any missing columns (same as in Farmer.php)
if ($houseCheck->num_rows == 0) {
    $addHouseColumn = $conn->query("ALTER TABLE farmer ADD COLUMN house VARCHAR(100) DEFAULT '' AFTER name");
}
if ($roadCheck->num_rows == 0) {
    $addRoadColumn = $conn->query("ALTER TABLE farmer ADD COLUMN road VARCHAR(100) DEFAULT '' AFTER house");
}
if ($areaCheck->num_rows == 0) {
    $addAreaColumn = $conn->query("ALTER TABLE farmer ADD COLUMN area VARCHAR(100) DEFAULT '' AFTER road");
}
if ($districtCheck->num_rows == 0) {
    $addDistrictColumn = $conn->query("ALTER TABLE farmer ADD COLUMN district VARCHAR(100) DEFAULT '' AFTER area");
}
if ($countryCheck->num_rows == 0) {
    $addCountryColumn = $conn->query("ALTER TABLE farmer ADD COLUMN country VARCHAR(100) DEFAULT '' AFTER district");
}
if ($weatherIdCheck->num_rows == 0) {
    $addWeatherIdColumn = $conn->query("ALTER TABLE farmer ADD COLUMN weather_id VARCHAR(50) DEFAULT '' AFTER years_of_experience");
}

// Delete Farmer Logic
if(isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM farmer WHERE farmer_id = '$delete_id'";
    if($conn->query($sql)) {
        echo "<script>alert('Farmer deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting farmer: " . $conn->error . "');</script>";
    }
    echo "<script>window.location.href='Farmer_list.php';</script>";
    exit;
}

// Filter functionality
$filterQuery = "";
if(isset($_GET['filter_id']) && !empty($_GET['filter_id'])) {
    $filter_id = $conn->real_escape_string($_GET['filter_id']);
    $filterQuery = " WHERE farmer_id = '$filter_id'";
}

// Calculate statistics
$totalFarmers = 0;
$avgExperience = 0;

// First check if farmer table exists and count total records
$result = $conn->query("SELECT COUNT(*) as farmer_count FROM farmer");
if ($result && $result->num_rows > 0) {
    $stats = $result->fetch_assoc();
    $totalFarmers = $stats['farmer_count'];
    
    // Calculate average experience if there are any farmers
    if ($totalFarmers > 0) {
        $expResult = $conn->query("SELECT AVG(years_of_experience) as avg_exp FROM farmer");
        if ($expResult && $expResult->num_rows > 0) {
            $expStats = $expResult->fetch_assoc();
            $avgExperience = $expStats['avg_exp'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Table</title>
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
                        url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
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
            <i class="fas fa-tractor decorative-icons icon-left"></i>
            <h1>🌱 Farmer Table</h1>
            <i class="fas fa-seedling decorative-icons icon-right"></i>
        </div>

        <div class="stats-card">
            <i class="fas fa-chart-line stats-icon"></i>
            <div>
                <h3>Farmer Statistics</h3>
                <p>Total Farmers: <span id="totalFarmers"><?php echo $totalFarmers; ?></span></p>
                <p>Average Experience: <span id="avgExperience"><?php echo number_format($avgExperience, 1); ?></span> years</p>
            </div>
        </div>

        <form class="filter-form" method="GET" action="">
            <input type="text" name="filter_id" placeholder="Filter by Farmer ID" class="filter-input" value="<?php echo isset($_GET['filter_id']) ? htmlspecialchars($_GET['filter_id']) : ''; ?>">
            <button type="submit" class="filter-btn">Filter</button>
            <?php if(isset($_GET['filter_id']) && !empty($_GET['filter_id'])): ?>
                <a href="Farmer_list.php" class="clear-filter">Clear</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Experience</th>
                    <th>Weather ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM farmer" . $filterQuery . " ORDER BY farmer_id DESC";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0):
                    while($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['farmer_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td>
                        <?php 
                        // Build address parts dynamically
                        $addressParts = array();
                        if (isset($row['house']) && !empty($row['house'])) $addressParts[] = htmlspecialchars($row['house']);
                        if (isset($row['road']) && !empty($row['road'])) $addressParts[] = htmlspecialchars($row['road']);
                        if (isset($row['area']) && !empty($row['area'])) $addressParts[] = htmlspecialchars($row['area']);
                        if (isset($row['district']) && !empty($row['district'])) $addressParts[] = htmlspecialchars($row['district']);
                        if (isset($row['country']) && !empty($row['country'])) $addressParts[] = htmlspecialchars($row['country']);
                        
                        echo implode(", ", $addressParts);
                        ?>
                    </td>
                    <td><?= htmlspecialchars($row['years_of_experience']) ?> years</td>
                    <td><?= isset($row['weather_id']) ? htmlspecialchars($row['weather_id']) : '' ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="Farmer.php?edit_id=<?= $row['farmer_id'] ?>" class="edit-btn">
                                <i class="fas fa-edit"></i>Edit
                            </a>
                            <a href="javascript:void(0)" class="delete-btn" 
                               onclick="deleteFarmer('<?= $row['farmer_id'] ?>')">
                                <i class="fas fa-trash"></i>Delete
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="nav-buttons">
            <a href="index.php" class="btn"><i class="fas fa-home"></i> Home</a>
            <a href="Farmer.php" class="btn"><i class="fas fa-arrow-left"></i> Back to Form</a>
        </div>
    </div>
    
    <script>
    function deleteFarmer(id) {
        if(confirm('Are you sure you want to delete this farmer?')) {
            window.location.href = 'Farmer_list.php?delete_id=' + id;
        }
    }
    </script>
</body>
</html>
