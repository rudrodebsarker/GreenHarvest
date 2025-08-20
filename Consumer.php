<?php 
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "agriculture";

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Create the consumer table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS consumer (
    consumer_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL
)";
if (!$conn->query($sql)) {
    die("Error creating table: " . $conn->error);
}

// Handle form submission for adding or updating consumer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['update_consumer']) && isset($_POST['edit_id'])) {
        // Update existing consumer
        $consumer_id = $conn->real_escape_string($_POST['consumerId']);
        $name = $conn->real_escape_string($_POST['name']);
        $contact = $conn->real_escape_string($_POST['contact']);
        $email = $conn->real_escape_string($_POST['email']);
        $edit_id = $conn->real_escape_string($_POST['edit_id']);

        $sql = "UPDATE consumer SET 
                consumer_id = '$consumer_id',
                name = '$name', 
                contact = '$contact', 
                email = '$email'
                WHERE consumer_id = '$edit_id'";

        if ($conn->query($sql)) {
            echo "<script>alert('Consumer updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating consumer: " . $conn->error . "');</script>";
        }
    } else {
        // Add new consumer
        $consumer_id = $conn->real_escape_string($_POST['consumerId']);
        $name = $conn->real_escape_string($_POST['name']);
        $contact = $conn->real_escape_string($_POST['contact']);
        $email = $conn->real_escape_string($_POST['email']);

        // Check if consumer ID already exists
        $check = $conn->query("SELECT consumer_id FROM consumer WHERE consumer_id = '$consumer_id'");
        if ($check->num_rows > 0) {
            echo "<script>alert('Consumer ID already exists!');</script>";
        } else {
            $sql = "INSERT INTO consumer (consumer_id, name, contact, email) 
                    VALUES ('$consumer_id', '$name', '$contact', '$email')";

            if ($conn->query($sql)) {
                echo "<script>alert('Consumer added successfully!');</script>";
            } else {
                echo "<script>alert('Error adding consumer: " . $conn->error . "');</script>";
            }
        }
    }
}

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $conn->real_escape_string($_GET['delete_id']);
    $sql = "DELETE FROM consumer WHERE consumer_id = '$delete_id'";
    if ($conn->query($sql)) {
        echo "<script>alert('Consumer deleted successfully!');</script>";
        echo "<script>window.location.href='Consumer_list.php';</script>";
    } else {
        echo "<script>alert('Error deleting consumer: " . $conn->error . "');</script>";
    }
}

// Set up edit mode if edit_id is provided
$editMode = false;
$editData = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $conn->real_escape_string($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM consumer WHERE consumer_id = '$edit_id'");
    if ($result->num_rows > 0) {
        $editData = $result->fetch_assoc();
        $editMode = true;
    }
}

// Calculate statistics
$totalConsumers = 0;
$emailsQuery = $conn->query("SELECT COUNT(DISTINCT email) as email_count, COUNT(*) as consumer_count FROM consumer");
if ($emailsQuery->num_rows > 0) {
    $stats = $emailsQuery->fetch_assoc();
    $totalConsumers = $stats['consumer_count'];
    $activeEmails = $stats['email_count'];
} else {
    $activeEmails = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 40px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .hero-section {
            background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(147, 51, 234, 0.8)),
                        url('https://images.unsplash.com/photo-1556740714-a8395b3bf30f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            height: 250px;
            border-radius: 15px;
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero-section h1 {
            color: white;
            font-size: 2.8em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            z-index: 2;
        }

        .stats-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin: 25px 0;
            display: flex;
            align-items: center;
            gap: 20px;
            border-left: 5px solid #9333ea;
        }

        .stats-icon {
            font-size: 28px;
            background: #9333ea;
            color: white;
            padding: 18px;
            border-radius: 50%;
        }

        .form-container {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-btn {
            background: #6b21a8;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn {
            background: #e74c3c;
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn {
            background: #9333ea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        button {
            background: #9333ea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        input {
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #9333ea;
            box-shadow: 0 0 8px rgba(147, 51, 234, 0.2);
        }

        .decorative-icons {
            position: absolute;
            opacity: 0.1;
            font-size: 100px;
            color: white;
        }
        
        .nav-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="hero-section">
            <i class="fas fa-users decorative-icons icon-left"></i>
            <h1>👥 Consumer Management System</h1>
            <i class="fas fa-shopping-cart decorative-icons icon-right"></i>
        </div>

        <div class="stats-card">
            <i class="fas fa-chart-pie stats-icon"></i>
            <div>
                <h3>Consumer Statistics</h3>
                <p>Total Consumers: <span id="totalConsumers"><?php echo $totalConsumers; ?></span></p>
                <p>Active Emails: <span id="activeEmails"><?php echo $activeEmails; ?></span></p>
            </div>
        </div>

        <div class="form-container">
            <form method="POST" action="">
                <?php if ($editMode): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editData['consumer_id']; ?>">
                <?php endif; ?>
                <div class="form-grid">
                    <div class="input-group">
                        <label><i class="fas fa-id-card"></i>Consumer ID</label>
                        <input type="text" name="consumerId" value="<?php echo $editMode ? $editData['consumer_id'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-user"></i>Name</label>
                        <input type="text" name="name" value="<?php echo $editMode ? $editData['name'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-phone"></i>Contact</label>
                        <input type="tel" name="contact" value="<?php echo $editMode ? $editData['contact'] : ''; ?>" required>
                    </div>
                    <div class="input-group">
                        <label><i class="fas fa-envelope"></i>Email</label>
                        <input type="email" name="email" value="<?php echo $editMode ? $editData['email'] : ''; ?>" required>
                    </div>
                </div>
                <div class="btn-container">
                    <?php if ($editMode): ?>
                        <button type="submit" name="update_consumer" class="btn"><i class="fas fa-edit"></i>Update Consumer</button>
                    <?php else: ?>
                        <button type="submit" class="btn"><i class="fas fa-user-plus"></i>Submit Consumer Data</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="nav-buttons">
            <a href="Consumer_list.php" class="btn"><i class="fas fa-arrow-right"></i> Next Page</a>
        </div>
    </div>
</body>
</html>
