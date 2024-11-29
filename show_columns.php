<?php
include('db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

function showTableColumns($conn, $tableName) {
    echo "<h3>Columns in $tableName:</h3>";
    $result = $conn->query("SHOW COLUMNS FROM $tableName");
    
    if ($result) {
        echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach($row as $value) {
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Error getting columns for $tableName: " . $conn->error;
    }
    echo "<hr>";
}

// Show columns for both tables
showTableColumns($conn, 'total_local_crop_production');
showTableColumns($conn, 'crude_oil');

// Also show a sample row from each table
function showSampleData($conn, $tableName) {
    echo "<h3>Sample data from $tableName:</h3>";
    $result = $conn->query("SELECT * FROM $tableName LIMIT 1");
    
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row) {
            echo "<table border='1' style='border-collapse: collapse; margin-bottom: 20px;'>";
            echo "<tr style='background-color: #f2f2f2;'>";
            foreach($row as $key => $value) {
                echo "<th style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($key) . "</th>";
            }
            echo "</tr><tr>";
            foreach($row as $value) {
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr></table>";
        } else {
            echo "No data in table";
        }
    } else {
        echo "Error getting sample data for $tableName: " . $conn->error;
    }
    echo "<hr>";
}

showSampleData($conn, 'total_local_crop_production');
showSampleData($conn, 'crude_oil');
?>
