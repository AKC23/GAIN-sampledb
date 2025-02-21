<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
while ($fieldInfo = $result->fetch_field()) {
    if ($fieldInfo->name == 'VehicleID') {
        echo "<th>VehicleName</th>";
    } elseif ($fieldInfo->name == 'FoodTypeID') {
        echo "<th>FoodTypeName</th>";
    } elseif ($fieldInfo->name == 'ReferenceID') {
        echo "<th>ReferenceNumber</th><th>Source</th><th>Link</th><th>ProcessToObtainData</th><th>AccessDate</th>";
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
        } elseif ($key == 'FoodTypeID') {
            // Fetch the FoodTypeName based on FoodTypeID
            $foodTypeResult = $conn->query("SELECT FoodTypeName FROM foodtype WHERE FoodTypeID = $cell");
            if ($foodTypeRow = $foodTypeResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($foodTypeRow['FoodTypeName']) . "</td>";
            } else {
                echo "<td>N/A</td>";
            }
        } elseif ($key == 'ReferenceID') {
            // Fetch the reference details based on ReferenceID
            $referenceResult = $conn->query("SELECT ReferenceNumber, Source, Link, ProcessToObtainData, AccessDate FROM reference WHERE ReferenceID = $cell");
            if ($referenceRow = $referenceResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($referenceRow['ReferenceNumber']) . "</td>";
                echo "<td>" . htmlspecialchars($referenceRow['Source']) . "</td>";
                echo "<td>" . htmlspecialchars($referenceRow['Link']) . "</td>";
                echo "<td>" . htmlspecialchars($referenceRow['ProcessToObtainData']) . "</td>";
                echo "<td>" . htmlspecialchars($referenceRow['AccessDate']) . "</td>";
            } else {
                echo "<td colspan='5'>N/A</td>";
            }
        } else {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
    }
    echo "</tr>";
}
echo "</tbody></table></div>";
?>
