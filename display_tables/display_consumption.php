<?php
// display_tables/display_consumption.php

$sql = "
    SELECT 
        c.ConsumptionID,
        c.VehicleID,
        c.GL1ID,
        c.GL2ID,
        c.GL3ID,
        c.GenderID,
        c.AgeID,
        c.NumberOfPeople,
        c.SourceVolume,
        c.VolumeMTY,
        c.UCID,
        c.YearTypeID,
        c.StartYear,
        c.EndYear,
        c.ReferenceID,
        gl1.AdminLevel1,
        gl2.AdminLevel2,
        gl3.AdminLevel3,
        co.CountryName AS GL1CountryName
    FROM consumption c
    LEFT JOIN geographylevel1 gl1 ON c.GL1ID = gl1.GL1ID
    LEFT JOIN geographylevel2 gl2 ON c.GL2ID = gl2.GL2ID
    LEFT JOIN geographylevel3 gl3 ON c.GL3ID = gl3.GL3ID
    LEFT JOIN country co ON gl1.CountryID = co.CountryID
";
$result = $conn->query($sql);

echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
echo "<th>ConsumptionID</th>";
echo "<th>Vehicle Name</th>";
echo "<th>Admin Level 1</th>";
echo "<th>Admin Level 2</th>";
echo "<th>Admin Level 3</th>";
echo "<th>Country</th>";
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
    echo "<td>" . htmlspecialchars($row['AdminLevel1']) . "</td>";

    // Fetch Admin Level 2 from geographylevel2 table
    echo "<td>" . htmlspecialchars($row['AdminLevel2']) . "</td>";

    // Fetch Admin Level 3 from geographylevel3 table
    echo "<td>" . htmlspecialchars($row['AdminLevel3']) . "</td>";

    // Display the joined country name
    echo "<td>" . htmlspecialchars($row['GL1CountryName']) . "</td>";

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
    } else {
        echo "<td>N/A</td>";
    }

    echo "</tr>";
}
echo "</tbody></table></div>";
