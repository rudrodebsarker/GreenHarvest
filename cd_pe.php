<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "agriculture";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to fetch sales data
function getSalesData($conn, $startDate = null, $endDate = null) {
    $sql = "SELECT s.sale_id, sd.product_id, sd.quantity_sold as quantity, 
                   sd.unit_price as price, DATE(s.sale_date) as date
            FROM sale s
            JOIN sale_details sd ON s.sale_id = sd.sale_id";
    
    $conditions = [];
    if ($startDate) {
        $conditions[] = "s.sale_date >= '$startDate'";
    }
    if ($endDate) {
        $conditions[] = "s.sale_date <= '$endDate 23:59:59'";
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY s.sale_date";
    
    $result = $conn->query($sql);
    
    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    return $data;
}

// Function to fetch price elasticity data
function getElasticityData($conn) {
    // This is a simplified calculation - in a real application you'd need more sophisticated analysis
    $sql = "SELECT 
                p.product_id, 
                p.name,
                AVG(sd.unit_price) as avg_price,
                SUM(sd.quantity_sold) as total_quantity
            FROM agri_product p
            LEFT JOIN sale_details sd ON p.product_id = sd.product_id
            GROUP BY p.product_id, p.name
            ORDER BY p.product_id";
    
    $result = $conn->query($sql);
    
    $products = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    
    // Calculate price elasticity (simplified example)
    $elasticityData = [];
    foreach ($products as $product) {
        if ($product['avg_price'] > 0 && $product['total_quantity'] > 0) {
            // This is a placeholder - real elasticity calculation would compare changes over time
            $price_change = rand(5, 20); // Random for example
            $quantity_change = -rand(3, 15); // Random for example (inverse relationship)
            $elasticity = $quantity_change / $price_change;
            
            $elasticityData[] = [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'price_change' => $price_change,
                'quantity_change' => $quantity_change,
                'elasticity' => round($elasticity, 2)
            ];
        }
    }
    
    return $elasticityData;
}

// Check if it's an AJAX request for filtered data
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_sales':
            $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
            $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
            echo json_encode(getSalesData($conn, $startDate, $endDate));
            break;
            
        case 'get_elasticity':
            $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : null;
            $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : null;
            echo json_encode(getElasticityData($conn));
            break;
    }
    
    exit;
}

// Get initial data
$salesData = getSalesData($conn);
$elasticityData = getElasticityData($conn);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricultural Analytics Dashboard</title>
    
    <!-- External Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- Internal CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 200px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            padding: 20px 0;
            position: fixed;
        }

        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid #34495e;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            padding: 15px 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sidebar-menu li:hover {
            background-color: #34495e;
        }

        .sidebar-menu li.active {
            background-color: #3498db;
        }

        .main-content {
            margin-left: 200px;
            padding: 20px;
            width: calc(100% - 200px);
        }

        .container {
            width: 90%;
            margin: auto;
        }

        .chart-wrapper {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        /* New style for side-by-side charts */
        .side-by-side-charts {
            display: flex;
            justify-content: space-between;
            width: 100%;
            gap: 20px;
        }

        .chart-box {
            width: 600px;
        }

        .side-by-side-charts .chart-box {
            width: calc(50% - 10px);
            min-width: 400px;
        }

        canvas {
            width: 100%;
            height: 300px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }

        button {
            margin: 10px;
            padding: 8px 12px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }

        .controls {
            margin: 20px;
        }

        .view {
            display: none;
        }

        .view.active {
            display: block;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Agri-Analytics</h2>
        </div>
        <ul class="sidebar-menu">
            <li class="active" onclick="showView('demand')">Consumer Demand</li>
            <li onclick="showView('elasticity')">Price Elasticity</li>
        </ul>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Consumer Demand View -->
        <div id="demand-view" class="view active">
            <div class="dashboard-header">
                <h2>📊 Consumer Demand Analysis</h2>
                <div class="controls">
                    <input type="date" id="startDate"> to
                    <input type="date" id="endDate">
                    <button onclick="filterData()">Filter</button>
                </div>
            </div>

            <div class="container">
                <div class="chart-wrapper">
                    <div class="chart-box">
                        <canvas id="consumptionChart"></canvas>
                    </div>
                </div>
            </div>

            <table id="salesTable">
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Product ID</th>
                        <th>Quantity Sold</th>
                        <th>Unit Price</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salesData as $sale): ?>
                        <tr>
                            <td><?= $sale['sale_id'] ?></td>
                            <td><?= $sale['product_id'] ?></td>
                            <td><?= $sale['quantity'] ?></td>
                            <td>$<?= number_format($sale['price'], 2) ?></td>
                            <td><?= $sale['date'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Price Elasticity View -->
        <div id="elasticity-view" class="view">
            <div class="dashboard-header">
                <h2>📈 Price Elasticity Analysis</h2>
                <div class="controls">
                    <input type="date" id="elasticityStartDate"> to
                    <input type="date" id="elasticityEndDate">
                    <button onclick="filterElasticityData()">Filter</button>
                </div>
            </div>

            <div class="container">
                <div class="side-by-side-charts">
                    <div class="chart-box">
                        <canvas id="priceChart"></canvas>
                    </div>
                    <div class="chart-box">
                        <canvas id="elasticityChart"></canvas>
                    </div>
                </div>
            </div>

            <table id="elasticityTable">
                <thead>
                    <tr>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Price Change (%)</th>
                        <th>Quantity Change (%)</th>
                        <th>Elasticity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($elasticityData as $item): ?>
                        <tr>
                            <td><?= $item['product_id'] ?></td>
                            <td><?= $item['name'] ?></td>
                            <td><?= $item['price_change'] ?>%</td>
                            <td><?= $item['quantity_change'] ?>%</td>
                            <td><?= $item['elasticity'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Chart instances
        let consumptionChart, priceChart, elasticityChart;
        let salesTable, elasticityTable;

        $(document).ready(function () {
            // Initialize DataTables
            salesTable = $('#salesTable').DataTable();
            elasticityTable = $('#elasticityTable').DataTable();
            
            // Create initial charts
            updateConsumptionChart(<?= json_encode($salesData) ?>);
            updatePriceChart(<?= json_encode($salesData) ?>);
            updateElasticityChart(<?= json_encode($elasticityData) ?>);
        });

        // Function to switch between views
        function showView(viewName) {
            // Update sidebar active item
            $('.sidebar-menu li').removeClass('active');
            $(`.sidebar-menu li:contains(${viewName === 'demand' ? 'Consumer Demand' : 'Price Elasticity'})`).addClass('active');
            
            // Show the selected view
            $('.view').removeClass('active');
            $(`#${viewName}-view`).addClass('active');
        }

        // Chart update functions
        function updateConsumptionChart(data) {
            let labels = data.map(item => item.date);
            let quantities = data.map(item => item.quantity);

            let ctx = document.getElementById('consumptionChart').getContext('2d');

            if (consumptionChart) {
                consumptionChart.destroy();
            }

            consumptionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Quantity Sold',
                        data: quantities,
                        backgroundColor: 'rgba(0, 123, 255, 0.5)',
                        borderColor: 'blue',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Product Consumption Over Time'
                        }
                    }
                }
            });
        }

        function updatePriceChart(data) {
            let labels = data.map(item => item.date);
            let prices = data.map(item => item.price);

            let ctx = document.getElementById('priceChart').getContext('2d');

            if (priceChart) {
                priceChart.destroy();
            }

            priceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Unit Price ($)',
                        data: prices,
                        backgroundColor: 'rgba(40, 167, 69, 0.5)',
                        borderColor: 'green',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Price Trends Over Time'
                        }
                    }
                }
            });
        }

        function updateElasticityChart(data) {
            let labels = data.map(item => item.name);
            let elasticityValues = data.map(item => item.elasticity);

            let ctx = document.getElementById('elasticityChart').getContext('2d');

            if (elasticityChart) {
                elasticityChart.destroy();
            }

            elasticityChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Price Elasticity',
                        data: elasticityValues,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    },
                    plugins: {
                        title: {
                            display: true,
                            text: 'Price Elasticity by Product'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += context.raw.toFixed(2);
                                    label += ' (';
                                    if (Math.abs(context.raw) > 1) {
                                        label += 'Elastic';
                                    } else if (Math.abs(context.raw) === 1) {
                                        label += 'Unit Elastic';
                                    } else {
                                        label += 'Inelastic';
                                    }
                                    label += ')';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Filter functions
        function filterData() {
            let startDate = document.getElementById('startDate').value;
            let endDate = document.getElementById('endDate').value;
            
            $.get('?action=get_sales', {
                start_date: startDate,
                end_date: endDate
            }, function(data) {
                // Update table
                salesTable.clear();
                data.forEach(item => {
                    salesTable.row.add([
                        item.sale_id,
                        item.product_id,
                        item.quantity,
                        '$' + parseFloat(item.price).toFixed(2),
                        item.date
                    ]).draw(false);
                });
                
                // Update chart
                updateConsumptionChart(data);
            }, 'json');
        }

        function filterElasticityData() {
            let startDate = document.getElementById('elasticityStartDate').value;
            let endDate = document.getElementById('elasticityEndDate').value;
            
            $.get('?action=get_elasticity', {
                start_date: startDate,
                end_date: endDate
            }, function(data) {
                // Update table
                elasticityTable.clear();
                data.forEach(item => {
                    elasticityTable.row.add([
                        item.product_id,
                        item.name,
                        item.price_change + '%',
                        item.quantity_change + '%',
                        item.elasticity
                    ]).draw(false);
                });
                
                // Update chart
                updateElasticityChart(data);
            }, 'json');
        }
    </script>
</body>
</html>