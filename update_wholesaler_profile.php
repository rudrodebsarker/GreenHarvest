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

// Handle Add form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $_SESSION['selected_wholesaler'] = [
        'wholesaler_id' => $_POST['wholesaler_id'],
        'name' => $_POST['name'],
        'contact' => $_POST['contact'],
        'road' => $_POST['road'],
        'house' => $_POST['house'],
        'area' => $_POST['area'],
        'district' => $_POST['district'],
        'country' => $_POST['country'],
    ];
}

// Handle AJAX fetch request
if (isset($_GET['ajax_fetch']) && isset($_GET['wholesaler_id'])) {
    $wholesaler_id = $conn->real_escape_string($_GET['wholesaler_id']);
    $query = "SELECT * FROM wholesaler WHERE wholesaler_id='$wholesaler_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo "invalid";
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Wholesaler - Update Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
  <style>
    body { background-color: #f8fbfd; font-family: 'Segoe UI', sans-serif; }
    .container-box { background: #fff; border-radius: 10px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-top: 30px; }
    .form-section { margin-bottom: 30px; }
    .form-label { font-weight: 600; }
    .btn-primary { padding: 8px 20px; }
    .btn-success { font-weight: 600; }
    .action-btn { margin-right: 5px; }
    .error { color: red; margin-top: 5px; }
  </style>
</head>
<body>

<div class="container mt-4">
  <h3 class="mb-4">Update Wholesaler Profile</h3>

  <!-- Input Form -->
  <div class="container-box">
    <form method="POST">
      <div class="row g-3 form-section">
        <div class="col-md-4">
          <label class="form-label">Wholesaler ID</label>
          <input type="text" class="form-control" id="wholesalerId" name="wholesaler_id" onblur="fetchWholesalerDetails()" required>
          <div id="error-message" class="error"></div>
        </div>
        <div class="col-md-4">
          <label class="form-label">Name</label>
          <input type="text" class="form-control" id="wholesalerName" name="name" readonly required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Contact</label>
          <input type="text" class="form-control" id="wholesalerContact" name="contact" readonly required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Road</label>
          <input type="text" class="form-control" id="road" name="road" readonly required>
        </div>
        <div class="col-md-4">
          <label class="form-label">House</label>
          <input type="text" class="form-control" id="house" name="house" readonly required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Area</label>
          <input type="text" class="form-control" id="area" name="area" readonly required>
        </div>
        <div class="col-md-4">
          <label class="form-label">District</label>
          <input type="text" class="form-control" id="district" name="district" readonly required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Country</label>
          <input type="text" class="form-control" id="country" name="country" readonly required>
        </div>
        <div class="col-md-4 d-flex align-items-end">
          <button type="submit" name="add" class="btn btn-primary w-100">Add</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Table Section -->
  <div class="container-box mt-4">
    <table class="table table-bordered table-striped">
      <thead class="table-light">
        <tr>
          <th>Wholesaler ID</th>
          <th>Name</th>
          <th>Contact</th>
          <th>Road</th>
          <th>House</th>
          <th>Area</th>
          <th>District</th>
          <th>Country</th>
        </tr>
      </thead>
      <tbody id="wholesalerTableBody">
        <?php if (isset($_SESSION['selected_wholesaler'])): ?>
        <tr>
          <td><?= htmlspecialchars($_SESSION['selected_wholesaler']['wholesaler_id']) ?></td>
          <td><?= htmlspecialchars($_SESSION['selected_wholesaler']['name']) ?></td>
          <td><?= htmlspecialchars($_SESSION['selected_wholesaler']['contact']) ?></td>
          <td><?= htmlspecialchars($_SESSION['selected_wholesaler']['road']) ?></td>
          <td><?= htmlspecialchars($_SESSION['selected_wholesaler']['house']) ?></td>
          <td><?= htmlspecialchars($_SESSION['selected_wholesaler']['area']) ?></td>
          <td><?= htmlspecialchars($_SESSION['selected_wholesaler']['district']) ?></td>
          <td><?= htmlspecialchars($_SESSION['selected_wholesaler']['country']) ?></td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <a href="wholesaler_dashboard.php" class="btn btn-success mt-2">← Back to Dashboard</a>
  </div>
</div>

<script>
function fetchWholesalerDetails() {
  const id = document.getElementById('wholesalerId').value;
  const errorMessage = document.getElementById('error-message');

  if (id.trim() === '') {
    return;
  }

  const xhr = new XMLHttpRequest();
  xhr.open('GET', 'update_wholesaler_profile.php?ajax_fetch=1&wholesaler_id=' + id, true);
  xhr.onload = function() {
    if (this.status == 200) {
      if (this.responseText === 'invalid') {
        errorMessage.textContent = "Invalid Wholesaler ID!";
        document.getElementById('wholesalerName').value = '';
        document.getElementById('wholesalerContact').value = '';
        document.getElementById('road').value = '';
        document.getElementById('house').value = '';
        document.getElementById('area').value = '';
        document.getElementById('district').value = '';
        document.getElementById('country').value = '';
      } else {
        errorMessage.textContent = "";
        const data = JSON.parse(this.responseText);
        document.getElementById('wholesalerName').value = data.name;
        document.getElementById('wholesalerContact').value = data.contact;
        document.getElementById('road').value = data.road;
        document.getElementById('house').value = data.house;
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
