<?php

$sql = "
SELECT 
    e.EntityID,
    e.ProducerProcessorName,
    c.CompanyName,
    fv.VehicleName,
    gl1.AdminLevel1,
    gl2.AdminLevel2,
    gl3.AdminLevel3,
    co.CountryName
FROM 
    entity e
LEFT JOIN 
    company c ON e.CompanyID = c.CompanyID
LEFT JOIN 
    foodvehicle fv ON e.VehicleID = fv.VehicleID
LEFT JOIN 
    geographylevel1 gl1 ON e.GL1ID = gl1.GL1ID
LEFT JOIN 
    geographylevel2 gl2 ON e.GL2ID = gl2.GL2ID
LEFT JOIN 
    geographylevel3 gl3 ON e.GL3ID = gl3.GL3ID
LEFT JOIN 
    country co ON e.CountryID = co.CountryID
ORDER BY 
    e.EntityID
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
        foreach ($row as $cell) {
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
