<?php
// display_tables/display_consumption.php

echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
echo "<th>ConsumptionID</th>";
echo "<th>Vehicle Name</th>";
echo "<th>Admin Level 1</th>";
echo "<th>Admin Level 2</th>";
echo "<th>Admin Level 3</th>";
echo "<th>Gender</th>";
echo "<th>Age Range</th>";
echo "<th>Number of People</th>";
echo "<th>Supply Volume Unit</th>";
echo "<th>Periodical Unit</th>";
echo "<th>Source Volume</th>";
echo "<th>Volume (MT/Y)</th>";
echo "<th>Year Type</th>";
echo "<th>Start Year</th>";
echo "<th>End Year</th>";
echo "<th>Reference Number</th>";
echo "<th>Source</th>";
echo "<th>Link</th>";
echo "<th>Process To Obtain Data</th>";
echo "<th>Access Date</th>";
echo "</tr></thead><tbody>";

// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['ConsumptionID']) . "</td>";

    // Fetch Vehicle Name from foodvehicle table
    $vehicleID = htmlspecialchars($row['VehicleID']);
    $vehicleQuery = $conn->query("SELECT VehicleName FROM foodvehicle WHERE VehicleID = $vehicleID");
    if ($vehicleRow = $vehicleQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($vehicleRow['VehicleName']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    // Fetch Admin Level 1 from geographylevel1 table
    $gl1ID = htmlspecialchars($row['GL1ID']);
    $gl1Query = $conn->query("SELECT AdminLevel1 FROM geographylevel1 WHERE GL1ID = $gl1ID");
    if ($gl1Row = $gl1Query->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($gl1Row['AdminLevel1']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    // Fetch Admin Level 2 from geographylevel2 table
    $gl2ID = htmlspecialchars($row['GL2ID']);
    $gl2Query = $conn->query("SELECT AdminLevel2 FROM geographylevel2 WHERE GL2ID = $gl2ID");
    if ($gl2Row = $gl2Query->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($gl2Row['AdminLevel2']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    // Fetch Admin Level 3 from geographylevel3 table
    $gl3ID = htmlspecialchars($row['GL3ID']);
    $gl3Query = $conn->query("SELECT AdminLevel3 FROM geographylevel3 WHERE GL3ID = $gl3ID");
    if ($gl3Row = $gl3Query->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($gl3Row['AdminLevel3']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    // Fetch Gender Name from gender table
    $genderID = htmlspecialchars($row['GenderID']);
    $genderQuery = $conn->query("SELECT GenderName FROM gender WHERE GenderID = $genderID");
    if ($genderRow = $genderQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($genderRow['GenderName']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    // Fetch Age Range from age table
    $ageID = htmlspecialchars($row['AgeID']);
    $ageQuery = $conn->query("SELECT AgeRange FROM age WHERE AgeID = $ageID");
    if ($ageRow = $ageQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($ageRow['AgeRange']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    echo "<td>" . htmlspecialchars($row['NumberOfPeople']) . "</td>";

    // Fetch Supply Volume Unit and Periodical Unit from measureunit1 table
    $ucid = htmlspecialchars($row['UCID']);
    $measureUnitQuery = $conn->query("SELECT SupplyVolumeUnit, PeriodicalUnit FROM measureunit1 WHERE UCID = $ucid");
    if ($measureUnitRow = $measureUnitQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($measureUnitRow['SupplyVolumeUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($measureUnitRow['PeriodicalUnit']) . "</td>";
    } else {
        echo "<td>N/A</td>";
        echo "<td>N/A</td>";
    }

    echo "<td>" . htmlspecialchars($row['SourceVolume']) . "</td>";
    echo "<td>" . htmlspecialchars($row['VolumeMTY']) . "</td>";

    // Fetch Year Type Name from yeartype table
    $yearTypeID = htmlspecialchars($row['YearTypeID']);
    $yearTypeQuery = $conn->query("SELECT YearTypeName FROM yeartype WHERE YearTypeID = $yearTypeID");
    if ($yearTypeRow = $yearTypeQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($yearTypeRow['YearTypeName']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    echo "<td>" . htmlspecialchars($row['StartYear']) . "</td>";
    echo "<td>" . htmlspecialchars($row['EndYear']) . "</td>";

    // Fetch Reference details from reference table
    $referenceID = htmlspecialchars($row['ReferenceID']);
    $referenceQuery = $conn->query("SELECT ReferenceNumber, Source, Link, ProcessToObtainData, AccessDate FROM reference WHERE ReferenceID = $referenceID");
    if ($referenceRow = $referenceQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($referenceRow['ReferenceNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($referenceRow['Source']) . "</td>";
        echo "<td>" . htmlspecialchars($referenceRow['Link']) . "</td>";
        echo "<td>" . htmlspecialchars($referenceRow['ProcessToObtainData']) . "</td>";
        echo "<td>" . htmlspecialchars($referenceRow['AccessDate']) . "</td>";
    } else {
        echo "<td>N/A</td>";
        echo "<td>N/A</td>";
        echo "<td>N/A</td>";
        echo "<td>N/A</td>";
        echo "<td>N/A</td>";
    }

    echo "</tr>";
}
echo "</tbody></table></div>";
