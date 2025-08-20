<?php
// Start session (only if not already started)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
$db = mysqli_connect('localhost', 'root', '', 'agriculture');
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get products for display and feedback dropdown
$products_query = "SELECT * FROM agri_product";
$products_result = mysqli_query($db, $products_query);
$products = mysqli_fetch_all($products_result, MYSQLI_ASSOC);

// Initialize variables
$consumer_data = null;
$current_section = isset($_GET['section']) ? $_GET['section'] : 'products';

// Handle consumer ID search
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['consumer_id'])) {
    $current_section = 'profile';
    $consumer_id = (int)$_POST['consumer_id'];
    $consumer_query = "SELECT * FROM consumer WHERE consumer_id = $consumer_id";
    $consumer_result = mysqli_query($db, $consumer_query);
    $consumer_data = mysqli_fetch_assoc($consumer_result);
    
    if (!$consumer_data) {
        $_SESSION['profile_error'] = "No consumer found with ID: $consumer_id";
    }
}

// Feedback Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_text'])) {
    $current_section = 'feedback';
    $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $feedback_text = trim($_POST['feedback_text']);
    
    // Validation
    if (empty($feedback_text)) {
        $_SESSION['feedback_error'] = "Feedback text cannot be empty";
    } elseif (strlen($feedback_text) > 1000) {
        $_SESSION['feedback_error'] = "Feedback is too long (max 1000 characters)";
    } else {
        // Get product name if product_id is selected
        $product_name = 'General Feedback';
        if ($product_id) {
            foreach ($products as $product) {
                if ($product['product_id'] == $product_id) {
                    $product_name = $product['name'];
                    break;
                }
            }
        }
        
        // Store feedback in JSON file
        $feedback_data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'product_id' => $product_id,
            'product_name' => $product_name,
            'feedback' => $feedback_text
        ];
        
        $file = 'feedback_data.json';
        $all_feedback = [];
        
        if (file_exists($file)) {
            $all_feedback = json_decode(file_get_contents($file), true);
            if (!is_array($all_feedback)) {
                $all_feedback = [];
            }
        }
        
        $all_feedback[] = $feedback_data;
        file_put_contents($file, json_encode($all_feedback, JSON_PRETTY_PRINT));
        
        $_SESSION['feedback_success'] = "Thank you for your feedback!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricultural Products Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
    /* General Reset & Body Styling */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      font-family: 'Poppins', sans-serif;
      background-color: #f0f2f5;
      color: #333;
      height: 100%;
    }

    /* The sidebar itself */
    .navbar {
      width: 250px;
      height: 100vh;
      position: fixed;
      top: 0;
      left: 0;
      background: linear-gradient(180deg, #2c3e50, #34495e);
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
      display: flex;
      flex-direction: column;
      z-index: 1000;
      overflow-y: auto;
    }

    /* Logo and branding at the top of the sidebar */
    .navbar-left {
      padding: 25px 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .navbar .logo-img {
      height: 80px;
      width: 80px;
      border-radius: 50%;
      border: 3px solid #ecf0f1;
      object-fit: cover;
    }

    .navbar .logo {
      font-size: 1.4rem;
      font-weight: 600;
      color: #ecf0f1;
      text-decoration: none;
      text-align: center;
    }

    /* Navigation links container */
    .nav-links {
      list-style: none;
      padding: 0;
      margin: 0;
      flex-grow: 1; /* Pushes logout to the bottom */
      display: flex;
      flex-direction: column;
    }

    .nav-links li {
      width: 100%;
    }

    /* Individual navigation links */
    .nav-links a, .nav-links button {
      display: block;
      padding: 16px 30px;
      color: #ecf0f1;
      text-decoration: none;
      font-size: 1rem;
      font-weight: 500;
      transition: background 0.3s ease, color 0.3s ease, padding-left 0.3s ease;
      border-left: 5px solid transparent;
      background: none;
      border: none;
      cursor: pointer;
      width: 100%;
      text-align: left;
      font-family: 'Poppins', sans-serif;
    }

    .nav-links a:hover,
    .nav-links button:hover,
    .nav-links li.active a, 
    .nav-links li.active button {
      background: #3498db;
      color: #fff;
      padding-left: 35px;
      border-left-color: #ecf0f1;
    }

    /* Logout Button */
    #Logout a, #Logout button {
      background-color: rgba(231, 76, 60, 0.8);
      border-left: 5px solid transparent;
    }

    #Logout a:hover, #Logout button:hover {
      background-color: #e74c3c;
      border-left-color: #c0392b;
      padding-left: 35px;
    }

    /* Main Content Area */
    .main-content {
      margin-left: 250px; /* Same as sidebar width */
      padding: 40px;
      transition: margin-left 0.3s ease;
    }
    
    .container {
        padding: 0;
    }

    header {
        background-color: #fff;
        color: #2c3e50;
        padding: 20px;
        text-align: center;
        margin-bottom: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    h1 {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .product-card {
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .product-image {
        height: 180px;
        background-color: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-size: 1.2rem;
    }

    .product-info {
        padding: 15px;
    }

    .section {
        display: none;
    }

    .section.active {
        display: block;
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 20px 0;
      margin-top: 40px;
      color: #95a5a6;
    }
    
    .menu-toggle {
        display: none;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
      .navbar {
        width: 100%;
        height: auto;
        position: relative;
        flex-direction: row;
        justify-content: space-between;
        padding: 0 20px;
      }
      .navbar-left {
        flex-direction: row;
        border-bottom: none;
        padding: 10px 0;
      }
      .logo {
        margin-left: 15px;
      }
      .nav-links {
        display: none; /* Hide links for a mobile toggle */
        flex-direction: column;
        width: 100%;
        position: absolute;
        top: 70px;
        left: 0;
        background: #34495e;
      }
      .main-content {
        margin-left: 0;
        padding: 20px;
      }
      .menu-toggle {
        display: block; /* Show hamburger */
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
      }
      .nav-links.active {
        display: flex;
      }
      #Logout {
        margin-top: 0;
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-left">
      <img class="logo-img" src="images/pic2.png" alt="Logo">
      <a href="#" class="logo">GreenHarvest</a>
    </div>
    <ul class="nav-links">
        <li class="<?= $current_section === 'products' ? 'active' : '' ?>"><button onclick="showSection('products', event)">Products</button></li>
        <li class="<?= $current_section === 'profile' ? 'active' : '' ?>"><button onclick="showSection('profile', event)">Profile</button></li>
        <li class="<?= $current_section === 'feedback' ? 'active' : '' ?>"><button onclick="showSection('feedback', event)">Feedback</button></li>
        <li id="Logout"><a href="logout.php">Logout</a></li>
    </ul>
    <button class="menu-toggle" id="menu-toggle">&#9776;</button>
  </nav>

  <!-- Main Content -->
  <div class="main-content">
        <!-- Products Section -->
        <div id="products" class="section <?= $current_section === 'products' ? 'active' : '' ?>">
            <header>
                <h1>Agricultural Products</h1>
                <p>Find fresh produce from local farmers</p>
            </header>

            <div class="search-container">
                <input type="text" id="search-input" placeholder="Search for products (e.g., Tomato, Rice, Mango)...">
            </div>

            <div class="products-grid" id="products-container">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php 
                                $emoji = '🌱';
                                if ($product['type'] === 'Fruit') $emoji = '🍎';
                                elseif ($product['type'] === 'Vegetable') $emoji = '🥦';
                                elseif ($product['type'] === 'Cereal') $emoji = '🌾';
                                echo $emoji . ' ' . htmlspecialchars($product['name']);
                            ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                            <span class="product-type"><?= htmlspecialchars($product['type']) ?></span>
                            <p class="product-season">Season: <?= htmlspecialchars($product['seasonality']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Profile Section -->
        <div id="profile" class="section <?= $current_section === 'profile' ? 'active' : '' ?>">
            <header>
                <h1>Consumer Profile</h1>
                <p>View consumer details by entering consumer ID</p>
            </header>

            <div class="consumer-search">
                <h2>Find Consumer</h2>
                <?php if (isset($_SESSION['profile_error'])): ?>
                    <div class="profile-error">
                        <?= $_SESSION['profile_error'] ?>
                        <?php unset($_SESSION['profile_error']); ?>
                    </div>
                <?php endif; ?>
                <form method="post" action="?section=profile">
                    <label for="consumer-id">Enter Consumer ID:</label>
                    <input type="number" id="consumer-id" name="consumer_id" required>
                    <button type="submit" class="btn">Search</button>
                </form>
            </div>

            <?php if ($consumer_data): ?>
            <div class="consumer-details">
                <h2>Consumer Details</h2>
                <p><strong>Consumer ID:</strong> <?= htmlspecialchars($consumer_data['consumer_id']) ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($consumer_data['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($consumer_data['email']) ?></p>
                <p><strong>Contact:</strong> <?= htmlspecialchars($consumer_data['contact']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Feedback Section -->
        <div id="feedback" class="section <?= $current_section === 'feedback' ? 'active' : '' ?>">
            <header>
                <h1>Provide Feedback</h1>
                <p>Share your thoughts about our products</p>
            </header>

            <div class="feedback-form">
                <?php if (isset($_SESSION['feedback_success'])): ?>
                    <div class="success-message">
                        <?= $_SESSION['feedback_success'] ?>
                        <?php unset($_SESSION['feedback_success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['feedback_error'])): ?>
                    <div class="error-message">
                        <?= $_SESSION['feedback_error'] ?>
                        <?php unset($_SESSION['feedback_error']); ?>
                    </div>
                <?php endif; ?>
                
                <h2>Feedback Form</h2>
                <form method="post" action="?section=feedback">
                    <div style="margin-bottom: 15px;">
                        <label for="product-select">Select Product (optional):</label>
                        <select name="product_id" id="product-select" style="width: 100%; padding: 8px; margin-top: 5px;">
                            <option value="">-- Select a product --</option>
                            <?php foreach ($products as $product): ?>
                                <option value="<?= $product['product_id'] ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="feedback-text">Your Feedback:</label>
                        <textarea name="feedback_text" id="feedback-text" placeholder="Please share your feedback here..." required></textarea>
                    </div>
                    <button type="submit">Submit Feedback</button>
                </form>
            </div>
        </div>
        <footer>
            <p>&copy; 2025 Consumer Dashboard | GreenHarvest</p>
        </footer>
  </div>

    <script>
        // Convert PHP products array to JavaScript
        const products = <?= json_encode($products) ?>;
        const productsContainer = document.getElementById('products-container');
        const searchInput = document.getElementById('search-input');

        // Add event listener for search input
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredProducts = products.filter(product => 
                product.name.toLowerCase().includes(searchTerm)
            );
            
            // Clear current products
            productsContainer.innerHTML = '';
            
            if (filteredProducts.length === 0) {
                productsContainer.innerHTML = `
                    <div class="no-results">
                        <p>No products found matching your search.</p>
                    </div>
                `;
                return;
            }
            
            // Display filtered products
            filteredProducts.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                
                // Get emoji based on product type
                let emoji = '🌱';
                if (product.type === 'Fruit') emoji = '🍎';
                else if (product.type === 'Vegetable') emoji = '🥦';
                else if (product.type === 'Cereal') emoji = '🌾';
                
                productCard.innerHTML = `
                    <div class="product-image">${emoji} ${product.name}</div>
                    <div class="product-info">
                        <h3 class="product-name">${product.name}</h3>
                        <span class="product-type">${product.type}</span>
                        <p class="product-season">Season: ${product.seasonality}</p>
                    </div>
                `;
                
                productsContainer.appendChild(productCard);
            });
        });

        // Section navigation
        function showSection(sectionId, event) {
            // Update URL without reloading
            history.pushState(null, null, `?section=${sectionId}`);
            
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.add('active');
            
            // Update active button in nav
            document.querySelectorAll('.nav-links li').forEach(li => {
                li.classList.remove('active');
            });
            event.target.parentElement.classList.add('active');
        }

        // Show section based on URL parameter
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const sectionParam = urlParams.get('section') || 'products';
            
            if (['products', 'profile', 'feedback'].includes(sectionParam)) {
                document.getElementById(sectionParam).classList.add('active');
                document.querySelector(`.nav-links button[onclick*="${sectionParam}"]`).parentElement.classList.add('active');
            }
        });

        // Mobile menu toggle
        const menuToggle = document.getElementById('menu-toggle');
        const navLinks = document.querySelector('.nav-links');
        if(menuToggle) {
          menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
          });
        }
    </script>
</body>
</html>
<?php
// Close database connection
mysqli_close($db);
?>
