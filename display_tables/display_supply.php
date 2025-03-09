<?php
// display_tables/display_supply.php

// SQL query to fetch data from supply and related tables
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
        r.ReferenceNumber
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
    ORDER BY 
        s.SupplyID";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
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
    echo "</tr></thead><tbody>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
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
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "0 results";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
