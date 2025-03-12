<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
while ($fieldInfo = $result->fetch_field()) {
    if ($fieldInfo->name == 'CompanyID') {
        echo "<th>Company Name</th>";
    } elseif ($fieldInfo->name == 'VehicleID') {
        echo "<th>Vehicle Name</th>";
    } elseif ($fieldInfo->name == 'GL1ID') {
        echo "<th>Admin Level 1</th>";
    } elseif ($fieldInfo->name == 'GL2ID') {
        echo "<th>Admin Level 2</th>";
    } elseif ($fieldInfo->name == 'GL3ID') {
        echo "<th>Admin Level 3</th>";
    } elseif ($fieldInfo->name == 'CountryID') {
        echo "<th>Country Name</th>";
    } else {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
}
echo "</tr></thead><tbody>";
// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $key => $cell) {
        if ($key == 'CompanyID') {
            // Fetch the CompanyName based on CompanyID
            $companyResult = $conn->query("SELECT CompanyName FROM company WHERE CompanyID = $cell");
            if ($companyRow = $companyResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($companyRow['CompanyName']) . "</td>";
            } else {
                echo "<td>N/A</td>";
            }
        } elseif ($key == 'VehicleID') {
            // Fetch the VehicleName based on VehicleID
            $vehicleResult = $conn->query("SELECT VehicleName FROM foodvehicle WHERE VehicleID = $cell");
            if ($vehicleRow = $vehicleResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($vehicleRow['VehicleName']) . "</td>";
            } else {
                echo "<td>N/A</td>";
            }
        } elseif ($key == 'GL1ID') {
            // Fetch the AdminLevel1 based on GL1ID
            if ($cell != null){
                $gl1Result = $conn->query("SELECT AdminLevel1 FROM geographylevel1 WHERE GL1ID = $cell");
                if ($gl1Row = $gl1Result->fetch_assoc()) {
                    echo "<td>" . htmlspecialchars($gl1Row['AdminLevel1']) . "</td>";
                } else {
                    echo "<td>N/A</td>";
                }
            } else {
                echo "<td>N/A</td>";
            }
        } elseif ($key == 'GL2ID') {
            // Fetch the AdminLevel2 based on GL2ID
            if ($cell != null){
                $gl2Result = $conn->query("SELECT AdminLevel2 FROM geographylevel2 WHERE GL2ID = $cell");
                if ($gl2Row = $gl2Result->fetch_assoc()) {
                    echo "<td>" . htmlspecialchars($gl2Row['AdminLevel2']) . "</td>";
                } else {
                    echo "<td>N/A</td>";
                }
            } else {
                echo "<td>N/A</td>";
            }
        } elseif ($key == 'GL3ID') {
            // Fetch the AdminLevel3 based on GL3ID
            if ($cell != null){
                $gl3Result = $conn->query("SELECT AdminLevel3 FROM geographylevel3 WHERE GL3ID = $cell");
                if ($gl3Row = $gl3Result->fetch_assoc()) {
                    echo "<td>" . htmlspecialchars($gl3Row['AdminLevel3']) . "</td>";
                } else {
                    echo "<td>N/A</td>";
                }
            } else {
                echo "<td>N/A</td>";
            }
        } elseif ($key == 'CountryID') {
            // Fetch the CountryName based on CountryID
            $countryResult = $conn->query("SELECT CountryName FROM country WHERE CountryID = $cell");
            if ($countryRow = $countryResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($countryRow['CountryName']) . "</td>";
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
?>
