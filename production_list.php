<?php
// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'agriculture'); // Ensure the correct database credentials

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a search term is provided
$search = '';
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// SQL query to join production_data with agri_product, farmer, and agri_officer tables
$query = "SELECT pd.production_id, pd.yield, pd.acreage, pd.cost, pd.per_acre_seeds_requirement, 
                 pd.seeding_date, pd.harvesting_date, pd.data_input_date, 
                 ap.product_id, ap.name AS product_name,  -- Fetch product name from agri_product
                 f.farmer_id, f.name AS farmer_name
          FROM production_data pd
          LEFT JOIN agri_product ap ON pd.product_id = ap.product_id
          LEFT JOIN farmer f ON pd.farmer_id = f.farmer_id
          WHERE pd.production_id LIKE '%$search%'"; // Filter based on search

// Execute the query
$result = $conn->query($query);

// Check for errors in the SQL query
if ($result === FALSE) {
    die("Error: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Production List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f4f8;
            margin: 0;
            padding: 20px;
        }

        .form-container {
            background-color: #ffffff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1200px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ccc;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f4f4f4;
        }

        td a {
            margin-right: 10px;
            text-decoration: none;
            color: #4CAF50;
        }

        td a:hover {
            text-decoration: underline;
        }

        /* Styling for the buttons */
        .button-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-input {
            padding: 8px;
            width: 200px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .button {
            padding: 8px 16px;
            border-radius: 5px;
            background-color: #2980b9;
            color: white;
            border: none;
            cursor: pointer;
            margin-left: 10px; /* Add space between the search button and the add button */
        }

        .button:hover {
            background-color: #1d6a8b;
        }


           /* Styling for the back button */
        .back-button {
            padding: 8px 16px;
            border-radius: 5px;
            background-color: #e74c3c;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
        }



          .back-button:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>


<div class="form-container">
    <h2>Production Data List</h2>

    <!-- Search and Add Production Buttons -->
    <div class="button-container">
        <form method="get" action="">
            <input type="text" name="search" class="search-input" placeholder="Search by Production ID" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="button">Search</button>
        </form>
        <!-- Add Production Data Button as a button element -->
        <form action="production_form.php" method="get">
            <button type="submit" class="button">Add Production Data</button>
        </form>
    </div>

     <table>
        <tr>
            <th>Production ID</th>
            <th>Product Name</th> <!-- Added Product Name column -->
            <th>Product ID</th>
            <th>Farmer ID</th>
            <th>Farmer Name</th>
            <th>Yield</th>
            <th>Acreage</th>
            <th>Cost</th>
            <th>Per Acre Seeds Requirement</th>
            <th>Seeding Date</th>
            <th>Harvesting Date</th>
            <th>Data Input Date</th>
            <th>Actions</th>
        </tr>


        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['production_id']; ?></td>
            <td><?php echo $row['product_name']; ?></td> <!-- Display Product Name -->
            <td><?php echo $row['product_id']; ?></td>
            <td><?php echo $row['farmer_id']; ?></td>
            <td><?php echo $row['farmer_name']; ?></td>
            <td><?php echo $row['yield']; ?></td>
            <td><?php echo $row['acreage']; ?></td>
            <td><?php echo $row['cost']; ?></td>
            <td><?php echo $row['per_acre_seeds_requirement']; ?></td>
            <td><?php echo $row['seeding_date']; ?></td>
            <td><?php echo $row['harvesting_date']; ?></td>
            <td><?php echo $row['data_input_date']; ?></td>
            <td>

                <!-- Edit Button -->
                <a href="productionData_edit.php?id=<?php echo $row['production_id']; ?>">Edit</a>
                <!-- Delete Button -->
                <a href="productionData_delete.php?id=<?php echo $row['production_id']; ?>" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>


     <!-- Back Button -->
    <a href="admin_dashboard.php" class="back-button">Back to Admin Dashboard</a>
</div>

</body>
</html>

<?php
$conn->close();
?>