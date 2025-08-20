<?php
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'agriculture');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $officer_id = $_POST['officer_id'];
    $farmer_id = $_POST['farmer_id'];
    $recommendation = $_POST['recommendation'];
    $joining_date = $_POST['joining_date'];
    $transfer_date = $_POST['transfer_date'] ?: "NULL";

    // Check if the entry already exists
    $check_sql = "SELECT * FROM farmer_agri_officer WHERE officer_id = '$officer_id' AND farmer_id = '$farmer_id'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        echo "<p style='color:red;'>This officer and farmer combination already exists.</p>";
    } else {
        // Insert data into the database
        $sql = "INSERT INTO farmer_agri_officer (officer_id, farmer_id, recommendation_info, joining_date, transfer_date)
                VALUES ('$officer_id', '$farmer_id', '$recommendation', '$joining_date', " . ($transfer_date === "NULL" ? "NULL" : "'$transfer_date'") . ")";

        if ($conn->query($sql) === TRUE) {
            echo "<p style='color:green;'>Record inserted successfully.</p>";
        } else {
            echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    }
}

// Fetch dropdown data
$officers = mysqli_query($conn, "SELECT officer_id, name FROM agri_officer");
$farmers = mysqli_query($conn, "SELECT farmer_id, name FROM farmer");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Recommendation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-2xl mx-auto bg-white shadow-lg p-8 rounded-xl">
        <h2 class="text-2xl font-bold mb-6 text-primary">Add Farmer Recommendation</h2>
        <form method="POST">
            <!-- Officer ID -->
            <label class="block mb-2 font-semibold">Select Officer:</label>
            <select name="officer_id" class="w-full border px-4 py-2 rounded mb-4">
                <?php while ($row = mysqli_fetch_assoc($officers)): ?>
                    <option value="<?= $row['officer_id'] ?>"><?= $row['officer_id'] ?> - <?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <!-- Farmer ID -->
            <label class="block mb-2 font-semibold">Select Farmer:</label>
            <select name="farmer_id" class="w-full border px-4 py-2 rounded mb-4">
                <?php while ($row = mysqli_fetch_assoc($farmers)): ?>
                    <option value="<?= $row['farmer_id'] ?>"><?= $row['farmer_id'] ?> - <?= $row['name'] ?></option>
                <?php endwhile; ?>
            </select>

            <!-- Recommendation Info -->
            <label class="block mb-2 font-semibold">Recommendation:</label>
            <textarea name="recommendation" rows="4" class="w-full border px-4 py-2 rounded mb-4" placeholder="Enter recommendation info..."></textarea>

            <!-- Joining Date -->
            <label class="block mb-2 font-semibold">Joining Date:</label>
            <input type="date" name="joining_date" class="w-full border px-4 py-2 rounded mb-4" required>

            <!-- Transfer Date -->
            <label class="block mb-2 font-semibold">Transfer Date (optional):</label>
            <input type="date" name="transfer_date" class="w-full border px-4 py-2 rounded mb-4">

            <!-- Colorful Submit Button -->
            <button type="submit" class="bg-gradient-to-r from-blue-500 via-green-500 to-yellow-500 text-white px-6 py-3 rounded-full font-semibold hover:from-pink-500 hover:to-purple-500 transition duration-300">
                Submit
            </button>
        </form>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
