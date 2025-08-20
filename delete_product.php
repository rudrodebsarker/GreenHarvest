<?php
session_start();

// Connect to the database
$db = mysqli_connect('localhost', 'root', '', 'agriculture');

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the product_id is set
if (isset($_GET['product_id'])) {
    $product_id = mysqli_real_escape_string($db, $_GET['product_id']);
    
    // Delete the product from the database
    $delete_query = "DELETE FROM agri_product WHERE product_id='$product_id'";
    
    if (mysqli_query($db, $delete_query)) {
        // Redirect to the product list page on successful deletion
        echo "<script>window.location.href='agriProduct_list.php';</script>";
    } else {
        echo "Error: " . mysqli_error($db);
    }
}

mysqli_close($db);
?>
