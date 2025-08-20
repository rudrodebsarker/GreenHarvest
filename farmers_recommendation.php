<?php 
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get search input
$searchFarmerId = isset($_GET['farmer_id']) ? $_GET['farmer_id'] : '';

// Prepare SQL with optional WHERE clause
$sql = "
    SELECT 
        fao.farmer_id, 
        fao.officer_id, 
        fao.recommendation_info,
        f.name AS farmer_name,
        ao.name AS officer_name
    FROM farmer_agri_officer fao
    INNER JOIN farmer f ON fao.farmer_id = f.farmer_id
    INNER JOIN agri_officer ao ON fao.officer_id = ao.officer_id
";

if (!empty($searchFarmerId)) {
    $sql .= " WHERE fao.farmer_id = '" . mysqli_real_escape_string($conn, $searchFarmerId) . "'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agri Insights - Farmer Agri Officer</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#3498DB',
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

<div class="relative z-10">
    <section id="assignmentSection" class="mt-6">
        <div class="max-w-5xl mx-auto">
            <h1 class="text-4xl font-bold text-center mb-8 text-primary">Farmer Recommendation</h1>

            <!-- Search Form -->
            <form method="GET" class="mb-6 flex justify-center gap-4">
                <input type="text" name="farmer_id" placeholder="Enter Farmer ID"
                       class="border border-gray-300 px-4 py-2 rounded-lg w-64 focus:outline-none focus:ring-2 focus:ring-primary"
                       value="<?php echo htmlspecialchars($searchFarmerId); ?>" />
                <button type="submit"
                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700">Search</button>
            </form>
        
            <!-- Table Display -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-primary">
                <h2 class="text-2xl font-bold mb-4 text-primary">Recommendation List</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left border border-gray-200">
                        <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-2">Farmer ID</th>
                            <th class="px-4 py-2">Farmer Name</th>
                            <th class="px-4 py-2">Officer ID</th>
                            <th class="px-4 py-2">Officer Name</th>
                            <th class="px-4 py-2">Recommendation</th>
                        </tr>
                        </thead>
                        <tbody id="assignmentTableBody" class="bg-white divide-y divide-gray-200">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td class='px-4 py-2'>{$row['farmer_id']}</td>
                                    <td class='px-4 py-2'>{$row['farmer_name']}</td>
                                    <td class='px-4 py-2'>{$row['officer_id']}</td>
                                    <td class='px-4 py-2'>{$row['officer_name']}</td>
                                    <td class='px-4 py-2'>{$row['recommendation_info']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='px-4 py-2 text-center'>No records found.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

</body>
</html>

<?php
$conn->close();
?>