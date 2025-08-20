<?php
include 'db_config.php';  // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Shipment Details</title>
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
    </style>
</head>
<body>

<div class="container">
    <h1>Product Shipment Details</h1>

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
