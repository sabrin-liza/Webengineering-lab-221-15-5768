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

$user_id = $_SESSION['user_id'];
// Get user serial
$res = $conn->query("SELECT serial FROM user WHERE id = $user_id");
$row = $res->fetch_assoc();
$user_serial = $row['serial'];

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $order_id = intval($_GET['delete']);
    // Ensure order belongs to user
    $conn->query("DELETE FROM `order` WHERE id=$order_id AND serial='$user_serial'");
    header("Location: history.php");
    exit();
}

// Handle update (from POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['amount'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $amount = intval($_POST['amount']);
    $status = $conn->real_escape_string($_POST['status']);
    // Validate amount
    if ($amount < 1) $amount = 1;
    // Update order only if belongs to user
    $stmt = $conn->prepare("UPDATE `order` SET amount=?, status=? WHERE id=? AND serial=?");
    $stmt->bind_param("isis", $amount, $status, $order_id, $user_serial);
    $stmt->execute();
    header("Location: history.php");
    exit();
}

// Fetch user orders
$orders_res = $conn->query("SELECT * FROM `order` WHERE serial='$user_serial' ORDER BY order_date DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Order History</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f9f9f9;
    margin: 0; padding: 0;
  }
  nav {
    background: #f4d5cdff;
    color: white;
    display: flex;
    justify-content: space-between;
    padding: 12px 20px;
    align-items: center;
  }
  nav .title {
    font-size: 1.5em;
    font-weight: bold;
  }
  nav .serial {
    font-weight: 600;
    font-size: 1.1em;
  }
  nav a.logout-btn {
    background: #8bdc35ff;
    color: white;
    padding: 6px 12px;
    text-decoration: none;
    border-radius: 4px;
    margin-left: 20px;
    transition: background 0.3s;
  }
  

  main {
    max-width: 900px;
    margin: 30px auto;
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 0 10px #ccc;
  }
  h2 {
    margin-top: 0;
    margin-bottom: 20px;
    text-align: center;
    color: #333;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }
  th, td {
    padding: 12px 10px;
    border-bottom: 1px solid #ddd;
    text-align: center;
    word-wrap: break-word;
  }
  th {
    background: #4d3326ff;
    color: white;
  }
  tr:hover {
    background: #f1f1f1;
  }
  form.inline-form {
    margin: 0;
  }
  input[type="number"] {
    width: 60px;
    padding: 4px;
    font-size: 1em;
    text-align: center;
  }
  select {
    padding: 4px;
    font-size: 1em;
  }
  button {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s;
  }
  button.update-btn {
    background: #121111ff;
    color: white;
  }
  
  button.delete-btn {
    background: #faa5adff;
    color: white;
  }
  
</style>
</head>
<body>

<nav>
  <div class="title">History</div>
  <div>
    <span class="serial"><?php echo htmlspecialchars($user_serial); ?></span>
    <a href="?logout=1" class="logout-btn">Logout</a>
  </div>
</nav>

<main>
  <h2>Order List</h2>
  <?php if ($orders_res->num_rows === 0): ?>
    <p style="text-align:center;">No orders yet.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Item</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while($order = $orders_res->fetch_assoc()): ?>
      <tr>
        <td><?php echo htmlspecialchars(date("Y-m-d H:i", strtotime($order['order_date']))); ?></td>
        <td><?php echo htmlspecialchars($order['item']); ?></td>
        <td>
          <form method="POST" class="inline-form">
            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
            <input type="number" name="amount" value="<?php echo $order['amount']; ?>" min="1" required>
        </td>
        <td>
            <select name="status" required>
              <?php
              $statuses = ["Pending", "Completed", "Cancelled"];
              foreach ($statuses as $status) {
                  $selected = ($order['status'] === $status) ? "selected" : "";
                  echo "<option value=\"$status\" $selected>$status</option>";
              }
              ?>
            </select>
        </td>
        <td>
            <button type="submit" class="update-btn" title="Update">Update</button>
          </form>
          <form method="GET" style="display:inline" onsubmit="return confirm('Delete this order?');">
            <input type="hidden" name="delete" value="<?php echo $order['id']; ?>">
            <button type="submit" class="delete-btn" title="Delete">Delete</button>
          </form>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php endif; ?>
</main>

</body>
</html>
