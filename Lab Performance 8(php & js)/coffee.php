<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "", "shop");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user serial
$user_id = $_SESSION['user_id'];
$res = $conn->query("SELECT serial FROM user WHERE id = $user_id");
$row = $res->fetch_assoc();
$user_serial = $row['serial'];

// Create order table if not exists
$conn->query("
CREATE TABLE IF NOT EXISTS `order` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serial VARCHAR(20) NOT NULL,
    item VARCHAR(100) NOT NULL,
    amount INT NOT NULL,
    order_date DATETIME NOT NULL
)
");

// Handle order form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item'], $_POST['amount'])) {
    $item = $_POST['item'];
    $amount = intval($_POST['amount']);
    $date = date("Y-m-d H:i:s");

    $stmt = $conn->prepare("INSERT INTO `order` (serial, item, amount, order_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $user_serial, $item, $amount, $date);
    $stmt->execute();

    // Removed redirect so user stays on this page after ordering
    // header("Location: history.php");
    // exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Coffee Time</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="stylesheet" href="feedback.css" />
  <style>
    .amount-control {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 8px 0;
    }
    .amount-btn {
        background-color: #2d1403ff;
        color: white;
        border: none;
        padding: 6px 10px;
        font-size: 18px;
        cursor: pointer;
        border-radius: 4px;
        margin: 0 4px;
        transition: background 0.2s;
    }
    
    .amount-input {
        width: 40px;
        text-align: center;
        border: 1px solid #ccc;
        padding: 5px;
        font-size: 16px;
        border-radius: 4px;
    }
    .order-btn {
        background-color: #f1c1acff;
        color: white;
        padding: 6px 12px;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background 0.2s;
    }
    .order-btn:hover {
        background-color: #218838;
    }
    /* Make cart clickable like a button */
    .cart {
        cursor: pointer;
        font-size: 24px;
        margin-left: 15px;
        position: relative;
        color: inherit;
        text-decoration: none;
        user-select: none;
    }
    .cart sub {
        position: absolute;
        top: -8px;
        right: -12px;
        background: red;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
        font-weight: bold;
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav>
    <div class="logo">Coffee Time</div>

    <div class="dropdown">
      <a href="#">Items</a>
      <div class="dropdown-content">
        <a href="#">espresso</a>
        <a href="#">cappichino</a>
        <a href="#">latte</a>
        <a href="#">mocha</a>
        
      </div>
    </div>

    <div class="dropdown">
      <a href="#">Offers</a>
      <div class="dropdown-content">
        <a href="#">Discounts</a>
        
      </div>
    </div>

    <a href="#">Account</a>

    <div class="search-bar">
      <input type="text" placeholder="Item name" />
      <button>Search</button>
    </div>

    
    <a href="history.php" class="cart" title="View  order ">🛒<sub>0</sub></a>

    <!-- ✅ Added welcome message -->
    <div style="margin-left: 15px; font-weight: bold;">
        Welcome, <?php echo htmlspecialchars($user_serial); ?>
    </div>
  </nav>

  <!-- Banner -->
  <div class="banner">
    <img src="offer.avif" alt="offer" style="width: 100%; height: 100%;" />
    <button class="special-btn">check</button>
  </div>

  <!-- Products -->
  <main class="product-grid">
    <?php
    $products = [
        ["Espresso", "1.jpg", "24.99"],
        ["capacinno", "2.jpg", "29.99"],
        ["latte", "3.jpg", "19.99"],
        
    ];
    foreach ($products as $index => $p) {
        echo '
        <div class="product-card">
            <img src="'.$p[1].'" alt="'.$p[0].'" />
            <h3>'.$p[0].'</h3>
            <p>$'.$p[2].'</p>
            <form method="POST">
                <input type="hidden" name="item" value="'.$p[0].'">
                <div class="amount-control">
                    <button type="button" class="amount-btn" onclick="changeAmount(\'amount'.$index.'\', -1)">-</button>
                    <input type="text" id="amount'.$index.'" name="amount" value="1" class="amount-input" readonly>
                    <button type="button" class="amount-btn" onclick="changeAmount(\'amount'.$index.'\', 1)">+</button>
                </div>
                <button type="submit" class="order-btn">Order</button>
            </form>
        </div>
        ';
    }
    ?>
  </main>

  <main class="product-grid">
    <?php
    foreach ($products as $index => $p) {
        $i2 = $index + 10; // unique ID for inputs
        echo '
        <div class="product-card">
            <img src="'.$p[1].'" alt="'.$p[0].'" />
            <h3>'.$p[0].'</h3>
            <p>$'.$p[2].'</p>
            <form method="POST">
                <input type="hidden" name="item" value="'.$p[0].'">
                <div class="amount-control">
                    <button type="button" class="amount-btn" onclick="changeAmount(\'amount'.$i2.'\', -1)">-</button>
                    <input type="text" id="amount'.$i2.'" name="amount" value="1" class="amount-input" readonly>
                    <button type="button" class="amount-btn" onclick="changeAmount(\'amount'.$i2.'\', 1)">+</button>
                </div>
                <button type="submit" class="order-btn">Order</button>
            </form>
        </div>
        ';
    }
    ?>
  </main>

  <!-- Feedback Button -->
  <div class="feedback-trigger">
    <button onclick="openFeedbackForm()">Feedback</button>
  </div>

  <!-- Feedback Modal -->
  <div id="feedbackForm" class="feedback-container">
    <div class="form-box">
      <h2>We value your feedback 💬</h2>
      <input type="text" placeholder="Name" required />
      <input type="email" placeholder="Email" required />

      <select required>
        <option value="" disabled selected>Select Gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option>
        <option value="Other">Other</option>
      </select>

      <input type="number" placeholder="Your Age" min="1" max="120" required />

      <select>
        <option selected disabled>Favorite Food Item</option>
        <option value="Coffee">Coffee</option>
        <option value="Pizza">Pizza</option>
        <option value="Burger">Burger</option>
        <option value="Pasta">Pasta</option>
        <option value="Other">Other</option>
      </select>

      <textarea placeholder="Your Address..." required></textarea>
      <textarea placeholder="Write your feedback..." required></textarea>

      <div class="rating">
        <span>Rate Us:</span>
        <span class="stars" onclick="setRating(event)">
          <i data-value="1">★</i>
          <i data-value="2">★</i>
          <i data-value="3">★</i>
          <i data-value="4">★</i>
          <i data-value="5">★</i>
        </span>
      </div>

      <div class="form-actions">
        <button onclick="submitFeedback()">Submit</button>
        <button onclick="closeFeedbackForm()">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>Thanks!</p>
  </footer>

  <script>
    function changeAmount(id, delta) {
        let input = document.getElementById(id);
        let value = parseInt(input.value) + delta;
        if (value < 1) value = 1;
        input.value = value;
    }
  </script>

  <script src="feedback.js"></script>
</body>
</html>
