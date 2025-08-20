<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "agriculture"; // Replace with your DB name

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the record from the database
    $delete_query = "DELETE FROM production_data WHERE production_id = $id";

    if ($conn->query($delete_query) === TRUE) {
        echo "<script>alert('Record deleted successfully!'); window.location.href='production_list.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "No ID provided!";
}

$conn->close();
?>
