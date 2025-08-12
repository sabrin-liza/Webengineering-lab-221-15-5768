<?php
$localhost = "localhost";
$username = "root";
$password = "";
$database = "crud";

$connection = mysqli_connect($localhost, $username, $password, $database);

if (mysqli_connect_errno()) {
    echo "Connection failed: " . mysqli_connect_error();
} else {
    echo "Connection established";
}
