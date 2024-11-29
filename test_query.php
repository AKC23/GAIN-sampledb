<?php
include('db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "
SELECT 
    t.DataID,
    t.FoodTypeID,
    ft.FoodTypeName,
    t.VehicleID,
    fv.VehicleName
FROM total_local_production_amount_edible_oil t
LEFT JOIN FoodType ft ON t.FoodTypeID = ft.FoodTypeID
LEFT JOIN FoodVehicle fv ON ft.VehicleID = fv.VehicleID
LIMIT 5";

$result = $conn->query($query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>DataID</th><th>FoodTypeID</th><th>FoodTypeName</th><th>VehicleID</th><th>VehicleName</th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['DataID'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['FoodTypeID'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['FoodTypeName'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['VehicleID'] ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($row['VehicleName'] ?? '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show the SQL query for debugging
    echo "<pre>";
    echo htmlspecialchars($query);
    echo "</pre>";
} else {
    echo "Error: " . $conn->error;
}
?>
