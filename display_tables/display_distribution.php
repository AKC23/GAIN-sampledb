<?php
// display_tables/display_distribution.php

// SQL query to fetch data from distribution and related tables
$sql = "
    SELECT 
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        fv.VehicleName,
        c.CountryName,
        d.StartYear,
        d.DistributedVolume
    FROM distribution d
    JOIN distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    JOIN subdistributionchannel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    JOIN foodvehicle fv ON d.VehicleID = fv.VehicleID
    JOIN country c ON d.CountryID = c.CountryID
    WHERE d.StartYear IS NOT NULL AND d.StartYear != '' AND d.DistributedVolume IS NOT NULL AND d.DistributedVolume != ''
    ORDER BY d.DistributionID";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    echo "<th>Distribution Channel Name</th>";
    echo "<th>SubDistribution Channel Name</th>";
    echo "<th>Vehicle Name</th>";
    echo "<th>Country Name</th>";
    echo "<th>Start Year</th>";
    echo "<th>Distributed Volume</th>";
    echo "</tr></thead><tbody>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['DistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SubDistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CountryName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['StartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributedVolume']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "0 results";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
