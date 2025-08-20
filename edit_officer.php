<?php
session_start();

include 'db_config.php'; 

if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];

    // Fetch officer data using prepared statement
    $sql_edit = $conn->prepare("SELECT * FROM agri_officer WHERE officer_id = ?");
    $sql_edit->bind_param('i', $edit_id);
    $sql_edit->execute();
    $result_edit = $sql_edit->get_result();
    $row_edit = $result_edit->fetch_assoc();

    // Fetch contact data associated with the officer
    $sql_contact = $conn->prepare("SELECT contact FROM agri_officer_contact WHERE officer_id = ?");
    $sql_contact->bind_param('i', $edit_id);
    $sql_contact->execute();
    $result_contact = $sql_contact->get_result();
    $contacts = [];
    while ($contact = $result_contact->fetch_assoc()) {
        $contacts[] = $contact['contact'];
    }
    $contacts_str = implode(', ', $contacts); 
}

// Update officer details
if (isset($_POST['update_officer'])) {
    $officer_id = $_POST['officer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $road = $_POST['road'];
    $area = $_POST['area'];
    $district = $_POST['district'];
    $country = $_POST['country'];
    $contact = $_POST['contact']; 

    // Update officer details using prepared statement
    $sql_update = $conn->prepare("UPDATE agri_officer 
                                  SET name = ?, email = ?, road = ?, area = ?, district = ?, country = ? 
                                  WHERE officer_id = ?");
    $sql_update->bind_param('ssssssi', $name, $email, $road, $area, $district, $country, $officer_id);
    $sql_update->execute();

    // Update contact details using prepared statement
    $sql_contact_update = $conn->prepare("INSERT INTO agri_officer_contact (officer_id, contact) VALUES (?, ?) 
                                         ON DUPLICATE KEY UPDATE contact = ?");
    $sql_contact_update->bind_param('iss', $officer_id, $contact, $contact);
    $sql_contact_update->execute();


    header("Location: agriOficers_List.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Agri Officer</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 900px;
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        .input-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 30px; /* Increased gap between fields */
            margin-bottom: 20px;
        }

        .input-wrapper .form-group {
            flex: 1;
            min-width: 250px;
        }

        label {
            margin-bottom: 10px;
            color: #555;
            font-size: 16px;
        }

        input {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #fafafa;
            width: 100%;
        }

        input:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        .back {
            background-color: #007bff;
            color: white;
            margin-top: 10px;
        }

        .back:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Officer</h1>

    <form method="POST" action="">
        <input type="hidden" name="officer_id" value="<?php echo htmlspecialchars($row_edit['officer_id']); ?>">

        <div class="input-wrapper">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($row_edit['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($row_edit['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="road">Road:</label>
                <input type="text" id="road" name="road" value="<?php echo htmlspecialchars($row_edit['road']); ?>" required>
            </div>
        </div>

        <div class="input-wrapper">
            <div class="form-group">
                <label for="area">Area:</label>
                <input type="text" id="area" name="area" value="<?php echo htmlspecialchars($row_edit['area']); ?>" required>
            </div>

            <div class="form-group">
                <label for="District">District:</label>
                <input type="text" id="District" name="District" value="<?php echo htmlspecialchars($row_edit['District']); ?>" required>
            </div>

            <div class="form-group">
                <label for="country">Country:</label>
                <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($row_edit['country']); ?>" required>
            </div>
        </div>

      
        <div class="form-group">
            <label for="contact">Contact:</label>
            <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($contacts_str); ?>" required>
        </div>

        <button type="submit" name="update_officer">Update Officer</button>
    </form>

    <a href="agriOficers_List.php">
        <button class="back" type="button">Back to List</button>
    </a>
</div>

</body>
</html>

<?php
$conn->close();
?>
