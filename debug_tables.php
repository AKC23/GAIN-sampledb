<?php
include('db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to display table contents
function displayTable($conn, $tableName) {
    echo "<h2>$tableName Table Contents:</h2>";
    $result = $conn->query("SELECT * FROM $tableName");
    if ($result) {
        echo "<table border='1' style='margin-bottom: 20px;'>";
        
        // Get and display column headers
        $fields = $result->fetch_fields();
        echo "<tr>";
        foreach ($fields as $field) {
            echo "<th>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        
        // Display data
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Show row count
        echo "<p>Total rows: " . $result->num_rows . "</p>";
    } else {
        echo "Error querying $tableName table: " . $conn->error;
    }
    echo "<hr>";
}

// Display contents of each reference table
displayTable($conn, 'FoodType');
displayTable($conn, 'FoodVehicle');
displayTable($conn, 'raw_crops');

?>
