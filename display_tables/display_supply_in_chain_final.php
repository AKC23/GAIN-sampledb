<?php


$sql = "
    SELECT
        s.SupplyID,
        fv.VehicleName,
        c.CountryName,
        ft.FoodTypeName,
        ps.ProcessingStageName,
        s.Origin,
        e.ProducerProcessorName,
        pp.ProductionCapacityVolumeMTY,
        pp.PercentageOfCapacityUsed,
        p.ProductName,
        pr.IdentifierNumber,
        mu.SupplyVolumeUnit,
        mu.PeriodicalUnit,
        s.SourceVolume,
        s.VolumeMTY,
        s.CropToFirstProcessedFoodStageConvertedValue,
        yt.YearTypeName,
        s.StartYear,
        s.EndYear,
        r.ReferenceNumber,

        -- Distribution columns
        d.DistributionID,
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        d.SourceVolume AS DistSourceVolume,
        d.Volume_MT_Y,
        d.StartYear AS DistStartYear,
        d.EndYear AS DistEndYear,
        rd.ReferenceNumber AS DistReferenceNumber

    FROM supply s
    LEFT JOIN foodvehicle fv ON s.VehicleID = fv.VehicleID
    LEFT JOIN country c ON s.CountryID = c.CountryID
    LEFT JOIN foodtype ft ON s.FoodTypeID = ft.FoodTypeID
    LEFT JOIN processingstage ps ON s.PSID = ps.PSID
    LEFT JOIN entity e ON s.EntityID = e.EntityID
    LEFT JOIN producerprocessor pp ON s.EntityID = pp.EntityID
    LEFT JOIN product p ON s.ProductID = p.ProductID
    LEFT JOIN producerreference pr ON s.ProducerReferenceID = pr.ProducerReferenceID
    LEFT JOIN measureunit1 mu ON s.UCID = mu.UCID
    LEFT JOIN yeartype yt ON s.YearTypeID = yt.YearTypeID
    LEFT JOIN reference r ON s.ReferenceID = r.ReferenceID

    LEFT JOIN distribution d 
        ON s.VehicleID = d.VehicleID
       AND s.CountryID = d.CountryID
       AND s.StartYear = d.StartYear
       AND s.EndYear = d.EndYear
    LEFT JOIN distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    LEFT JOIN subdistributionchannel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    LEFT JOIN reference rd ON d.ReferenceID = rd.ReferenceID

    ORDER BY s.SupplyID
";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    // Supply columns
    echo "<th>SupplyID</th>";
    echo "<th>Vehicle Name</th>";
    echo "<th>Country Supplied</th>";
    echo "<th>Food Type</th>";
    echo "<th>Processing Stage</th>";
    echo "<th>Origin</th>";
    echo "<th>Producer/Processor Name</th>";
    echo "<th>Production Capacity Volume (MT/Y)</th>";
    echo "<th>Percentage of Capacity Used</th>";
    echo "<th>Product Name</th>";
    echo "<th>Identifier Number</th>";
    echo "<th>Supply Volume Unit</th>";
    echo "<th>Periodical Unit</th>";
    echo "<th>Source Volume</th>";
    echo "<th>Value in Metric Ton/Year</th>";
    echo "<th>Crop to 1st Processed Food Stage Converted Value</th>";
    echo "<th>Year Type</th>";
    echo "<th>Start Year</th>";
    echo "<th>End Year</th>";
    echo "<th>Reference Number</th>";
    // Distribution columns
    echo "<th>DistributionID</th>";
    echo "<th>Distribution Channel Name</th>";
    echo "<th>Sub Distribution Channel Name</th>";
    echo "<th>Distribution Source Volume</th>";
    echo "<th>Distribution Volume (MT/Y)</th>";
    echo "<th>Distribution Start Year</th>";
    echo "<th>Distribution End Year</th>";
    echo "<th>Distribution Reference Number</th>";
    echo "</tr></thead><tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        // Supply columns
        echo "<td>" . htmlspecialchars($row['SupplyID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CountryName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['FoodTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProcessingStageName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Origin']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProducerProcessorName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductionCapacityVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PercentageOfCapacityUsed']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['IdentifierNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVolumeUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PeriodicalUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['VolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CropToFirstProcessedFoodStageConvertedValue']) . "</td>";
        echo "<td>" . htmlspecialchars($row['YearTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['StartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['EndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ReferenceNumber']) . "</td>";

        // Distribution columns
        echo "<td>" . htmlspecialchars($row['DistributionID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SubDistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistSourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Volume_MT_Y']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistStartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistEndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistReferenceNumber']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "0 results";
}

?>







