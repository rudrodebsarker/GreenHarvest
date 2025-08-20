<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$retailer_id = $_GET['retailer_id'];

$query = "SELECT * FROM retailer WHERE retailer_id='$retailer_id'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo "invalid";
}

$conn->close();
?>
