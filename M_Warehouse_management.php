<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 'Warehouse_manager') {
    $_SESSION['msg'] = "You must log in as Warehouse Manager first";
    header('location: login.php');
    exit();
}

$conn = mysqli_connect('localhost', 'root', '', 'agriculture');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = '';  

// Handle delete warehouse functionality
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $sql_delete = "DELETE FROM WAREHOUSE WHERE warehouse_id = '$delete_id'";
    if ($conn->query($sql_delete) === TRUE) {
        $message = "Warehouse deleted successfully.";
    } else {
        $message = "Error: " . $sql_delete . "<br>" . $conn->error;
    }
}

// Handle edit form submission 
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $warehouse_id = $_POST['warehouse_id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $contact_num = $_POST['contact_num'];
    $available_stock = $_POST['available_stock_of_product'];
    $last_updated = $_POST['last_updated'];

    // Update warehouse information
    $sql = "UPDATE WAREHOUSE SET name='$name', location='$location', contact_num='$contact_num', available_stock_of_product='$available_stock', last_updated='$last_updated' WHERE warehouse_id='$warehouse_id'";

    if ($conn->query($sql) === TRUE) {
        $message = "Warehouse updated successfully.";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle form submission for adding new warehouse
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $warehouse_id = $_POST['warehouse_id'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $contact_num = $_POST['contact_num'];
    $available_stock = $_POST['available_stock_of_product'];
    $last_updated = $_POST['last_updated'];

    // Ensure warehouse_id is unique
    $check_sql = "SELECT * FROM WAREHOUSE WHERE warehouse_id = '$warehouse_id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $message = "Warehouse ID already exists. Please use a unique Warehouse ID.";
    } else {
        // Insert new warehouse record
        $sql = "INSERT INTO WAREHOUSE (warehouse_id, name, location, contact_num, available_stock_of_product, last_updated) 
                VALUES ('$warehouse_id', '$name', '$location', '$contact_num', '$available_stock', '$last_updated')";
        
        if ($conn->query($sql) === TRUE) {
            $message = "New warehouse added successfully.";
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$sql = "SELECT * FROM WAREHOUSE";
$result = $conn->query($sql);

$query_donut = "SELECT name, SUM(available_stock_of_product) AS total_stock FROM WAREHOUSE GROUP BY name";
$result_donut = mysqli_query($conn, $query_donut);
$donut_data = [];
while ($row = mysqli_fetch_assoc($result_donut)) {
    $donut_data[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Management</title>
    <style>
        /* Reset & Global Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
            color: #333;
            padding: 0 20px;
        }

        h1 {
            text-align: center;
            background-color: #2980b9;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        h2 {
            color: #2980b9;
            padding-bottom: 10px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            max-width: 1200px;
        }

        /* Form container styling */
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }

        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        button {
            background-color: #2980b9;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #1d6a8b;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #2980b9;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Actions */
        .actions a {
            text-decoration: none;
            color: #2980b9;
            margin: 0 10px;
        }

        .actions a:hover {
            text-decoration: underline;
        }

      
        .scrollToForm {
            transition: all 0.5s ease-in-out;
        }

       
        .back-btn {
            padding: 10px 20px;
            background-color: #1d6a8b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background-color: #2980b9;
        }
    </style>
    <script>
    
        window.onload = function() {
            if (window.location.hash === "#editForm") {
                document.getElementById("editForm").scrollIntoView({ behavior: "smooth" });
            }
        };
    </script>
</head>
<body>

<div class="container">
    <!-- Back Button -->
    <a href="Warehouse_manager_dashboard.php" class="back-btn">Back to Dashboard</a>

    <h1>Warehouse Management</h1>

    <!-- Success/Error Message -->
    <?php if ($message) { echo "<p style='color: green;'>$message</p>"; } ?>

    <!-- Add New Warehouse Form -->
    <div class="form-container">
        <h2>Add New Warehouse</h2>
        <form method="POST" action="M_Warehouse_management.php">
            <label for="warehouse_id">Warehouse ID:</label>
            <input type="text" id="warehouse_id" name="warehouse_id" required><br>

            <label for="name">Warehouse Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required><br>

            <label for="contact_num">Contact Number:</label>
            <input type="text" id="contact_num" name="contact_num" required><br>

            <label for="available_stock_of_product">Available Stock:</label>
            <input type="number" id="available_stock_of_product" name="available_stock_of_product" required><br>

            <label for="last_updated">Last Updated:</label>
            <input type="date" id="last_updated" name="last_updated" required><br>

            <button type="submit" name="add">Add Warehouse</button>
        </form>
    </div>

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
                                <a href='?edit_id={$row['warehouse_id']}#editForm'>Edit</a> | 
                                <a href='?delete_id={$row['warehouse_id']}' onclick='return confirm(\"Are you sure you want to delete?\")'>Delete</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No warehouses available.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Edit form -->
    <?php
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $sql = "SELECT * FROM WAREHOUSE WHERE warehouse_id='$edit_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
    ?>
        <div id="editForm" class="form-container">
            <h2>Edit Warehouse Information</h2>
            <form method="POST" action="M_Warehouse_management.php">
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
</div>

</body>
</html>

<?php
$conn->close();
?>
