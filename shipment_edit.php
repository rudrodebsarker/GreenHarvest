<?php
include 'db_config.php';

if (isset($_GET['id'])) {
    $shipment_id = $_GET['id'];

    // Fetch shipment details for the given shipment_id
    $sql = "SELECT * FROM SHIPMENT WHERE shipment_id = '$shipment_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Shipment not found!";
        exit;
    }
}

// Handle form submission (Update)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $shipment_id = $_POST['shipment_id'];
    $ship_date = $_POST['ship_date'];
    $warehouse_id = $_POST['warehouse_id'];

    $sql = "UPDATE SHIPMENT SET ship_date = '$ship_date', warehouse_id = '$warehouse_id' WHERE shipment_id = '$shipment_id'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Shipment updated successfully.'); window.location.href='shipment_list.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Shipment</title>
    <style>
        /* Your styling from previous page */
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Shipment</h1>

    <form method="POST" action="shipment_edit.php?id=<?php echo $shipment_id; ?>">
        <label for="shipment_id">Shipment ID:</label>
        <input type="text" id="shipment_id" name="shipment_id" value="<?php echo $row['shipment_id']; ?>" readonly><br><br>

        <label for="ship_date">Ship Date:</label>
        <input type="date" id="ship_date" name="ship_date" value="<?php echo $row['ship_date']; ?>" required><br><br>

        <label for="warehouse_id">Warehouse ID:</label>
        <select id="warehouse_id" name="warehouse_id" required>
            <?php
            // Fetch all warehouse IDs from the WAREHOUSE table
            $sql = "SELECT warehouse_id FROM WAREHOUSE";
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['warehouse_id'] . "' " . ($row['warehouse_id'] == $row['warehouse_id'] ? "selected" : "") . ">" . $row['warehouse_id'] . "</option>";
            }
            ?>
        </select><br><br>

        <button type="submit" name="update">Update Shipment</button>
    </form>
</div>

</body>
</html>

<?php
$conn->close();
?>
