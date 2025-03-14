<?php

$sql = "
SELECT 
    ec.ExtractionID,
    ec.ExtractionRate,
    fv.VehicleName,
    ft.FoodTypeName,
    r.ReferenceNumber
FROM 
    extractionconversion ec
JOIN 
    foodvehicle fv ON ec.VehicleID = fv.VehicleID
JOIN 
    foodtype ft ON ec.FoodTypeID = ft.FoodTypeID
JOIN 
    reference r ON ec.ReferenceID = r.ReferenceID
ORDER BY 
    ec.ExtractionID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    // Fetch and display table headers
    while ($fieldInfo = $result->fetch_field()) {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
    echo "</tr></thead><tbody>";
    // Fetch and display table rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $key => $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo 'No records found';
}

$conn->close();
?>
