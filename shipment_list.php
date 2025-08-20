<?php
include 'db_config.php';  // Include database connection

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete the shipment record
    $delete_sql = "DELETE FROM SHIPMENT WHERE shipment_id = '$delete_id'";

    if ($conn->query($delete_sql) === TRUE) {
        echo "<script>alert('Shipment deleted successfully.'); window.location.href='shipment_list.php';</script>";
    } else {
        echo "Error: " . $delete_sql . "<br>" . $conn->error;
    }
}

// Fetch all shipment records
$sql = "SELECT * FROM SHIPMENT";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipment List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
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

        .actions a {
            text-decoration: none;
            color: #2980b9;
            margin: 0 10px;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        .back {
    padding: 10px 20px;
    background-color: #2980b9;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    width: auto;
}

.btn:hover {
    background-color: #1d6a8b;
}


        .btn:hover {
            background-color: #1d6a8b;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Shipment List</h1>

    <table>
        <thead>
            <tr>
                <th>Shipment ID</th>
                <th>Ship Date</th>
                <th>Warehouse ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch and display shipment records
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['shipment_id']}</td>
                            <td>{$row['ship_date']}</td>
                            <td>{$row['warehouse_id']}</td>
                            <td class='actions'>
                                <a href='shipment_edit.php?id={$row['shipment_id']}'>Edit</a> |
                                <a href='shipment_list.php?delete_id={$row['shipment_id']}' onclick='return confirm(\"Are you sure you want to delete this shipment?\")'>Delete</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No shipments found</td></tr>";
            }
            ?>
        </tbody>
    </table>

    

    <!-- View Shipment List Button -->
    <button onclick="window.location.href='shipment.php';" class="back">Back</button>

</div>

</body>
</html>

<?php
$conn->close();
?>
