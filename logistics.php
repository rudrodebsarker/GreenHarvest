<?php 
include 'db_config.php';  // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logistics - Supply Level</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ccc; }
        th { background-color: #f4f4f4; }
        h1 { color: #333; }

        /* Set chart size */
        #logisticsChart, #logisticsChart2 {
            width: 450px !important;  /* Ensure fixed width */
            height: 300px !important; /* Ensure fixed height */
            display: block;  /* Ensure it does not stretch */
            margin: 20px auto;  /* Center the chart */
        }
        
        .chart-container {
            display: flex;
            justify-content: space-evenly;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<h1>Logistics Tracking</h1>

<!-- Container for the two charts -->
<div class="chart-container">
    <!-- Shipment Status Chart -->
    <div>
        <h2>Shipment Status Chart</h2>
        <canvas id="logisticsChart"></canvas>
    </div>
    
    <!-- Product Quantity and Ship Date Chart -->
    <div>
        <h2>Product Quantity by Ship Date</h2>
        <canvas id="logisticsChart2"></canvas>
    </div>
</div>

<!-- Table to display shipment data -->
<table id="logisticsTable">
    <thead>
        <tr>
            <th>Shipment ID</th>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Quantity</th>
            <th>Ship Date</th>
            <th>Warehouse</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Query to fetch shipment data from SHIPMENT_AGRI_PRODUCT, AGRI_PRODUCT, and WAREHOUSE tables
        $sql = "SELECT sap.shipment_id, sap.product_id, p.name AS product_name, sap.quantity_shipped, s.ship_date, w.name AS warehouse
                FROM SHIPMENT_AGRI_PRODUCT sap
                JOIN AGRI_PRODUCT p ON sap.product_id = p.product_id
                JOIN SHIPMENT s ON sap.shipment_id = s.shipment_id
                JOIN WAREHOUSE w ON s.warehouse_id = w.warehouse_id";
        $result = $conn->query($sql);

        // Loop through the result and display data in table
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['shipment_id']}</td>
                    <td>{$row['product_id']}</td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['quantity_shipped']} units</td>
                    <td>{$row['ship_date']}</td>
                    <td>{$row['warehouse']}</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<script>
    // Prepare chart data from PHP for the first chart (shipment_id and product_name)
    const labels = [];
    const quantities = [];

    <?php
    // Rewind result pointer and store data in JavaScript arrays for the first chart
    $result->data_seek(0); 
    while ($row = $result->fetch_assoc()) {
        // Combine shipment_id and product_name for the X-axis label
        $label = "Shipment: {$row['shipment_id']} - Product: {$row['product_name']}";
        echo "labels.push('{$label}');";  // Combining shipment_id and product_name for X-axis labels
        echo "quantities.push({$row['quantity_shipped']});";  // Using quantity_shipped for Y-axis
    }
    ?>

    // Create the first chart
    const ctx = document.getElementById('logisticsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',  // Line chart
        data: {
            labels: labels,
            datasets: [{
                label: 'Quantity Shipped',
                data: quantities,
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true  // Start the y-axis from 0
                }
            }
        }
    });

    // Prepare chart data from PHP for the second chart (ship_date and quantity_shipped)
    const shipDateLabels = [];
    const shipDateQuantities = [];

    <?php
    // Rewind result pointer and store data in JavaScript arrays for the second chart
    $result->data_seek(0); 
    while ($row = $result->fetch_assoc()) {
        echo "shipDateLabels.push('{$row['ship_date']}');";
        echo "shipDateQuantities.push({$row['quantity_shipped']});";
    }
    ?>

    // Create the second chart (Product Quantity by Ship Date Chart)
    const ctx2 = document.getElementById('logisticsChart2').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',  // Bar chart
        data: {
            labels: shipDateLabels,  // Ship Dates on X-axis
            datasets: [{
                label: 'Quantity Shipped by Ship Date',
                data: shipDateQuantities,  // Y-axis for Quantity
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true  // Start the y-axis from 0
                }
            }
        }
    });
</script>

</body>
</html>
