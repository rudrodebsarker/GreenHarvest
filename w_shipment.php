<?php 
include 'db_config.php';  // Include database connection

// Handle form submission (Insert)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $shipment_id = $_POST['shipment_id'];
    $ship_date = $_POST['ship_date'];
    $warehouse_id = $_POST['warehouse_id'];

    // Ensure shipment_id is unique
    $check_sql = "SELECT * FROM SHIPMENT WHERE shipment_id = '$shipment_id'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Shipment ID already exists. Please use a unique ID.');</script>";
    } else {
        // Insert into SHIPMENT table
        $sql = "INSERT INTO SHIPMENT (shipment_id, ship_date, warehouse_id) 
                VALUES ('$shipment_id', '$ship_date', '$warehouse_id')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('New shipment created successfully.'); window.location.href='shipment.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Shipment Date</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            text-align: center;
            background-color:rgb(6, 52, 84);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            max-width: 800px;
        }

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

        input[type="text"], input[type="date"], select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
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

        .product-details-btn {
            padding: 10px 20px;
            background-color: #f39c12;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
            margin-top: 20px;
            width: 100%;
        }

        .product-details-btn:hover {
            background-color: #e67e22;
        }

        .view-shipment-btn {
            padding: 10px 20px;
            background-color: #16a085;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
            margin-top: 20px;
            width: 100%;
        }

        .view-shipment-btn:hover {
            background-color: #1abc9c;
        }

        /* Back to Dashboard Button */
        .back-to-dashboard-btn {
            padding: 10px 20px;
            background-color: #2980b9;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
            margin-top: 20px;
            width: 200px; /* Set a fixed width */
            margin: 0 auto; /* Center the button */
        }

        .back-to-dashboard-btn:hover {
            background-color: #1d6a8b;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Record Shipment Date</h1>

    <!-- Back to Dashboard Button -->
    <button onclick="window.location.href='wholesaler_dashboard.php';" class="back-to-dashboard-btn">Back to Dashboard</button>


    <div class="form-container">
        <form method="POST" action="shipment.php">
            <label for="shipment_id">Shipment ID (Unique):</label>
            <input type="text" id="shipment_id" name="shipment_id" required>

            <label for="ship_date">Ship Date:</label>
            <input type="date" id="ship_date" name="ship_date" required>

            <label for="warehouse_id">Warehouse ID:</label>
            <select id="warehouse_id" name="warehouse_id" required>
                <?php
                // Fetch all warehouse IDs from the WAREHOUSE table
                $sql = "SELECT warehouse_id FROM WAREHOUSE";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['warehouse_id'] . "'>" . $row['warehouse_id'] . "</option>";
                }
                ?>
            </select>

            <button type="submit" name="submit">Submit</button>
        </form>
    </div>

    <button onclick="window.location.href='w_product_shipment_details.php';" class="product-details-btn">View Product Shipment Details</button>
    <button onclick="window.location.href='shipment_list.php';" class="view-shipment-btn">View Shipment List</button>

</div>

</body>
</html>

