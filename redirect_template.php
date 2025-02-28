<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tableName = $_POST["tableName"];
    if ($tableName == "foodvehicle") {
        header("Location: input_foodvehicle.php");
        exit();
    }
    // Add more conditions for other tables as needed
}
?>
