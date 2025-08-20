<?php
include 'db_config.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $shipment_id = $_POST['shipment_id'];
    $product_id = $_POST['product_id'];
    $quantity_shipped = $_POST['quantity_shipped'];
    $product_total_cost = $_POST['product_total_cost'];

   
    $sql = "INSERT INTO SHIPMENT_AGRI_PRODUCT (shipment_id, product_id, quantity_shipped, cost) 
            VALUES ('$shipment_id', '$product_id', '$quantity_shipped', '$product_total_cost')";

    if ($conn->query($sql) === TRUE) {
        echo "Product shipment details added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Shipment Details</title>
    <style>
        /* General body styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
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
            max-width: 1000px;
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
            display: block;
        }

        input[type="text"], input[type="number"], select {
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
            width: 100%;
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

        /* Back button styling */
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
</head>
<body>

<div class="container">
    <!-- Back Button -->
    <a href="dashboard.php" class="back-btn">Back to Dashboard</a>

    <h1>Product Shipment Details</h1>

    <!-- Product Shipment Form -->
    <div class="form-container">
        <form method="POST" action="product_shipment_details.php">
            <label for="shipment_id">Shipment ID:</label>
            <select id="shipment_id" name="shipment_id" required>
                <?php
                // Fetch shipment IDs from the SHIPMENT table
                $sql = "SELECT shipment_id FROM SHIPMENT";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['shipment_id'] . "'>" . $row['shipment_id'] . "</option>";
                }
                ?>
            </select><br><br>

            <label for="product_id">Product ID:</label>
            <select id="product_id" name="product_id" required>
                <?php
                // Fetch product IDs from the agri_product table (displaying product_id)
                $sql = "SELECT product_id, name FROM agri_product"; // Updated to fetch from agri_product
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['product_id'] . "'>" . $row['product_id'] . "</option>";  // Display Product ID instead of name
                }
                ?>
            </select><br><br>

            <label for="quantity_shipped">Quantity Shipped:</label>
            <input type="number" id="quantity_shipped" name="quantity_shipped" required><br><br>

            <label for="product_total_cost">Product Total Cost:</label>
            <input type="number" id="product_total_cost" name="product_total_cost" required><br><br>

            <button type="submit" name="submit">Submit</button>
        </form>
    </div>

    <!-- Display the Shipment Agri Product Details in a Table -->
    <h2>Shipment Agri Product Details</h2>
    <table>
        <thead>
            <tr>
                <th>Shipment ID</th>
                <th>Product ID</th>
                <th>Quantity Shipped</th>
                <th>Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch data from the SHIPMENT_AGRI_PRODUCT table
            $sql = "SELECT sap.shipment_id, sap.product_id, sap.quantity_shipped, sap.cost 
                    FROM SHIPMENT_AGRI_PRODUCT sap";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['shipment_id']}</td>
                        <td>{$row['product_id']}</td>
                        <td>{$row['quantity_shipped']}</td>
                        <td>{$row['cost']}</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
