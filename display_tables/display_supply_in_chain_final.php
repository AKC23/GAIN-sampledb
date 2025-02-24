<?php
// display_tables/display_supply_in_chain_final.php


// SQL query to join supply and distribution tables by matching StartYear and EndYear
$sql = "
    SELECT 
        s.SupplyID,
        fv.VehicleName AS SupplyVehicleName,
        c.CountryName AS SupplyCountryName,
        ft.FoodTypeName,
        ps.ProcessingStageName,
        s.Origin,
        e.ProducerProcessorName,
        pp.ProductionCapacityVolumeMTY,
        pp.PercentageOfCapacityUsed,
        p.ProductName,
        pr.IdentifierNumber,
        mu.SupplyVolumeUnit AS SupplyVolumeUnit,
        mu.PeriodicalUnit AS SupplyPeriodicalUnit,
        s.SourceVolume AS SupplySourceVolume,
        s.VolumeMTY AS SupplyVolumeMTY,
        s.CropToFirstProcessedFoodStageConvertedValue,
        yt.YearTypeName AS SupplyYearTypeName,
        s.StartYear AS SupplyStartYear,
        s.EndYear AS SupplyEndYear,
        r.ReferenceNumber AS SupplyReferenceNumber,
        r.Source AS SupplySource,
        r.Link AS SupplyLink,
        r.ProcessToObtainData AS SupplyProcessToObtainData,
        r.AccessDate AS SupplyAccessDate,
        d.DistributionID,
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        fv2.VehicleName AS DistributionVehicleName,
        mu2.SupplyVolumeUnit AS DistributionVolumeUnit,
        mu2.PeriodicalUnit AS DistributionPeriodicalUnit,
        d.SourceVolume AS DistributionSourceVolume,
        d.Volume_MT_Y AS DistributionVolumeMTY,
        c2.CountryName AS DistributionCountryName,
        yt2.YearTypeName AS DistributionYearTypeName,
        d.StartYear AS DistributionStartYear,
        d.EndYear AS DistributionEndYear,
        r2.ReferenceNumber AS DistributionReferenceNumber,
        r2.Source AS DistributionSource,
        r2.Link AS DistributionLink,
        r2.ProcessToObtainData AS DistributionProcessToObtainData,
        r2.AccessDate AS DistributionAccessDate
    FROM 
        supply s
    JOIN 
        foodvehicle fv ON s.VehicleID = fv.VehicleID
    JOIN 
        country c ON s.CountryID = c.CountryID
    JOIN 
        foodtype ft ON s.FoodTypeID = ft.FoodTypeID
    JOIN 
        processingstage ps ON s.PSID = ps.PSID
    JOIN 
        entity e ON s.EntityID = e.EntityID
    JOIN 
        producerprocessor pp ON s.EntityID = pp.EntityID
    JOIN 
        product p ON s.ProductID = p.ProductID
    JOIN 
        producerreference pr ON s.ProducerReferenceID = pr.ProducerReferenceID
    JOIN 
        measureunit1 mu ON s.UCID = mu.UCID
    JOIN 
        yeartype yt ON s.YearTypeID = yt.YearTypeID
    JOIN 
        reference r ON s.ReferenceID = r.ReferenceID
    JOIN 
        distribution d ON s.StartYear = d.StartYear AND s.EndYear = d.EndYear
    JOIN 
        distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    JOIN 
        subdistributionchannel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    JOIN 
        foodvehicle fv2 ON d.VehicleID = fv2.VehicleID
    JOIN 
        measureunit1 mu2 ON d.UCID = mu2.UCID
    JOIN 
        country c2 ON d.CountryID = c2.CountryID
    JOIN 
        yeartype yt2 ON d.YearTypeID = yt2.YearTypeID
    JOIN 
        reference r2 ON d.ReferenceID = r2.ReferenceID
    ORDER BY 
        s.SupplyID, d.DistributionID";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    echo "<th>SupplyID</th>";
    echo "<th>Supply Vehicle Name</th>";
    echo "<th>Supply Country</th>";
    echo "<th>Food Type</th>";
    echo "<th>Processing Stage</th>";
    echo "<th>Origin</th>";
    echo "<th>Producer/Processor Name</th>";
    echo "<th>Production Capacity Volume (MT/Y)</th>";
    echo "<th>Percentage of Capacity Used</th>";
    echo "<th>Product Name</th>";
    echo "<th>Identifier Number</th>";
    echo "<th>Supply Volume Unit</th>";
    echo "<th>Supply Periodical Unit</th>";
    echo "<th>Supply Source Volume</th>";
    echo "<th>Supply Volume (MT/Y)</th>";
    echo "<th>Crop to 1st Processed Food Stage Converted Value</th>";
    echo "<th>Supply Year Type</th>";
    echo "<th>Supply Start Year</th>";
    echo "<th>Supply End Year</th>";
    echo "<th>Supply Reference Number</th>";
    echo "<th>Supply Source</th>";
    echo "<th>Supply Link</th>";
    echo "<th>Supply Process To Obtain Data</th>";
    echo "<th>Supply Access Date</th>";
    echo "<th>DistributionID</th>";
    echo "<th>Distribution Channel Name</th>";
    echo "<th>Sub Distribution Channel Name</th>";
    echo "<th>Distribution Vehicle Name</th>";
    echo "<th>Distribution Volume Unit</th>";
    echo "<th>Distribution Periodical Unit</th>";
    echo "<th>Distribution Source Volume</th>";
    echo "<th>Distribution Volume (MT/Y)</th>";
    echo "<th>Distribution Country</th>";
    echo "<th>Distribution Year Type</th>";
    echo "<th>Distribution Start Year</th>";
    echo "<th>Distribution End Year</th>";
    echo "<th>Distribution Reference Number</th>";
    echo "<th>Distribution Source</th>";
    echo "<th>Distribution Link</th>";
    echo "<th>Distribution Process To Obtain Data</th>";
    echo "<th>Distribution Access Date</th>";
    echo "</tr></thead><tbody>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['SupplyID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVehicleName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyCountryName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['FoodTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProcessingStageName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Origin']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProducerProcessorName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductionCapacityVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PercentageOfCapacityUsed']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['IdentifierNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVolumeUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyPeriodicalUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplySourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CropToFirstProcessedFoodStageConvertedValue']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyYearTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyStartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyEndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyReferenceNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplySource']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyLink']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyProcessToObtainData']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyAccessDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SubDistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionVehicleName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionVolumeUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionPeriodicalUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionSourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionCountryName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionYearTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionStartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionEndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionReferenceNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionSource']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionLink']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionProcessToObtainData']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionAccessDate']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "0 results";
}

// Close the database connection
$conn->close();
?>
