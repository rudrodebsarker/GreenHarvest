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

// Check if the table exists, if not create it
$tableCheckNew = $conn->query("SHOW TABLES LIKE 'farmer'");
if ($tableCheckNew->num_rows == 0) {
    $sql = "CREATE TABLE IF NOT EXISTS farmer (
        farmer_id VARCHAR(50) PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        house VARCHAR(100),
        road VARCHAR(100),
        area VARCHAR(100),
        district VARCHAR(100),
        country VARCHAR(100),
        years_of_experience INT,
        weather_id VARCHAR(50)
    )";
    if (!$conn->query($sql)) {
        die("Error creating table: " . $conn->error);
    }
}

// Check for all required columns and add them if missing
$houseCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'house'");
$roadCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'road'");
$areaCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'area'");
$districtCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'district'");
$countryCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'country'");
$weatherIdCheck = $conn->query("SHOW COLUMNS FROM farmer LIKE 'weather_id'");

// Check if house column exists and add it if not
if ($houseCheck->num_rows == 0) {
    $addHouseColumn = $conn->query("ALTER TABLE farmer ADD COLUMN house VARCHAR(100) DEFAULT '' AFTER name");
    if (!$addHouseColumn) {
        echo "<script>console.error('Warning: Could not add house column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing house column');</script>";
    }
}

// Check if road column exists and add it if not
if ($roadCheck->num_rows == 0) {
    $addRoadColumn = $conn->query("ALTER TABLE farmer ADD COLUMN road VARCHAR(100) DEFAULT '' AFTER house");
    if (!$addRoadColumn) {
        echo "<script>console.error('Warning: Could not add road column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing road column');</script>";
    }
}

// Check if area column exists and add it if not
if ($areaCheck->num_rows == 0) {
    $addAreaColumn = $conn->query("ALTER TABLE farmer ADD COLUMN area VARCHAR(100) DEFAULT '' AFTER road");
    if (!$addAreaColumn) {
        echo "<script>console.error('Warning: Could not add area column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing area column');</script>";
    }
}

// Check if district column exists and add it if not
if ($districtCheck->num_rows == 0) {
    $addDistrictColumn = $conn->query("ALTER TABLE farmer ADD COLUMN district VARCHAR(100) DEFAULT '' AFTER area");
    if (!$addDistrictColumn) {
        echo "<script>console.error('Warning: Could not add district column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing district column');</script>";
    }
}

// Check if country column exists and add it if not
if ($countryCheck->num_rows == 0) {
    $addCountryColumn = $conn->query("ALTER TABLE farmer ADD COLUMN country VARCHAR(100) DEFAULT '' AFTER district");
    if (!$addCountryColumn) {
        echo "<script>console.error('Warning: Could not add country column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing country column');</script>";
    }
}

// Check if weather_id column exists and add it if not
if ($weatherIdCheck->num_rows == 0) {
    $addWeatherIdColumn = $conn->query("ALTER TABLE farmer ADD COLUMN weather_id VARCHAR(50) DEFAULT '' AFTER years_of_experience");
    if (!$addWeatherIdColumn) {
        echo "<script>console.error('Warning: Could not add weather_id column: " . $conn->error . "');</script>";
    } else {
        echo "<script>console.log('Added missing weather_id column');</script>";
    }
}

// Handle form submission for adding or updating farmer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update_farmer']) && isset($_POST['edit_id'])) {
        // Update existing farmer
        $farmer_id = $conn->real_escape_string($_POST['farmerId']);
        $name = $conn->real_escape_string($_POST['name']);
        $house = $conn->real_escape_string($_POST['house']);
        $road = $conn->real_escape_string($_POST['road']);
        $area = $conn->real_escape_string($_POST['area']);
        $district = $conn->real_escape_string($_POST['district']);
        $country = $conn->real_escape_string($_POST['country']);
        $experience = intval($_POST['experience']);
        $weather_id = $conn->real_escape_string($_POST['weatherId']);
        $edit_id = $conn->real_escape_string($_POST['edit_id']);

        // Build dynamic UPDATE query based on existing columns
        $updates = array();
        
        if ($farmer_id) {
            $updates[] = "farmer_id = '$farmer_id'";
        }
        $updates[] = "name = '$name'";
        
        // Check if other columns exist before including them
        $houseExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'house'")->num_rows > 0;
        if ($houseExists) {
            $updates[] = "house = '$house'";
        }
        
        $roadExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'road'")->num_rows > 0;
        if ($roadExists) {
            $updates[] = "road = '$road'";
        }
        
        $areaExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'area'")->num_rows > 0;
        if ($areaExists) {
            $updates[] = "area = '$area'";
        }
        
        $districtExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'district'")->num_rows > 0;
        if ($districtExists) {
            $updates[] = "district = '$district'";
        }
        
        $countryExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'country'")->num_rows > 0;
        if ($countryExists) {
            $updates[] = "country = '$country'";
        }
        
        $expExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'years_of_experience'")->num_rows > 0;
        if ($expExists) {
            $updates[] = "years_of_experience = $experience";
        }
        
        $weatherExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'weather_id'")->num_rows > 0;
        if ($weatherExists && !empty($weather_id)) {
            // Check if the weather_id exists in weather_info table
            $weatherCheck = $conn->query("SELECT weather_id FROM weather_info WHERE weather_id = '$weather_id'");
            if ($weatherCheck && $weatherCheck->num_rows > 0) {
                $updates[] = "weather_id = '$weather_id'";
            } else {
                echo "<script>alert('Warning: The specified Weather ID does not exist in the system. Weather ID will not be updated.');</script>";
            }
        } else if ($weatherExists && empty($weather_id)) {
            $updates[] = "weather_id = NULL";
        }
        
        $sql = "UPDATE farmer SET " . implode(", ", $updates) . " WHERE farmer_id = '$edit_id'";

        if ($conn->query($sql)) {
            echo "<script>alert('Farmer updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating farmer: " . $conn->error . "');</script>";
        }
    } else {
        // Add new farmer
        $farmer_id = isset($_POST['farmerId']) ? $conn->real_escape_string($_POST['farmerId']) : null;
        $name = $conn->real_escape_string($_POST['name']);
        $house = $conn->real_escape_string($_POST['house']);
        $road = $conn->real_escape_string($_POST['road']);
        $area = $conn->real_escape_string($_POST['area']);
        $district = $conn->real_escape_string($_POST['district']);
        $country = $conn->real_escape_string($_POST['country']);
        $experience = intval($_POST['experience']);
        $weather_id = isset($_POST['weatherId']) ? $conn->real_escape_string($_POST['weatherId']) : '';

        // Check if farmer ID already exists if provided
        if ($farmer_id) {
            $check = $conn->query("SELECT farmer_id FROM farmer WHERE farmer_id = '$farmer_id'");
            if ($check->num_rows > 0) {
                echo "<script>alert('Farmer ID already exists!');</script>";
                $proceed = false;
            } else {
                $proceed = true;
            }
        } else {
            $proceed = true;
        }

        if ($proceed) {
            // Construct a dynamic INSERT query based on existing columns
            $columns = array();
            $values = array();
            
            // Include ID if provided
            if ($farmer_id) {
                $columns[] = "farmer_id";
                $values[] = "'$farmer_id'";
            }
            
            // Always include these required fields
            $columns[] = "name";
            $values[] = "'$name'";
            
            // Check if other columns exist before including them
            $houseExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'house'")->num_rows > 0;
            if ($houseExists) {
                $columns[] = "house";
                $values[] = "'$house'";
            }
            
            $roadExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'road'")->num_rows > 0;
            if ($roadExists) {
                $columns[] = "road";
                $values[] = "'$road'";
            }
            
            $areaExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'area'")->num_rows > 0;
            if ($areaExists) {
                $columns[] = "area";
                $values[] = "'$area'";
            }
            
            $districtExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'district'")->num_rows > 0;
            if ($districtExists) {
                $columns[] = "district";
                $values[] = "'$district'";
            }
            
            $countryExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'country'")->num_rows > 0;
            if ($countryExists) {
                $columns[] = "country";
                $values[] = "'$country'";
            }
            
            $expExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'years_of_experience'")->num_rows > 0;
            if ($expExists) {
                $columns[] = "years_of_experience";
                $values[] = $experience;
            }
            
            $weatherExists = $conn->query("SHOW COLUMNS FROM farmer LIKE 'weather_id'")->num_rows > 0;
            if ($weatherExists && !empty($weather_id)) {
                // Check if the weather_id exists in weather_info table
                $weatherCheck = $conn->query("SELECT weather_id FROM weather_info WHERE weather_id = '$weather_id'");
                if ($weatherCheck && $weatherCheck->num_rows > 0) {
                    $columns[] = "weather_id";
                    $values[] = "'$weather_id'";
                } else {
                    echo "<script>alert('Warning: The specified Weather ID does not exist in the system. Weather ID will not be saved.');</script>";
                }
            }
            
            $sql = "INSERT INTO farmer (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ")";
            
            if ($conn->query($sql)) {
                echo "<script>alert('Farmer added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding farmer: " . $conn->error . "');</script>";
            }
        }
    }
}

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM farmer WHERE farmer_id = '$delete_id'";
    if ($conn->query($sql)) {
        echo "<script>alert('Farmer deleted successfully!');</script>";
        echo "<script>window.location.href='Farmer_list.php';</script>";
    } else {
        echo "<script>alert('Error deleting farmer: " . $conn->error . "');</script>";
    }
}

// Set up edit mode if edit_id is provided
$editMode = false;
$editData = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $conn->real_escape_string($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM farmer WHERE farmer_id = '$edit_id'");
    if ($result->num_rows > 0) {
        $editData = $result->fetch_assoc();
        $editMode = true;
    }
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
    <title>Farmer Management System</title>
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

        .form-container {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
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

        button {
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
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        input {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
        }

        .decorative-icons {
            position: absolute;
            opacity: 0.1;
            font-size: 100px;
            color: white;
        }
        
        .nav-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }
        
        .next-page-btn {
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
        
        .next-page-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        select {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero-section">
            <i class="fas fa-tractor decorative-icons icon-left"></i>
            <h1>🌱 Farmer Management System</h1>
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

        <div class="form-container">
            <form method="POST" action="">
                <?php if ($editMode): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editData['farmer_id']; ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="input-group">
                        <label><i class="fas fa-id-card"></i>Farmer ID</label>
                        <input type="text" name="farmerId" value="<?php echo $editMode ? $editData['farmer_id'] : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-user"></i>Full Name</label>
                        <input type="text" name="name" value="<?php echo $editMode ? $editData['name'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-home"></i>House</label>
                        <input type="text" name="house" value="<?php echo $editMode ? $editData['house'] : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-road"></i>Road</label>
                        <input type="text" name="road" value="<?php echo $editMode ? $editData['road'] : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-globe"></i>Area</label>
                        <input type="text" name="area" value="<?php echo $editMode ? $editData['area'] : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-map-marked"></i>District</label>
                        <input type="text" name="district" value="<?php echo $editMode ? $editData['district'] : ''; ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-flag"></i>Country</label>
                        <input type="text" name="country" value="<?php echo $editMode ? $editData['country'] : 'Bangladesh'; ?>">
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-clock"></i>Experience (Years)</label>
                        <input type="number" name="experience" value="<?php echo $editMode ? $editData['years_of_experience'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-cloud-sun"></i>Weather ID</label>
                        <input type="text" name="weatherId" value="<?php echo $editMode ? $editData['weather_id'] : ''; ?>" placeholder="Enter a valid Weather ID">
                    </div>
                </div>
                <div class="btn-container">
                    <?php if ($editMode): ?>
                        <button type="submit" name="update_farmer"><i class="fas fa-edit"></i>Update Farmer</button>
                    <?php else: ?>
                        <button type="submit"><i class="fas fa-user-plus"></i>Submit Farmer Data</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="nav-buttons">
            <a href="Farmer_list.php" class="next-page-btn"><i class="fas fa-arrow-right"></i> Next Page</a>
        </div>
    </div>
</body>
</html>
