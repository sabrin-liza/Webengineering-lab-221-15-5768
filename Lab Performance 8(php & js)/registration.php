<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "shop");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$conn->query("
CREATE TABLE IF NOT EXISTS user (    id INT AUTO_INCREMENT PRIMARY KEY,    serial VARCHAR(20),    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    birthdate DATE NOT NULL,
    password VARCHAR(255) NOT NULL
)
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert without serial first
    $stmt = $conn->prepare("INSERT INTO user (name, email, birthdate, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $birthdate, $password);
    $stmt->execute();

    // Get inserted id
    $id = $stmt->insert_id;
    $birthYear = date("Y", strtotime($birthdate));

    $serial = $birthYear . "-" . $id;

    // Update serial
    $conn->query("UPDATE user SET serial='$serial' WHERE id=$id");

    //  to login page
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <style>
        body { font-family: Arial; background: #f9d6d6ff; }
        .container { width: 400px; background: white; padding: 20px; margin: auto; margin-top: 50px; border-radius: 8px; }
        label { font-weight: bold; }
        input { width: 100%; padding: 8px; margin: 8px 0; }
        button { background: green; color: white; padding: 10px; border: none; cursor: pointer; }
        button:hover { background: darkgreen; }
    </style>
    <script>
        function validateForm() {
            let email = document.getElementById("email").value;
            let password = document.getElementById("password").value;

            // Email pattern for DIU
            let emailPattern = /^[a-zA-Z0-9._%+-]+@diu\.edu\.bd$/;
            if (!emailPattern.test(email)) {
                alert("Email must be of your university Mail:");
                return false;
            }

            // Password pattern
            let passPattern = /^[a-zA-Z0-9@_]{9,}$/;
            if (!passPattern.test(password)) {
                alert("Password must be minimum lenght 9 characters and contain only letters, numbers, @, _");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Registration Form</h2>
        <form method="POST" onsubmit="return validateForm()">
            <label>Name</label>
            <input type="text" name="name" required>

            <label>DIU Mail</label>
            <input type="email" id="email" name="email" required>

            <label>Birthdate</label>
            <input type="date" name="birthdate" required>

            <label>Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Ok</button>
        </form>
    </div>
</body>
</html>
