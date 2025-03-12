<?php

// SQL query to fetch data from distribution and related tables
$sql = "
    SELECT 
        d.DistributionID,
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        fv.VehicleName,
        mu.SupplyVolumeUnit,
        mu.PeriodicalUnit,
        d.SourceVolume,
        d.Volume_MT_Y,
        c.CountryName,
        yt.YearTypeName,
        d.StartYear,
        d.EndYear,
        r.ReferenceNumber
    FROM 
        distribution d
    JOIN 
        distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    JOIN 
        subdistributionchannel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    JOIN 
        foodvehicle fv ON d.VehicleID = fv.VehicleID
    JOIN 
        measureunit1 mu ON d.UCID = mu.UCID
    JOIN 
        country c ON d.CountryID = c.CountryID
    JOIN 
        yeartype yt ON d.YearTypeID = yt.YearTypeID
    JOIN 
        reference r ON d.ReferenceID = r.ReferenceID
    ORDER BY 
        d.DistributionID";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    echo "<th>DistributionID</th>";
    echo "<th>DistributionChannelName</th>";
    echo "<th>SubDistributionChannelName</th>";
    echo "<th>VehicleName</th>";
    echo "<th>SupplyVolumeUnit</th>";
    echo "<th>PeriodicalUnit</th>";
    echo "<th>SourceVolume</th>";
    echo "<th>Volume_MT_Y</th>";
    echo "<th>CountryName</th>";
    echo "<th>YearTypeName</th>";
    echo "<th>StartYear</th>";
    echo "<th>EndYear</th>";
    echo "<th>ReferenceNumber</th>";
    echo "</tr></thead><tbody>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['DistributionID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SubDistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVolumeUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PeriodicalUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Volume_MT_Y']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CountryName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['YearTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['StartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['EndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ReferenceNumber']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "0 results";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
