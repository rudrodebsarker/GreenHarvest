<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Filter functionality
$filterQuery = "";
if (isset($_GET['filter_id']) && !empty($_GET['filter_id'])) {
    $filter_id = intval($_GET['filter_id']);
    $filterQuery = " WHERE wholesaler_id = $filter_id";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wholesaler Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        button:hover {
            background: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .action-links a {
            color: #2196F3;
            text-decoration: none;
            margin-right: 10px;
        }
        .action-links a:hover {
            text-decoration: underline;
        }
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .nav-buttons a {
            text-decoration: none;
        }
        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }
        .filter-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-btn {
            background: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .clear-filter {
            background: #f5f5f5;
            color: #333;
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .clear-filter:hover {
            background: #e5e5e5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registered Wholesalers</h2>

        <form class="filter-form" method="GET" action="">
            <input type="text" name="filter_id" placeholder="Filter by Wholesaler ID" class="filter-input" value="<?php echo isset($_GET['filter_id']) ? htmlspecialchars($_GET['filter_id']) : ''; ?>">
            <button type="submit" class="filter-btn">Filter</button>
            <?php if (isset($_GET['filter_id']) && !empty($_GET['filter_id'])): ?>
                <a href="wholesaler_list.php" class="clear-filter">Clear</a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Wholesaler ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Address</th>
                    <th>Country</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM wholesaler" . $filterQuery . " ORDER BY wholesaler_id DESC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['wholesaler_id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['contact']) ?></td>
                    <td>
                        <?= htmlspecialchars($row['road']) ?>, 
                        <?= htmlspecialchars($row['area']) ?>, 
                        <?= htmlspecialchars($row['District']) ?><br>
                        <?= htmlspecialchars($row['country']) ?>
                    </td>
                    <td><?= htmlspecialchars($row['country']) ?></td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
