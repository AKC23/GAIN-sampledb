<?php
// display_tables/display_processing_stage.php

echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
echo "<th>PSID</th>";
echo "<th>Processing Stage Name</th>";
echo "<th>Extraction Rate</th>";
echo "<th>Vehicle Name</th>";
echo "</tr></thead><tbody>";

// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['PSID']) . "</td>";
    echo "<td>" . htmlspecialchars($row['ProcessingStageName']) . "</td>";
    echo "<td>" . htmlspecialchars($row['ExtractionRate']) . "</td>";

    // Fetch Vehicle Name from foodvehicle table
    $vehicleID = htmlspecialchars($row['VehicleID']);
    $vehicleQuery = $conn->query("SELECT VehicleName FROM foodvehicle WHERE VehicleID = $vehicleID");
    if ($vehicleRow = $vehicleQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($vehicleRow['VehicleName']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    echo "</tr>";
}
echo "</tbody></table></div>";
?>
