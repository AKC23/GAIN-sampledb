<?php
// display_tables/display_distribution.php

// SQL query to fetch data from distribution and related tables
$sql = "
    WITH SubDistributionCounts AS (
        SELECT 
            DistributionChannelID, 
            CountryID,
            VehicleID,
            COUNT(DISTINCT CASE WHEN SubDistributionChannelID > 1 THEN SubDistributionChannelID END) AS TotalSubSpecific
        FROM distribution
        WHERE DistributionChannelID > 1
        GROUP BY DistributionChannelID, CountryID, VehicleID
    ),
    TotalSubDistribution AS (
        SELECT 
            CountryID,
            VehicleID,
            COUNT(DISTINCT CASE WHEN SubDistributionChannelID > 1 THEN SubDistributionChannelID END) AS TotalSubAll
        FROM distribution
        WHERE DistributionChannelID > 1
        GROUP BY CountryID, VehicleID
    )
    SELECT 
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        fv.VehicleName,
        c.CountryName,
        s.StartYear,
        SUM(s.SourceVolume * (sc.TotalSubSpecific / NULLIF(ts.TotalSubAll, 0))) AS DistributedVolume
    FROM supply s
    JOIN distribution d ON s.VehicleID = d.VehicleID AND s.CountryID = d.CountryID
    JOIN distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    JOIN subdistributionchannel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    JOIN foodvehicle fv ON d.VehicleID = fv.VehicleID
    JOIN country c ON d.CountryID = c.CountryID
    JOIN SubDistributionCounts sc 
        ON d.DistributionChannelID = sc.DistributionChannelID 
        AND d.CountryID = sc.CountryID 
        AND d.VehicleID = sc.VehicleID
    JOIN TotalSubDistribution ts 
        ON d.CountryID = ts.CountryID 
        AND d.VehicleID = ts.VehicleID
    WHERE d.DistributionChannelID > 1
    AND d.SubDistributionChannelID > 1
    AND s.StartYear IS NOT NULL AND s.StartYear != ''
    GROUP BY 
        dc.DistributionChannelName, 
        sdc.SubDistributionChannelName, 
        fv.VehicleName, 
        c.CountryName, 
        s.StartYear
    HAVING 
        DistributedVolume IS NOT NULL AND DistributedVolume != ''";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    echo "<th>DistributionChannelName</th>";
    echo "<th>SubDistributionChannelName</th>";
    echo "<th>VehicleName</th>";
    echo "<th>CountryName</th>";
    echo "<th>StartYear</th>";
    echo "<th>DistributedVolume</th>";
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
