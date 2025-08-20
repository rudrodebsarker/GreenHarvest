<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'agriculture'); // Ensure the correct database credentials

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the existing data for the production record
    $query = "SELECT * FROM production_data WHERE production_id = $id";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
} else {
    echo "No ID provided!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated data from the form
    $production_id = $_POST['production_id'];
    $yield = $_POST['yield'];
    $acreage = $_POST['acreage'];
    $cost = $_POST['cost'];
    $seeding_date = $_POST['seeding_date'];
    $harvesting_date = $_POST['harvesting_date'];
    $data_input_date = $_POST['data_input_date'];
    $per_acre_seeds_requirement = $_POST['per_acre_seeds_requirement']; // Added per acre seeds requirement

    // Update the production record in the database
    $update_query = "UPDATE production_data SET 
                        yield = '$yield',
                        acreage = '$acreage',
                        cost = '$cost',
                        seeding_date = '$seeding_date',
                        harvesting_date = '$harvesting_date',
                        data_input_date = '$data_input_date',
                        per_acre_seeds_requirement = '$per_acre_seeds_requirement'
                    WHERE production_id = $production_id"; // Using the production_id to update

    if ($conn->query($update_query) === TRUE) {
        echo "<script>alert('Record updated successfully!'); window.location.href='production_list.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Production Data</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Production Data</h2>

    <form method="POST">
        <!-- Hidden field for production_id -->
        <input type="hidden" name="production_id" value="<?php echo $row['production_id']; ?>">

        <label for="yield">Yield (kg):</label>
        <input type="number" step="0.01" name="yield" value="<?php echo $row['yield']; ?>" required><br>

        <label for="acreage">Acreage (hectares):</label>
        <input type="number" step="0.01" name="acreage" value="<?php echo $row['acreage']; ?>" required><br>

        <label for="cost">Cost ($):</label>
        <input type="number" step="0.01" name="cost" value="<?php echo $row['cost']; ?>" required><br>

        <label for="per_acre_seeds_requirement">Per Acre Seeds Requirement (kg):</label>
        <input type="number" step="0.01" name="per_acre_seeds_requirement" value="<?php echo $row['per_acre_seeds_requirement']; ?>" required><br>

        <label for="seeding_date">Seeding Date:</label>
        <input type="date" name="seeding_date" value="<?php echo $row['seeding_date']; ?>" required><br>

        <label for="harvesting_date">Harvesting Date:</label>
        <input type="date" name="harvesting_date" value="<?php echo $row['harvesting_date']; ?>" required><br>

        <label for="data_input_date">Data Input Date:</label>
        <input type="date" name="data_input_date" value="<?php echo $row['data_input_date']; ?>" required><br>

        <input type="submit" value="Update">
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
