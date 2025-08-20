<?php 
include 'db_config.php';  // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage - Supply Level</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ccc; }
        th { background-color: #f4f4f4; }
        h1 { color: #333; }

        /* Set chart size */
        #storageChart {
           width: 650px;  /* Set width */
           height: 600px; /* Set height */
        }
    </style>
</head>
<body>

<h1>Storage Management</h1>

<!-- Table to display storage data -->
<table id="storageTable">
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
        // Query to fetch storage data from WAREHOUSE table
        $sql = "SELECT name AS warehouse, location, available_stock_of_product AS availableStock, last_updated 
                FROM WAREHOUSE";
        $result = $conn->query($sql);
        
        // Loop through the result and display data in table
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['warehouse']}</td>
                    <td>{$row['location']}</td>
                    <td>{$row['availableStock']} units</td>
                    <td>{$row['last_updated']} </td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

<h2>Warehouse Available Stock Chart</h2>
<canvas id="storageChart"></canvas>

<script>
    // Prepare chart data
    const labels = <?php
    $result->data_seek(0);  // Rewind the result pointer to fetch labels
    $labels = [];
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['warehouse'];  // Extract warehouse names
    }
    echo json_encode($labels);
    ?>;

    const availableStock = <?php
    $result->data_seek(0);  // Rewind the result pointer to fetch available stock data
    $availableStock = [];
    while ($row = $result->fetch_assoc()) {
        $availableStock[] = $row['availableStock'];  // Extract available stock values
    }
    echo json_encode($availableStock);
    ?>;

    // Create the chart
    const ctx = document.getElementById('storageChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',  // Bar chart
        data: {
            labels: labels,
            datasets: [{
                label: 'Available Stock',
                data: availableStock,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',  // Light blue color for bars
                borderColor: 'rgba(54, 162, 235, 1)',  // Dark blue border for bars
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,  // Start the y-axis from 0
                    title: {
                        display: true,
                        text: 'Stock (Units)'  // Y-axis label
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Warehouse Name'  // X-axis label
                    }
                }
            }
        }
    });
</script>

</body>
</html>
