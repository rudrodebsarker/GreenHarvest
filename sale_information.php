<?php
//DATABASE CONNECTION
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

//HANDLE DELETE
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM sale WHERE sale_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

//HANDLE SALE INSERTION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addSale') {
    $saleId = $_POST['saleId'];
    $date = $_POST['saleDate'];
    $time = $_POST['saleTime'];
    $retailerId = $_POST['retailerId'];
    $consumerId = $_POST['consumerId'];

    if ($saleId && $date && $time && $retailerId && $consumerId) {
        $stmt = $conn->prepare("INSERT INTO sale (sale_id, sale_date, time, retailer_id, consumer_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $saleId, $date, $time, $retailerId, $consumerId);

        if ($stmt->execute()) {
            $message = "Sale record added successfully!";
        } else {
            $message = "Error: " . $conn->error;
        }
        $stmt->close();
    } else {
        $message = "Please fill in all fields.";
    }
}

//FETCH DATA
$retailers = [];
$consumers = [];

$retailerRes = $conn->query("SELECT retailer_id FROM retailer");
while ($row = $retailerRes->fetch_assoc()) {
    $retailers[] = $row['retailer_id'];
}

$consumerRes = $conn->query("SELECT consumer_id FROM consumer");
while ($row = $consumerRes->fetch_assoc()) {
    $consumers[] = $row['consumer_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sale Information</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px;
            background: #f1f5f9;
            color: #1e293b;
        }
        h2 {
            color: #1e293b;
            margin-bottom: 20px;
        }
        .top-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .form-section {
            background: #ffffff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            align-items: flex-end;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            width: calc(33.333% - 20px);
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 6px;
        }
        input, select {
            padding: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            background-color: #3b82f6;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease-in-out;
        }
        button:hover {
            background-color: #2563eb;
        }
        .view-details {
            background-color: #10b981;
        }
        .view-details:hover {
            background-color: #059669;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        th, td {
            padding: 14px 16px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }
        th {
            background-color: #f8fafc;
            font-weight: 600;
        }
        .message {
            margin-bottom: 16px;
            padding: 12px;
            border-radius: 6px;
            background: #d1fae5;
            color: #065f46;
            font-weight: 500;
        }
        .action-btn {
            background: #4CAF50;
            padding: 6px 12px;
            margin: 0 2px;
        }
        .delete-btn {
            background: #f44336;
            padding: 6px 12px;
        }
        .back-btn {
            background-color: #10b981;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <h2>Sale Information</h2>

    <?php if (isset($message)): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Top Buttons -->
    <div class="top-buttons">
        <a href="retailer_dashboard.php" class="back-btn">← Back to Dashboard</a>
        <a href="sale_details.php"><button class="view-details">View Sale Details</button></a>
    </div>

    <!-- Form Section -->
    <form method="POST">
        <input type="hidden" name="action" value="addSale">
        <div class="form-section">
            <div class="form-group">
                <label for="saleId">Sale ID</label>
                <input type="number" id="saleId" name="saleId" required>
            </div>
            <div class="form-group">
                <label for="saleDate">Date</label>
                <input type="date" id="saleDate" name="saleDate" required>
            </div>
            <div class="form-group">
                <label for="saleTime">Time</label>
                <input type="time" id="saleTime" name="saleTime" step="1" required>
            </div>
            <div class="form-group">
                <label for="retailerId">Retailer ID</label>
                <select id="retailerId" name="retailerId" required>
                    <option value="">-- Select Retailer ID --</option>
                    <?php foreach ($retailers as $rid): ?>
                        <option value="<?= htmlspecialchars($rid) ?>"><?= htmlspecialchars($rid) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="consumerId">Consumer ID</label>
                <select id="consumerId" name="consumerId" required>
                    <option value="">-- Select Consumer ID --</option>
                    <?php foreach ($consumers as $cid): ?>
                        <option value="<?= htmlspecialchars($cid) ?>"><?= htmlspecialchars($cid) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="width: 100px;">
                <button type="submit">Add</button>
            </div>
        </div>
    </form>

    <!-- Table Section -->
    <table>
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>Date</th>
                <th>Time</th>
                <th>Retailer ID</th>
                <th>Consumer ID</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sales = $conn->query("SELECT * FROM sale");
            while ($row = $sales->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['sale_id']) ?></td>
                <td><?= htmlspecialchars($row['sale_date']) ?></td>
                <td><?= htmlspecialchars($row['time']) ?></td>
                <td><?= htmlspecialchars($row['retailer_id']) ?></td>
                <td><?= htmlspecialchars($row['consumer_id']) ?></td>
                <td>
                    <a href="edit_sale.php?id=<?= $row['sale_id'] ?>">
                        <button class="action-btn">Edit</button>
                    </a>
                    <button class="delete-btn"
                            onclick="if(confirm('Are you sure?')) window.location='?delete_id=<?= $row['sale_id'] ?>'">
                        Delete
                    </button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
