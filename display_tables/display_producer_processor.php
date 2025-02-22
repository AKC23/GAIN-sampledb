<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
echo "<th>ProducerProcessorID</th>";
echo "<th>Producer-Processor Name</th>";
echo "<th>CompanyName</th>";
echo "<th>VehicleName</th>";
echo "<th>AdminLevel1</th>";
echo "<th>AdminLevel2</th>";
echo "<th>AdminLevel3</th>";
echo "<th>CountryName</th>";
echo "<th>TaskDoneByEntity</th>";
echo "<th>ProductionCapacityVolume</th>";
echo "<th>PercentageOfCapacityUsed</th>";
echo "<th>AnnualProductionSupplyVolume</th>";
echo "<th>IdentifierNumber</th>";
echo "</tr></thead><tbody>";

// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['ProducerProcessorID']) . "</td>";

    // Fetch entity details from entity table
    $entityID = htmlspecialchars($row['EntityID']);
    $entityQuery = $conn->query("SELECT e.ProducerProcessorName, c.CompanyName, fv.VehicleName, gl1.AdminLevel1, gl2.AdminLevel2, gl3.AdminLevel3, co.CountryName 
                                 FROM entity e 
                                 JOIN company c ON e.CompanyID = c.CompanyID 
                                 JOIN foodvehicle fv ON e.VehicleID = fv.VehicleID 
                                 LEFT JOIN geographylevel1 gl1 ON e.GL1ID = gl1.GL1ID
                                 LEFT JOIN geographylevel2 gl2 ON e.GL2ID = gl2.GL2ID
                                 LEFT JOIN geographylevel3 gl3 ON e.GL3ID = gl3.GL3ID
                                 JOIN country co ON e.CountryID = co.CountryID
                                 WHERE e.EntityID = $entityID");
    if ($entityRow = $entityQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($entityRow['ProducerProcessorName']) . "</td>";
        echo "<td>" . htmlspecialchars($entityRow['CompanyName']) . "</td>";
        echo "<td>" . htmlspecialchars($entityRow['VehicleName']) . "</td>";
        echo "<td>" . htmlspecialchars($entityRow['AdminLevel1']) . "</td>";
        echo "<td>" . htmlspecialchars($entityRow['AdminLevel2']) . "</td>";
        echo "<td>" . htmlspecialchars($entityRow['AdminLevel3']) . "</td>";
        echo "<td>" . htmlspecialchars($entityRow['CountryName']) . "</td>";
    } else {
        echo "<td colspan='7'>N/A</td>";
    }

    echo "<td>" . htmlspecialchars($row['TaskDoneByEntity']) . "</td>";
    echo "<td>" . htmlspecialchars($row['ProductionCapacityVolume']) . "</td>";
    echo "<td>" . htmlspecialchars($row['PercentageOfCapacityUsed']) . "</td>";
    echo "<td>" . htmlspecialchars($row['AnnualProductionSupplyVolume']) . "</td>";

    // Fetch IdentifierNumber from producerreference table
    $producerReferenceID = htmlspecialchars($row['ProducerReferenceID']);
    $identifierQuery = $conn->query("SELECT IdentifierNumber FROM producerreference WHERE ProducerReferenceID = $producerReferenceID");
    if ($identifierRow = $identifierQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($identifierRow['IdentifierNumber']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    echo "</tr>";
}
echo "</tbody></table></div>";
?>
