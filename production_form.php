<?php
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'agriculture'); // Ensure the correct database credentials

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch farmer IDs
$farmer_query = "SELECT farmer_id FROM farmer"; 
$farmer_result = mysqli_query($conn, $farmer_query);

// Fetch officer IDs
$officer_query = "SELECT officer_id FROM agri_officer";
$officer_result = mysqli_query($conn, $officer_query);

// Fetch product IDs
$product_query = "SELECT product_id FROM agri_product";
$product_result = mysqli_query($conn, $product_query);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Production Data Entry</title>
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
            width: 700px;
            height: auto;
            max-height: 80vh;
            overflow-y: auto;
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

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-field {
            flex: 1 1 calc(50% - 20px);
            min-width: 220px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
        }

        input[type="submit"],
        .view-button {
            background-color: #5283e4;
            border: 1px solid #ccc;
            padding: 10px 20px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 8px;
        }

        input[type="submit"]:hover,
        .view-button:hover {
            background-color: #ddd;
        }

        /* For mobile responsiveness */
        @media (max-width: 768px) {
            .form-field {
                flex: 1 1 100%;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <!-- View Production List Button moved to top -->
        <button class="view-button" onclick="window.location.href='production_list.php';">View Production List</button>

        <h2>Enter Production Data</h2>
        <form id="productionForm" method="POST" action="insert_production.php">
            
            <!-- Production ID -->
            <label for="production_id">Production ID:</label>
            <input type="text" name="production_id" id="production_id" required>

            <div class="form-row">
                <div class="form-field">
                    <label for="yield">Yield (kg):</label>
                    <input type="number" step="0.01" name="yield" id="yield" required>
                </div>
                <div class="form-field">
                    <label for="acreage">Acreage (hectares):</label>
                    <input type="number" step="0.01" name="acreage" id="acreage" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="cost">Cost (TK):</label>
                    <input type="text" name="cost" id="cost" required>
                </div>
                <div class="form-field">
                    <label for="per_acre_seeds_requirement">Per Acre Seeds Requirement (kg):</label>
                    <input type="number" step="0.01" name="per_acre_seeds_requirement" id="per_acre_seeds_requirement" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="seeding_date">Seeding Date:</label>
                    <input type="date" name="seeding_date" id="seeding_date" required>
                </div>
                <div class="form-field">
                    <label for="harvesting_date">Harvesting Date:</label>
                    <input type="date" name="harvesting_date" id="harvesting_date" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="data_input_date">Data Input Date:</label>
                    <input type="date" name="data_input_date" id="data_input_date" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="farmer_id">Farmer ID:</label>
                    <select name="farmer_id" required>
                        <?php while ($row = mysqli_fetch_assoc($farmer_result)): ?>
                            <option value="<?php echo $row['farmer_id']; ?>"><?php echo $row['farmer_id']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label for="officer_id">Officer ID:</label>
                    <select name="officer_id" required>
                        <?php while ($row = mysqli_fetch_assoc($officer_result)): ?>
                            <option value="<?php echo $row['officer_id']; ?>"><?php echo $row['officer_id']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label for="product_id">Product ID:</label>
                    <select name="product_id" required>
                        <?php while ($row = mysqli_fetch_assoc($product_result)): ?>
                            <option value="<?php echo $row['product_id']; ?>"><?php echo $row['product_id']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <!-- Submit button styled like other buttons -->
            <input type="submit" value="Submit">
        </form>

    </div>

</body>
</html>

<?php
mysqli_close($conn);
?>