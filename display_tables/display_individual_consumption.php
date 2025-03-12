<?php

$query = "
    SELECT c.ConsumptionID,
           v.VehicleName,
           g.GenderName,
           a.AgeRange,
           c.NumberOfPeople,
           m.SupplyVolumeUnit,
           m.PeriodicalUnit,
           c.SourceVolume,
           c.VolumeMTY,
           y.YearTypeName,
           c.StartYear,
           c.EndYear,
           r.ReferenceNumber
    FROM consumption c
    LEFT JOIN foodvehicle v ON c.VehicleID = v.VehicleID
    LEFT JOIN gender g ON c.GenderID = g.GenderID
    LEFT JOIN age a ON c.AgeID = a.AgeID
    LEFT JOIN measureunit1 m ON c.UCID = m.UCID
    LEFT JOIN yeartype y ON c.YearTypeID = y.YearTypeID
    LEFT JOIN reference r ON c.ReferenceID = r.ReferenceID
";
$result = $conn->query($query);

echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
echo "<th>ConsumptionID</th>";
echo "<th>Vehicle Name</th>";
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
    echo "<td>{$row['ConsumptionID']}</td>";
    echo "<td>{$row['VehicleName']}</td>";
    echo "<td>{$row['GenderName']}</td>";
    echo "<td>{$row['AgeRange']}</td>";
    echo "<td>{$row['NumberOfPeople']}</td>";
    echo "<td>{$row['SupplyVolumeUnit']}</td>";
    echo "<td>{$row['PeriodicalUnit']}</td>";
    echo "<td>{$row['SourceVolume']}</td>";
    echo "<td>{$row['VolumeMTY']}</td>";
    echo "<td>{$row['YearTypeName']}</td>";
    echo "<td>{$row['StartYear']}</td>";
    echo "<td>{$row['EndYear']}</td>";
    echo "<td>{$row['ReferenceNumber']}</td>";
    echo "</tr>";
}
echo "</tbody></table></div>";
?>