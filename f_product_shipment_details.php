<?php
include 'db_config.php';  // Include database connection

// Handle form submission (Insert into SHIPMENT_AGRI_PRODUCT)
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

// Handle editing the data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $shipment_id = $_POST['shipment_id'];
    $product_id = $_POST['product_id'];
    $quantity_shipped = $_POST['quantity_shipped'];
    $product_total_cost = $_POST['product_total_cost'];

    $sql = "UPDATE SHIPMENT_AGRI_PRODUCT 
            SET quantity_shipped='$quantity_shipped', cost='$product_total_cost' 
            WHERE shipment_id='$shipment_id' AND product_id='$product_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Product shipment details updated successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle deleting the data
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $sql = "DELETE FROM SHIPMENT_AGRI_PRODUCT WHERE shipment_id='$delete_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully.";
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
            margin: 20px 0;
        }

        .back-btn:hover {
            background-color: #2980b9;
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

        .edit-btn, .delete-btn {
            padding: 6px 12px;
            background-color: #f39c12;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn:hover {
            background-color: #e67e22;
        }

        .delete-btn {
            background-color: #e74c3c;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Product Shipment Details</h1>

    <div class="form-container">
        <form method="POST" action="product_shipment_details.php">
            <label for="shipment_id">Shipment ID:</label>
            <select id="shipment_id" name="shipment_id" required>
                <?php
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
                $sql = "SELECT product_id, name FROM agri_product";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['product_id'] . "'>" . $row['product_id'] . "</option>";
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

    <h2>Shipment Agri Product Details</h2>
    <table>
        <thead>
            <tr>
                <th>Shipment ID</th>
                <th>Product ID</th>
                <th>Quantity Shipped</th>
                <th>Cost</th>
                <th>Actions</th>
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
                        <td>
                            <a href='edit_product_shipment.php?shipment_id={$row['shipment_id']}&product_id={$row['product_id']}' class='edit-btn'>Edit</a>
                            <a href='product_shipment_details.php?delete_id={$row['shipment_id']}' class='delete-btn'>Delete</a>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>