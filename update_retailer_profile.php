<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form is submitted by "Add" button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $_SESSION['selected_retailer'] = [
        'retailer_id' => $_POST['retailer_id'],
        'name' => $_POST['name'],
        'contact' => $_POST['contact'],
        'road' => $_POST['road'],
        'area' => $_POST['area'],
        'district' => $_POST['district'],
        'country' => $_POST['country'],
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Update Retailer Profile</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f1f5f9;
      margin: 0;
      padding: 20px;
    }
    h2 {
      font-size: 28px;
      font-weight: bold;
      color: #111827;
      margin-bottom: 20px;
    }
    .container {
      background-color: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      margin-bottom: 30px;
    }
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }
    .form-group {
      display: flex;
      flex-direction: column;
    }
    label {
      font-weight: 600;
      margin-bottom: 5px;
      color: #374151;
    }
    input[type="text"] {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #d1d5db;
      background-color: #f9fafb;
    }
    .add-btn, .back-btn {
      padding: 10px 20px;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
    }
    .add-btn {
      background-color: #3b82f6;
      color: white;
    }
    .add-btn:hover {
      background-color: #2563eb;
    }
    .back-btn {
      background-color: #10b981;
      color: white;
      text-decoration: none;
      display: inline-block;
      margin-bottom: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      border-radius: 10px;
      overflow: hidden;
    }
    th, td {
      padding: 12px 16px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }
    th {
      background-color: #f3f4f6;
      font-weight: 600;
    }
    td:last-child {
      text-align: center;
    }
    .error {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<h2>Update Retailer Profile</h2>

<!-- Back Button -->
<a href="retailer_dashboard.php" class="back-btn">← Back to Dashboard</a>

<!-- Input Form Section -->
<div class="container">
  <form method="POST" id="retailerForm">
    <div class="form-grid">
      <div class="form-group">
        <label for="retailer_id">Retailer ID</label>
        <input type="text" id="retailer_id" name="retailer_id" required onblur="fetchRetailerDetails()">
        <div id="error-message" class="error"></div>
      </div>
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" readonly required>
      </div>
      <div class="form-group">
        <label for="contact">Contact</label>
        <input type="text" id="contact" name="contact" readonly required>
      </div>
      <div class="form-group">
        <label for="road">Road</label>
        <input type="text" id="road" name="road" readonly required>
      </div>
      <div class="form-group">
        <label for="area">Area</label>
        <input type="text" id="area" name="area" readonly required>
      </div>
      <div class="form-group">
        <label for="district">District</label>
        <input type="text" id="district" name="district" readonly required>
      </div>
      <div class="form-group">
        <label for="country">Country</label>
        <input type="text" id="country" name="country" readonly required>
      </div>
    </div>
    <button type="submit" name="add" class="add-btn">Add</button>
  </form>
</div>

<!-- Retailer Table Section -->
<table>
  <thead>
    <tr>
      <th>Retailer ID</th>
      <th>Name</th>
      <th>Contact</th>
      <th>Road</th>
      <th>Area</th>
      <th>District</th>
      <th>Country</th>
    </tr>
  </thead>
  <tbody>
    <?php if (isset($_SESSION['selected_retailer'])): ?>
      <tr>
        <td><?= htmlspecialchars($_SESSION['selected_retailer']['retailer_id']) ?></td>
        <td><?= htmlspecialchars($_SESSION['selected_retailer']['name']) ?></td>
        <td><?= htmlspecialchars($_SESSION['selected_retailer']['contact']) ?></td>
        <td><?= htmlspecialchars($_SESSION['selected_retailer']['road']) ?></td>
        <td><?= htmlspecialchars($_SESSION['selected_retailer']['area']) ?></td>
        <td><?= htmlspecialchars($_SESSION['selected_retailer']['district']) ?></td>
        <td><?= htmlspecialchars($_SESSION['selected_retailer']['country']) ?></td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>

<!-- JavaScript for Fetching Retailer Details -->
<script>
function fetchRetailerDetails() {
    const retailer_id = document.getElementById('retailer_id').value;
    const errorMessage = document.getElementById('error-message');

    if (retailer_id.trim() === '') {
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_retailer.php?retailer_id=' + retailer_id, true);
    xhr.onload = function() {
        if (this.status == 200) {
            if (this.responseText === 'invalid') {
                errorMessage.textContent = "Invalid Retailer ID!";
                document.getElementById('name').value = '';
                document.getElementById('contact').value = '';
                document.getElementById('road').value = '';
                document.getElementById('area').value = '';
                document.getElementById('district').value = '';
                document.getElementById('country').value = '';
            } else {
                errorMessage.textContent = "";
                const data = JSON.parse(this.responseText);
                document.getElementById('name').value = data.name;
                document.getElementById('contact').value = data.contact;
                document.getElementById('road').value = data.road;
                document.getElementById('area').value = data.area;
                document.getElementById('district').value = data.District;
                document.getElementById('country').value = data.country;
            }
        }
    };
    xhr.send();
}
</script>

</body>
</html>
