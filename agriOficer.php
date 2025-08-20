<?php
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $officer_id = mysqli_real_escape_string($conn, $_POST['officer_id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $road = mysqli_real_escape_string($conn, $_POST['road']);
    $area = mysqli_real_escape_string($conn, $_POST['area']);
    $district = mysqli_real_escape_string($conn, $_POST['district']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $contacts = $_POST['contact']; // array of contacts

    // Check if Officer ID already exists
    $check_sql = "SELECT officer_id FROM agri_officer WHERE officer_id = '$officer_id'";
    $check_result = mysqli_query($conn, $check_sql);

    if (mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Error: Officer ID already exists! Please choose a unique ID.'); window.location.href=window.location.href;</script>";
        exit();
    } else {
        // Insert officer details
        $officer_sql = "INSERT INTO agri_officer (officer_id, name, email, road, area, district, country)
                        VALUES ('$officer_id', '$full_name', '$email', '$road', '$area', '$district', '$country')";

        if (mysqli_query($conn, $officer_sql)) {
            // Insert all contact numbers
            foreach ($contacts as $contact) {
                $contact = mysqli_real_escape_string($conn, $contact);
                if (!empty($contact)) {
                    $contact_sql = "INSERT INTO agri_officer_contact (officer_id, contact)
                                    VALUES ('$officer_id', '$contact')";
                    mysqli_query($conn, $contact_sql);
                }
            }
            echo "<script>alert('Officer and Contacts added successfully!'); window.location.href=window.location.href;</script>";
            exit();
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Agri Officer Form</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #f4f7fc;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      padding: 30px 0;
    }
    .form-container {
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      width: 900px;
      max-width: 95%;
    }
    .header {
      text-align: center;
      margin-bottom: 25px;
    }
    h1 {
      font-size: 2rem;
      color: #333;
    }
    .form-row {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }
    .form-field {
      flex: 1 1 calc(33.33% - 20px);
      min-width: 200px;
    }
    label {
      font-weight: 600;
      color: #555;
      margin-bottom: 8px;
      text-align: left;
      display: block;
    }
    input[type="text"], input[type="email"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-top: 5px;
      font-size: 1rem;
      background-color: #f9f9f9;
      box-sizing: border-box;
      transition: all 0.3s ease;
    }
    input[type="submit"] {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 12px 25px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
      width: auto;
      transition: background-color 0.3s ease;
      margin-top: 20px;
    }
    input[type="submit"]:hover {
      background-color: #45a049;
    }
    .add-contact-btn {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.9rem;
      margin-bottom: 10px;
      margin-top: -10px;
    }
    .add-contact-btn:hover {
      background-color: #2980b9;
    }
    @media (max-width: 768px) {
      .form-field {
        flex: 1 1 100%;
      }
    }
  </style>

  <script>
    function addContactField() {
      const container = document.getElementById('contactFields');
      const newField = document.createElement('div');
      newField.classList.add('form-field');
      newField.innerHTML = `
          <label>Contact Number</label>
          <input type="text" name="contact[]" placeholder="Enter Contact Number">
      `;
      container.appendChild(newField);
    }
  </script>
</head>

<body>

<div class="form-container">
  <div class="header">
    <h1>Agri Officer Management System</h1>
  </div>

  <form method="POST" action="">

    <div class="form-row">
      <div class="form-field">
        <label for="officer_id">Officer ID</label>
        <input type="text" name="officer_id" id="officer_id" required>
      </div>

      <div class="form-field">
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" id="full_name" required>
      </div>

      <div class="form-field">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label for="road">Road</label>
        <input type="text" name="road" id="road" required>
      </div>

      <div class="form-field">
        <label for="area">Area</label>
        <input type="text" name="area" id="area" required>
      </div>

      <div class="form-field">
        <label for="district">District</label>
        <input type="text" name="district" id="district" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label for="country">Country</label>
        <input type="text" name="country" id="country" required>
      </div>
    </div>

    <div class="form-row" id="contactFields">
      <div class="form-field">
        <label>Contact Number</label>
        <input type="text" name="contact[]" placeholder="Enter Contact Number">
      </div>
    </div>

    <button type="button" class="add-contact-btn" onclick="addContactField()">+ Add Another Contact</button>

    <div class="submit-container">
      <input type="submit" value="Submit">
    </div>

  </form>
</div>

</body>
</html>
