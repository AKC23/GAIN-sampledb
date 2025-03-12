<?php

$sql = "
    SELECT 
        -- Supply Table Columns
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
        mu.SupplyVolumeUnit AS SupplyUnit,
        mu.PeriodicalUnit AS SupplyPeriod,
        s.SourceVolume AS SupplySourceVolume,
        s.VolumeMTY AS SupplyVolumeMTY,
        s.CropToFirstProcessedFoodStageConvertedValue,
        yt.YearTypeName AS SupplyYearType,
        s.StartYear AS SupplyStartYear,
        s.EndYear AS SupplyEndYear,
        r.ReferenceNumber AS SupplyReferenceNumber,

        -- Distribution Table Columns
        d.DistributionID,
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        mu_d.SupplyVolumeUnit AS DistributionUnit,
        mu_d.PeriodicalUnit AS DistributionPeriod,
        d.SourceVolume AS DistributionSourceVolume,
        d.Volume_MT_Y AS DistributionVolumeMTY,
        c_d.CountryName AS DistributionCountryName,
        yt_d.YearTypeName AS DistributionYearType,
        d.StartYear AS DistributionStartYear,
        d.EndYear AS DistributionEndYear,
        r_d.ReferenceNumber AS DistributionReferenceNumber,  -- Corrected alias

        -- Consumption Table Columns
        con.ConsumptionID,
        con.NumberOfPeople,
        con.SourceVolume AS ConsumptionSourceVolume,
        con.VolumeMTY AS ConsumptionVolumeMTY,
        mu_c.SupplyVolumeUnit AS ConsumptionUnit,
        mu_c.PeriodicalUnit AS ConsumptionPeriod,
        yt_c.YearTypeName AS ConsumptionYearType,
        con.StartYear AS ConsumptionStartYear,
        con.EndYear AS ConsumptionEndYear

    FROM 
        supply s
    LEFT JOIN 
        distribution d 
        ON s.CountryID = d.CountryID 
        AND s.VehicleID = d.VehicleID 
        AND s.StartYear = d.StartYear 

    -- Joins for Supply Table
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

    -- Joins for Distribution Table
    LEFT JOIN 
        distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    LEFT JOIN 
        subdistributionchannel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    LEFT JOIN 
        foodvehicle fv_d ON d.VehicleID = fv_d.VehicleID
    LEFT JOIN 
        measureunit1 mu_d ON d.UCID = mu_d.UCID
    LEFT JOIN 
        country c_d ON d.CountryID = c_d.CountryID
    LEFT JOIN 
        yeartype yt_d ON d.YearTypeID = yt_d.YearTypeID
    LEFT JOIN 
        reference r_d ON d.ReferenceID = r_d.ReferenceID

    -- Join with Consumption Table
    LEFT JOIN 
        consumption con 
        ON s.VehicleID = con.VehicleID 
        AND s.CountryID = con.GL1ID  -- Assuming GL1ID corresponds to CountryID
        AND s.StartYear = con.StartYear 
    LEFT JOIN 
        measureunit1 mu_c ON con.UCID = mu_c.UCID
    LEFT JOIN 
        yeartype yt_c ON con.YearTypeID = yt_c.YearTypeID
    LEFT JOIN 
        reference r_c ON con.ReferenceID = r_c.ReferenceID

    ORDER BY 
        s.SupplyID, d.DistributionID, con.ConsumptionID
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
    // Consumption columns
    echo "<th>ConsumptionID</th>";
    echo "<th>Number of People</th>";
    echo "<th>Consumption Source Volume</th>";
    echo "<th>Consumption Volume (MT/Y)</th>";
    echo "<th>Consumption Unit</th>";
    echo "<th>Consumption Period</th>";
    echo "<th>Consumption Year Type</th>";
    echo "<th>Consumption Start Year</th>";
    echo "<th>Consumption End Year</th>";
    echo "</tr></thead><tbody>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        // Supply columns
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
        echo "<td>" . htmlspecialchars($row['SupplyUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyPeriod']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplySourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CropToFirstProcessedFoodStageConvertedValue']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyYearType']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyStartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyEndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyReferenceNumber']) . "</td>";

        // Distribution columns
        echo "<td>" . htmlspecialchars($row['DistributionID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SubDistributionChannelName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionSourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionStartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionEndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionReferenceNumber']) . "</td>";

        // Consumption columns
        echo "<td>" . htmlspecialchars($row['ConsumptionID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['NumberOfPeople']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionSourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionPeriod']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionYearType']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionStartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionEndYear']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "0 results";
}

?>







