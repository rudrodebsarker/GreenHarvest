<?php
include 'db_config.php';  // Include database connection

// Fetch the data based on shipment_id and product_id for editing
if (isset($_GET['shipment_id']) && isset($_GET['product_id'])) {
    $shipment_id = $_GET['shipment_id'];
    $product_id = $_GET['product_id'];

    // Fetch the current details of the shipment and product
    $sql = "SELECT sap.shipment_id, sap.product_id, sap.quantity_shipped, sap.cost, p.name AS product_name 
            FROM SHIPMENT_AGRI_PRODUCT sap
            JOIN AGRI_PRODUCT p ON sap.product_id = p.product_id
            WHERE sap.shipment_id = '$shipment_id' AND sap.product_id = '$product_id'";

    $result = $conn->query($sql);

    // If data exists, fetch it
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_quantity = $row['quantity_shipped'];
        $current_cost = $row['cost'];
        $product_name = $row['product_name'];
    } else {
        echo "Record not found!";
        exit;
    }
}

// Handle form submission to update the record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $quantity_shipped = $_POST['quantity_shipped'];
    $product_total_cost = $_POST['product_total_cost'];

    // Update the SHIPMENT_AGRI_PRODUCT table with new data
    $update_sql = "UPDATE SHIPMENT_AGRI_PRODUCT 
                   SET quantity_shipped = '$quantity_shipped', cost = '$product_total_cost'
                   WHERE shipment_id = '$shipment_id' AND product_id = '$product_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "Product shipment details updated successfully.";
        header("Location: product_shipment_details.php"); // Redirect back to the main page
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product Shipment</title>
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

        input[type="number"], input[type="text"], select {
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
    <a href="product_shipment_details.php" class="back-btn">Back to Product Shipment Details</a>

    <h1>Edit Product Shipment Details</h1>

    <!-- Edit Product Shipment Form -->
    <div class="form-container">
        <form method="POST" action="edit_product_shipment.php?shipment_id=<?php echo $shipment_id; ?>&product_id=<?php echo $product_id; ?>">
            <label for="shipment_id">Shipment ID:</label>
            <input type="text" id="shipment_id" name="shipment_id" value="<?php echo $shipment_id; ?>" disabled><br><br>

            <label for="product_id">Product ID:</label>
            <input type="text" id="product_id" name="product_id" value="<?php echo $product_id; ?>" disabled><br><br>

            <label for="quantity_shipped">Quantity Shipped:</label>
            <input type="number" id="quantity_shipped" name="quantity_shipped" value="<?php echo $current_quantity; ?>" required><br><br>

            <label for="product_total_cost">Product Total Cost:</label>
            <input type="number" id="product_total_cost" name="product_total_cost" value="<?php echo $product_total_cost; ?>" required><br><br>

            <button type="submit" name="update">Update</button>
        </form>
    </div>
</div>

</body>
</html>