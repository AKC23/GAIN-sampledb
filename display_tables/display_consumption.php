<?php
// display_tables/display_consumption.php

$sql = "
    SELECT 
        c.ConsumptionID,
        fv.VehicleName,
        gl1.AdminLevel1,
        gl2.AdminLevel2,
        gl3.AdminLevel3,
        co.CountryName AS CountryName,
        g.GenderName,
        a.AgeRange,
        c.NumberOfPeople,
        mu.SupplyVolumeUnit,
        mu.PeriodicalUnit,
        c.SourceVolume,
        c.VolumeMTY,
        yt.YearTypeName,
        c.StartYear,
        c.EndYear,
        r.ReferenceNumber
    FROM consumption c
    LEFT JOIN foodvehicle fv ON c.VehicleID = fv.VehicleID
    LEFT JOIN geographylevel1 gl1 ON c.GL1ID = gl1.GL1ID
    LEFT JOIN geographylevel2 gl2 ON c.GL2ID = gl2.GL2ID
    LEFT JOIN geographylevel3 gl3 ON c.GL3ID = gl3.GL3ID
    LEFT JOIN country co ON gl1.CountryID = co.CountryID
    LEFT JOIN gender g ON c.GenderID = g.GenderID
    LEFT JOIN age a ON c.AgeID = a.AgeID
    LEFT JOIN measureunit1 mu ON c.UCID = mu.UCID
    LEFT JOIN yeartype yt ON c.YearTypeID = yt.YearTypeID
    LEFT JOIN reference r ON c.ReferenceID = r.ReferenceID
    ORDER BY c.ConsumptionID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
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

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['ConsumptionID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AdminLevel1']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AdminLevel2']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AdminLevel3']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CountryName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['GenderName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AgeRange']) . "</td>";
        echo "<td>" . htmlspecialchars($row['NumberOfPeople']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVolumeUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PeriodicalUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['VolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['YearTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['StartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['EndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ReferenceNumber']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo 'No records found';
}

$conn->close();
?>
