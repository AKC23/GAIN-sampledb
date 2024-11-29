<?php
include('db_connect.php');

$table = 'import_amount_oilseeds';

// Check table structure
$result = $conn->query("SHOW COLUMNS FROM $table");
if ($result) {
    echo "<h3>Table Structure:</h3>";
    while($row = $result->fetch_assoc()) {
        echo htmlspecialchars(print_r($row, true)) . "<br>";
    }
} else {
    echo "Error getting table structure: " . $conn->error;
}

// Check sample data
$result = $conn->query("SELECT * FROM $table LIMIT 1");
if ($result) {
    echo "<h3>Sample Data:</h3>";
    while($row = $result->fetch_assoc()) {
        echo htmlspecialchars(print_r($row, true)) . "<br>";
    }
} else {
    echo "Error getting sample data: " . $conn->error;
}
?>
