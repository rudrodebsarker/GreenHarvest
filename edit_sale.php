<?php
// === DATABASE CONNECTION ===
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// === INITIALIZE VARIABLES ===
$sale_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sale = [];
$retailers = [];
$consumers = [];
$error = '';

// === FETCH EXISTING DATA ===
if ($sale_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM sale WHERE sale_id = ?");
    $stmt->bind_param("i", $sale_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sale = $result->fetch_assoc();
    
    // Fetch retailers
    $retailerRes = $conn->query("SELECT retailer_id FROM retailer");
    while ($row = $retailerRes->fetch_assoc()) {
        $retailers[] = $row['retailer_id'];
    }
    
    // Fetch consumers
    $consumerRes = $conn->query("SELECT consumer_id FROM consumer");
    while ($row = $consumerRes->fetch_assoc()) {
        $consumers[] = $row['consumer_id'];
    }
}

// === HANDLE FORM SUBMISSION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['saleDate'];
    $time_input = $_POST['saleTime'];
    
    // Convert to proper MySQL TIME format
    try {
        $time = date("H:i:s", strtotime($time_input));
    } catch (Exception $e) {
        $error = "Invalid time format: " . $e->getMessage();
    }
    
    $retailerId = $_POST['retailerId'];
    $consumerId = $_POST['consumerId'];
    
    if (!$error) {
        $stmt = $conn->prepare("UPDATE sale SET 
            sale_date = ?,
            time = ?,
            retailer_id = ?,
            consumer_id = ?
            WHERE sale_id = ?");
            
        $stmt->bind_param("ssssi", 
            $date,
            $time,
            $retailerId,
            $consumerId,
            $sale_id
        );
        
        if ($stmt->execute()) {
            header("Location: sale_information.php");
            exit();
        } else {
            $error = "Error updating record: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Sale Information</title>
  <style>
    /* Original CSS preserved */
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      padding: 40px;
    }
    .container {
      background: white;
      padding: 30px;
      border-radius: 10px;
      max-width: 500px;
      margin: auto;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    h2 {
      text-align: center;
      color: #333;
    }
    label {
      display: block;
      margin-bottom: 10px;
      font-weight: bold;
    }
    input[type="text"],
    input[type="date"],
    input[type="time"],
    select {
      width: 100%;
      padding: 8px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    button {
      padding: 10px 20px;
      border: none;
      background-color: #3498db;
      color: white;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      width: 100%;
    }
    button:hover {
      background-color: #2980b9;
    }
    .back-btn {
      margin-top: 15px;
      background-color: #2ecc71;
    }
    .back-btn:hover {
      background-color: #27ae60;
    }
    .error-message {
      color: red;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 5px;
      background: #ffe6e6;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Edit Sale Information</h2>
    
    <?php if($error): ?>
    <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <label for="saleId">Sale ID</label>
      <input type="text" id="saleId" name="saleId" 
             value="<?= htmlspecialchars($sale['sale_id'] ?? '') ?>" readonly>

      <label for="saleDate">Date</label>
      <input type="date" id="saleDate" name="saleDate" 
             value="<?= htmlspecialchars($sale['sale_date'] ?? '') ?>" required>

      <label for="saleTime">Time</label>
      <input type="time" id="saleTime" name="saleTime" 
             value="<?= isset($sale['time']) ? date('H:i:s', strtotime($sale['time'])) : '' ?>" 
             step="1" required>

      <label for="retailerId">Retailer ID</label>
      <select id="retailerId" name="retailerId" required>
        <option value="">-- Select Retailer ID --</option>
        <?php foreach ($retailers as $rid): ?>
          <option value="<?= htmlspecialchars($rid) ?>" 
            <?= ($rid == ($sale['retailer_id'] ?? '')) ? 'selected' : '' ?>>
            <?= htmlspecialchars($rid) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="consumerId">Consumer ID</label>
      <select id="consumerId" name="consumerId" required>
        <option value="">-- Select Consumer ID --</option>
        <?php foreach ($consumers as $cid): ?>
          <option value="<?= htmlspecialchars($cid) ?>" 
            <?= ($cid == ($sale['consumer_id'] ?? '')) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cid) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit">Save Changes</button>
      <button type="button" class="back-btn" 
              onclick="window.location.href='sale_information.php'">
        Back to Sale List
      </button>
    </form>
  </div>
</body>
</html>