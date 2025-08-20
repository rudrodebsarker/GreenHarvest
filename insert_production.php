<?php
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'agriculture'); // Ensure the correct database credentials

// Check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and sanitize form data
$production_id = mysqli_real_escape_string($conn, $_POST['production_id']);
$yield = mysqli_real_escape_string($conn, $_POST['yield']);
$acreage = mysqli_real_escape_string($conn, $_POST['acreage']);
$cost = mysqli_real_escape_string($conn, $_POST['cost']);
$per_acre_seeds_requirement = mysqli_real_escape_string($conn, $_POST['per_acre_seeds_requirement']);
$seeding_date = mysqli_real_escape_string($conn, $_POST['seeding_date']);
$harvesting_date = mysqli_real_escape_string($conn, $_POST['harvesting_date']);
$data_input_date = mysqli_real_escape_string($conn, $_POST['data_input_date']);
$farmer_id = mysqli_real_escape_string($conn, $_POST['farmer_id']);
$officer_id = mysqli_real_escape_string($conn, $_POST['officer_id']);
$product_id = mysqli_real_escape_string($conn, $_POST['product_id']);

// Ensure the required fields are not empty
if (empty($yield) || empty($acreage) || empty($cost) || empty($seeding_date) || empty($farmer_id) || empty($officer_id) || empty($product_id)) {
    echo "<script>alert('All fields are required!');</script>";
    exit;
}

// Check if the production_id already exists
$check_sql = "SELECT * FROM production_data WHERE production_id = '$production_id'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    // If production_id already exists, show an error message via a pop-up
    echo "<script>alert('Error: Production ID \"$production_id\" already exists. Please use a unique ID.');</script>";
    exit;
}

// SQL query to insert data
$sql = "INSERT INTO production_data (production_id, yield, acreage, cost, per_acre_seeds_requirement, seeding_date, harvesting_date, data_input_date, farmer_id, officer_id, product_id) 
        VALUES ('$production_id', '$yield', '$acreage', '$cost', '$per_acre_seeds_requirement', '$seeding_date', '$harvesting_date', '$data_input_date', '$farmer_id', '$officer_id', '$product_id')";

// Execute the query
if (mysqli_query($conn, $sql)) {
    // If successful, show a success message via a pop-up
    echo "<script>alert('Production data inserted successfully!');</script>";
} else {
    echo "<script>alert('Error inserting data: " . mysqli_error($conn) . "');</script>";
}

// Close the connection
mysqli_close($conn);
?>
