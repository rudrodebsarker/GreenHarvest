<?php
// ------------------------
// Database Connection
// ------------------------
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// ------------------------
// Table Rename & Migration
// ------------------------
$tableOld = $conn->query("SHOW TABLES LIKE 'wholeseller'");
$tableNew = $conn->query("SHOW TABLES LIKE 'wholesaler'");

if ($tableOld->num_rows > 0 && $tableNew->num_rows > 0) {
    $conn->query("DROP TABLE wholeseller");
} elseif ($tableOld->num_rows > 0) {
    $conn->query("RENAME TABLE wholeseller TO wholesaler");
}

// ------------------------
// Schema Update
// ------------------------
function columnExists($conn, $table, $column) {
    return $conn->query("SHOW COLUMNS FROM $table LIKE '$column'")->num_rows > 0;
}

if (columnExists($conn, 'wholesaler', 'city')) {
    if (!columnExists($conn, 'wholesaler', 'district')) {
        $conn->query("ALTER TABLE wholesaler CHANGE city district VARCHAR(100) NOT NULL");
    } else {
        $conn->query("ALTER TABLE wholesaler DROP COLUMN city");
    }
}

$columnsToAdd = [
    'house' => "AFTER contact",
    'road' => "AFTER house",
    'area' => "AFTER road",
    'country' => "AFTER district"
];

foreach ($columnsToAdd as $col => $position) {
    if (!columnExists($conn, 'wholesaler', $col)) {
        $conn->query("ALTER TABLE wholesaler ADD $col VARCHAR(100) NOT NULL DEFAULT '' $position");
    }
}

if ($tableNew->num_rows == 0) {
    $conn->query("CREATE TABLE wholesaler (
        wholesaler_id VARCHAR(50) PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        contact VARCHAR(50) NOT NULL,
        house VARCHAR(100) NOT NULL,
        road VARCHAR(100) NOT NULL,
        area VARCHAR(100) NOT NULL,
        district VARCHAR(100) NOT NULL,
        country VARCHAR(100) NOT NULL
    )");
}

// ------------------------
// Form Submission Handling
// ------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $conn->real_escape_string($_POST['wholesalerId']);
    $name = $conn->real_escape_string($_POST['name']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $house = $conn->real_escape_string($_POST['house']);
    $road = $conn->real_escape_string($_POST['road']);
    $area = $conn->real_escape_string($_POST['area']);
    $district = $conn->real_escape_string($_POST['city']);
    $country = $conn->real_escape_string($_POST['country']);

    if (isset($_POST['update_wholesaler']) && isset($_POST['edit_id'])) {
        $edit_id = $conn->real_escape_string($_POST['edit_id']);
        $sql = "UPDATE wholesaler SET 
            wholesaler_id = '$id',
            name = '$name',
            contact = '$contact',
            house = '$house',
            road = '$road',
            area = '$area',
            district = '$district',
            country = '$country' 
            WHERE wholesaler_id = '$edit_id'";
        $conn->query($sql);
    } else {
        $exists = $conn->query("SELECT * FROM wholesaler WHERE wholesaler_id = '$id'");
        if ($exists->num_rows == 0) {
            $sql = "INSERT INTO wholesaler (wholesaler_id, name, contact, house, road, area, district, country) VALUES 
            ('$id', '$name', '$contact', '$house', '$road', '$area', '$district', '$country')";
            $conn->query($sql);
        }
    }
}

// ------------------------
// Delete Operation
// ------------------------
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $conn->query("DELETE FROM wholesaler WHERE wholesaler_id = '$delete_id'");
    header("Location: Wholesaler_list.php");
    exit();
}

// ------------------------
// Edit Mode Setup
// ------------------------
$editMode = false;
$editData = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $conn->real_escape_string($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM wholesaler WHERE wholesaler_id = '$edit_id'");
    if ($result->num_rows > 0) {
        $editData = $result->fetch_assoc();
        $editMode = true;
    }
}

// ------------------------
// Dashboard Stats
// ------------------------
$totalWholesalers = 0;
$activeCities = 0;
$result = $conn->query("SELECT COUNT(*) AS count FROM wholesaler");
if ($result && $row = $result->fetch_assoc()) {
    $totalWholesalers = $row['count'];
    $cityResult = $conn->query("SELECT COUNT(DISTINCT district) AS count FROM wholesaler");
    $activeCities = ($cityResult && $cityResult->num_rows > 0) ? $cityResult->fetch_assoc()['count'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wholesaler Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #e0f7fa, #e8f5e9);
            margin: 0;
            padding: 0;
        }
        .dashboard {
            padding: 40px;
            max-width: 1200px;
            margin: auto;
        }
        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
        }
        .stats {
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        .stat {
            text-align: center;
        }
        .form-container {
            background: #f1f8e9;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.05);
        }
        input, button {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            width: 100%;
        }
        button {
            background-color: #43a047;
            color: white;
            border: none;
            cursor: pointer;
        }
        label {
            font-weight: 500;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h1>Wholesaler Management</h1>
        <div class="stats">
            <div class="stat">
                <h3>Total Wholesalers</h3>
                <p><?php echo $totalWholesalers; ?></p>
            </div>
            <div class="stat">
                <h3>Active Districts</h3>
                <p><?php echo $activeCities; ?></p>
            </div>
        </div>

        <div class="form-container">
            <form method="POST">
                <?php if ($editMode): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editData['wholesaler_id']; ?>">
                <?php endif; ?>
                <div class="grid">
                    <div>
                        <label>Wholesaler ID</label>
                        <input type="text" name="wholesalerId" value="<?php echo $editMode ? $editData['wholesaler_id'] : ''; ?>" required>
                    </div>
                    <div>
                        <label>Name</label>
                        <input type="text" name="name" value="<?php echo $editMode ? $editData['name'] : ''; ?>" required>
                    </div>
                    <div>
                        <label>Contact</label>
                        <input type="text" name="contact" value="<?php echo $editMode ? $editData['contact'] : ''; ?>" required>
                    </div>
                    <div>
                        <label>House</label>
                        <input type="text" name="house" value="<?php echo $editMode ? $editData['house'] : ''; ?>" required>
                    </div>
                    <div>
                        <label>Road</label>
                        <input type="text" name="road" value="<?php echo $editMode ? $editData['road'] : ''; ?>" required>
                    </div>
                    <div>
                        <label>Area</label>
                        <input type="text" name="area" value="<?php echo $editMode ? $editData['area'] : ''; ?>" required>
                    </div>
                    <div>
                        <label>District</label>
                        <input type="text" name="city" value="<?php echo $editMode ? $editData['district'] : ''; ?>" required>
                    </div>
                    <div>
                        <label>Country</label>
                        <input type="text" name="country" value="<?php echo $editMode ? $editData['country'] : ''; ?>" required>
                    </div>
                </div>
                <button type="submit" name="<?php echo $editMode ? 'update_wholesaler' : 'add_wholesaler'; ?>">
                    <?php echo $editMode ? 'Update Wholesaler' : 'Add Wholesaler'; ?>
                </button>
            </form>
        </div>
    </div>
</body>
</html>
