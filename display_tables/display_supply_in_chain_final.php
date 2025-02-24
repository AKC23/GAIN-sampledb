<?php
// display_tables/display_supply_in_chain_final.php

// Include the database connection
include('db_connect.php');

// SQL query to fetch data from supply_in_chain_final table
$sql = "SELECT * FROM supply_in_chain_final ORDER BY SupplyID";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    echo "<th>SupplyID</th>";
    echo "<th>Supply Country</th>";
    echo "<th>Food Type</th>";
    echo "<th>Processing Stage</th>";
    echo "<th>Origin</th>";
    echo "<th>Producer/Processor Name</th>";
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
    echo "<th>ConsumptionID</th>";
    echo "<th>Consumption Vehicle Name</th>";
    echo "<th>Admin Level 1</th>";
    echo "<th>Admin Level 2</th>";
    echo "<th>Admin Level 3</th>";
    echo "<th>Gender</th>";
    echo "<th>Age Range</th>";
    echo "<th>Number of People</th>";
    echo "<th>Consumption Volume Unit</th>";
    echo "<th>Consumption Periodical Unit</th>";
    echo "<th>Consumption Source Volume</th>";
    echo "<th>Consumption Volume (MT/Y)</th>";
    echo "<th>Consumption Year Type</th>";
    echo "<th>Consumption Start Year</th>";
    echo "<th>Consumption End Year</th>";
    echo "<th>Consumption Reference Number</th>";
    echo "<th>Consumption Source</th>";
    echo "<th>Consumption Link</th>";
    echo "<th>Consumption Process To Obtain Data</th>";
    echo "<th>Consumption Access Date</th>";
    echo "</tr></thead><tbody>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['SupplyID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyCountry']) . "</td>";
        echo "<td>" . htmlspecialchars($row['FoodTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProcessingStageName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Origin']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProducerProcessorName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['IdentifierNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVolumeUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyPeriodicalUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplySourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CropToFirstProcessedFoodStageConvertedValue']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SupplyYearType']) . "</td>";
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
        echo "<td>" . htmlspecialchars($row['DistributionCountry']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionYearType']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionStartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionEndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionReferenceNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionSource']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionLink']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionProcessToObtainData']) . "</td>";
        echo "<td>" . htmlspecialchars($row['DistributionAccessDate']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionVehicleName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AdminLevel1']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AdminLevel2']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AdminLevel3']) . "</td>";
        echo "<td>" . htmlspecialchars($row['GenderName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AgeRange']) . "</td>";
        echo "<td>" . htmlspecialchars($row['NumberOfPeople']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionVolumeUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionPeriodicalUnit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionSourceVolume']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionVolumeMTY']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionYearType']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionStartYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionEndYear']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionReferenceNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionSource']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionLink']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionProcessToObtainData']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ConsumptionAccessDate']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "0 results";
}

// Close the database connection
$conn->close();
?>
