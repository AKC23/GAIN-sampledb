<?php
// db_connect.php
// $servername = "localhost";
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "sampledb";


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>




