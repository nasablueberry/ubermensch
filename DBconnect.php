<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ubermench";  // your database name

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
?>
