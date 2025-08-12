<?php
session_start();

// DB connection
$conn = new mysqli("localhost", "root", "", "shop");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_serial = $_POST['email_or_serial'];
    $password = $_POST['password'];

    // Check in database
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? OR serial = ?");
    $stmt->bind_param("ss", $email_or_serial, $email_or_serial);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Login successful
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            header("Location: coffee.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Email/Serial invalid";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial; background: #f4cfcfff; }
        .container { width: 400px; background: white; padding: 20px; margin: auto; margin-top: 50px; border-radius: 8px; }
        label { font-weight: bold; }
        input { width: 100%; padding: 8px; margin: 8px 0; }
        button { background: blue; color: white; padding: 10px; border: none; cursor: pointer; }
        button:hover { background: darkblue; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error != "") echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <label>Email / Serial</label>
            <input type="text" name="email_or_serial" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">ok</button>
        </form>
    </div>
</body>
</html>
