<?php 
include 'db_config.php';  // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Supply Level</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ccc; }
        th { background-color: #f4f4f4; }
        h1 { color: #333; }

        /* Set chart size to 650px width and 600px height */
        #inventoryChart {
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

<h1>Inventory Management</h1>

<!-- Container for the chart -->
<div class="chart-container">
    <!-- Stock Level Chart -->
    <div>
        <h2>Stock Level Chart</h2>
        <canvas id="inventoryChart"></canvas>
    </div>
</div>

<!-- Table to display inventory data -->
<table id="inventoryTable">
    <thead>
        <tr>
            <th>Warehouse Name</th>
            <th>Location</th>
            <th>Available Stock</th>
            <th>Last Updated</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Query to represent inventory from WAREHOUSE table
        $sql = "SELECT w.name AS warehouse_name, w.location, w.available_stock_of_product, w.last_updated
                FROM WAREHOUSE w";
        
        $result = $conn->query($sql);
        
        // Loop through the result and display data in table
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['warehouse_name']}</td>
                    <td>{$row['location']}</td>
                    <td>{$row['available_stock_of_product']} units</td>
                    <td>{$row['last_updated']}</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<script>
    // Prepare chart data for the first chart based on warehouse_name and available_stock
    const warehouseLabels = <?php
    $result->data_seek(0);  // Rewind the result pointer to fetch labels
    $labels = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['warehouse_name'];  // Using warehouse_name for X-axis labels
    }
    echo json_encode($labels);
    ?>;

    const stockLevels = <?php
    $result->data_seek(0);  // Rewind the result pointer to fetch stock data
    $stockLevels = [];
    while ($row = $result->fetch_assoc()) {
        $stockLevels[] = $row['available_stock_of_product'];  // Stock level for Y-axis
    }
    echo json_encode($stockLevels);
    ?>;

    // Create the chart based on warehouse_name
    const ctx = document.getElementById('inventoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',  // Bar chart
        data: {
            labels: warehouseLabels,  // Using warehouse_name as X-axis labels
            datasets: [{
                label: 'Available Stock Levels',
                data: stockLevels,  // Stock level data for Y-axis
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,  // Ensures chart is responsive
            maintainAspectRatio: false,  // Allows custom width/height from CSS
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
