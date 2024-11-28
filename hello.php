<?php
// Connect to MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sampledb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicleName = $_POST['vehicleName'];

    // Insert data into FoodVehicle table
    $sql = "INSERT INTO FoodVehicle (VehicleName) VALUES ('$vehicleName')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Vehicle</title>
</head>
<body>
    <h2>Insert a New Vehicle</h2>
    <form method="post" action="">
        <label for="vehicleName">Vehicle Name:</label><br>
        <input type="text" id="vehicleName" name="vehicleName" required><br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
