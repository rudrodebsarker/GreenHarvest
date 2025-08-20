<?php
include('server.php');

if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'Wholesaler') {
    $_SESSION['msg'] = "You must log in as Wholesaler first";
    header('Location: login.php');
    exit();
}

$conn = new mysqli("localhost", "root", "", "agriculture");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch wholesaler ID
$wholesaler_id = '';
if (isset($_SESSION['selected_wholesaler']['wholesaler_id'])) {
    $wholesaler_id = $_SESSION['selected_wholesaler']['wholesaler_id'];
} else {
    $username = $_SESSION['username'];
    $result = $conn->query("SELECT wholesaler_id FROM wholesaler WHERE name = '$username' LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $wholesaler_id = $row['wholesaler_id'] ?? '';
    }
}

// Fetch available products for wholesaler
$products = [];

if ($wholesaler_id) {
    $query = "
        SELECT 
            ap.product_id,
            ap.name,
            ap.seasonality,
            ap.type,
            GROUP_CONCAT(DISTINCT apv.variety SEPARATOR ', ') AS varieties,
            sap.quantity_shipped,
            COALESCE(SUM(tat.quantity), 0) AS quantity_sold
        FROM shipment_agri_product sap
        JOIN agri_product ap ON sap.product_id = ap.product_id
        LEFT JOIN agri_product_variety apv ON ap.product_id = apv.product_id
        JOIN shipment s ON sap.shipment_id = s.shipment_id
        JOIN warehouse w ON s.warehouse_id = w.warehouse_id
        LEFT JOIN track_agri_traders tat 
            ON tat.product_id = ap.product_id AND tat.wholesaler_id = w.wholesaler_id
        WHERE w.wholesaler_id = ?
        GROUP BY sap.shipment_id, sap.product_id
        HAVING quantity_shipped > quantity_sold
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $wholesaler_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Products to Wholesaler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8fafc;
            padding: 40px 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 1100px;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }

        h2 {
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
            color: #2c3e50;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
        }

        .search-container input {
            width: 300px;
            margin-right: 10px;
        }

        .btn-back {
            position: absolute;
            top: 30px;
            right: 40px;
        }

        .btn-back a {
            text-decoration: none;
        }

        .table thead {
            background-color: #f1f1f1;
        }

        .table th {
            vertical-align: middle;
        }

        .table td, .table th {
            text-align: center;
        }

        .no-data {
            text-align: center;
            font-style: italic;
            color: #888;
        }
    </style>
</head>
<body>

    <div class="btn-back">
        <a href="wholesaler_dashboard.php" class="btn btn-success">&larr; Back to Dashboard</a>
    </div>

    <div class="container">
        <h2>Available Products to Wholesaler</h2>

        <div class="search-container">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by Product Name or ID...">
            <button class="btn btn-primary" onclick="searchTable()">Search</button>
        </div>

        <table class="table table-bordered table-hover" id="productTable">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Seasonality</th>
                    <th>Type</th>
                    <th>Variety</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['product_id']) ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['seasonality']) ?></td>
                            <td><?= htmlspecialchars($product['type']) ?></td>
                            <td><?= htmlspecialchars($product['varieties']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="no-data">No available products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function searchTable() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const rows = document.querySelectorAll("#productTable tbody tr");

            rows.forEach(row => {
                const cells = row.querySelectorAll("td");
                const match = [...cells].slice(0, 2).some(cell =>
                    cell.textContent.toLowerCase().includes(input)
                );
                row.style.display = match ? "" : "none";
            });
        }
    </script>

</body>
</html>
