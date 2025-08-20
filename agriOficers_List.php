<?php

$conn = mysqli_connect('localhost', 'root', '', 'agriculture');


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


if (isset($_GET['search_id']) && $_GET['search_id'] !== '') {
    $search_id = mysqli_real_escape_string($conn, $_GET['search_id']);
    
    // Fetch officer data based on the provided officer ID
    $query = "SELECT ao.officer_id, ao.name, ao.email, ao.road, ao.area, ao.district, ao.country,
                     GROUP_CONCAT(aoc.contact SEPARATOR ', ') AS contacts
              FROM agri_officer ao
              LEFT JOIN agri_officer_contact aoc ON ao.officer_id = aoc.officer_id
              WHERE ao.officer_id = '$search_id'
              GROUP BY ao.officer_id";
} else {
    // Fetch all officers with their contact details
    $query = "SELECT ao.officer_id, ao.name, ao.email, ao.road, ao.area, ao.district, ao.country,
                     GROUP_CONCAT(aoc.contact SEPARATOR ', ') AS contacts
              FROM agri_officer ao
              LEFT JOIN agri_officer_contact aoc ON ao.officer_id = aoc.officer_id
              GROUP BY ao.officer_id";
}

$result = mysqli_query($conn, $query);

// Close the connection later after HTML
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Agri Officer List</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #f4f7fc;
      padding: 20px;
    }
    h1 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      overflow: hidden;
    }
    th, td {
      padding: 15px 20px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #4CAF50;
      color: white;
      font-weight: 600;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
    .table-container {
      margin-top: 30px;
      overflow-x: auto;
    }
    .search-form {
      margin-bottom: 20px;
      display: flex;
      justify-content: center;
    }
    .search-form input {
      padding: 8px;
      margin-right: 10px;
      width: 200px;
    }
    .no-result {
      color: red;
      font-weight: bold;
      margin-top: 10px;
      text-align: center;
    }
  </style>
</head>

<body>

  <h1>Agri Officer List</h1>

  <!-- Search Form -->
  <form method="GET" action="" class="search-form">
    <label for="search_id">Search by Officer ID:</label>
    <input type="text" id="search_id" name="search_id" placeholder="Enter Officer ID" required>
    <button type="submit" class="btn">Search</button>
  </form>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Officer ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Road</th>
          <th>Area</th>
          <th>District</th>
          <th>Country</th>
          <th>Contact Numbers</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['officer_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                echo "<td>" . htmlspecialchars($row['road']) . "</td>";
                echo "<td>" . htmlspecialchars($row['area']) . "</td>";
                echo "<td>" . htmlspecialchars($row['district']) . "</td>";
                echo "<td>" . htmlspecialchars($row['country']) . "</td>";
                echo "<td>" . htmlspecialchars($row['contacts']) . "</td>";
                echo "<td>
                        <a href='edit_officer.php?edit_id=" . $row['officer_id'] . "'>Edit</a> | 
                        <a href='?delete_id=" . $row['officer_id'] . "' onclick='return confirm(\"Are you sure you want to delete?\")'>Delete</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='9' class='no-result'>No Officers Found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

</body>
</html>

<?php
mysqli_close($conn);
?>
