<?php
session_start();

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle the form submission
if (isset($_POST['add_product'])) {
    $product_id = mysqli_real_escape_string($db, $_POST['product_id']);
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $seasonality = mysqli_real_escape_string($db, $_POST['seasonality']);
    $type = mysqli_real_escape_string($db, $_POST['type']);

    // Check if the product ID already exists
    $check_query = "SELECT * FROM agri_product WHERE product_id='$product_id'";
    $result = mysqli_query($db, $check_query);

    if (mysqli_num_rows($result) > 0) {
        // If product ID exists, show an error
        echo "<script>alert('Product ID already exists. Please choose a different ID.');</script>";
    } else {
        // If product ID does not exist, insert the new product
        $query = "INSERT INTO agri_product (product_id, name, seasonality, type) 
                  VALUES ('$product_id', '$name', '$seasonality', '$type')";
        if (mysqli_query($db, $query)) {
            // Redirect to the product list page on successful insert
            echo "<script>window.location.href='agriProduct_list.php';</script>";
        } else {
            echo "Error: " . mysqli_error($db);
        }
    }
}

mysqli_close($db);
?>
