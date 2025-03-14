<?php

$sql = "
SELECT 
    ft.FoodTypeID,
    ft.FoodTypeName,
    fv.VehicleName
FROM 
    foodtype ft
LEFT JOIN 
    foodvehicle fv ON ft.VehicleID = fv.VehicleID
ORDER BY 
    ft.FoodTypeID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    // Fetch and display table headers
    while ($fieldInfo = $result->fetch_field()) {
        if ($fieldInfo->name == 'VehicleID') {
            echo "<th>VehicleName</th>";
        } else {
            echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
        }
    }
    echo "</tr></thead><tbody>";
    // Fetch and display table rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $cell) {
            if ($key == 'VehicleID') {
                // Fetch the VehicleName based on VehicleID
                $vehicleResult = $conn->query("SELECT VehicleName FROM foodvehicle WHERE VehicleID = $cell");
                if ($vehicleRow = $vehicleResult->fetch_assoc()) {
                    echo "<td>" . htmlspecialchars($vehicleRow['VehicleName']) . "</td>";
                } else {
                    echo "<td>N/A</td>";
                }
            } else {
                echo "<td>" . htmlspecialchars($cell) . "</td>";
            }
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo 'No records found';
}

$conn->close();
?>
