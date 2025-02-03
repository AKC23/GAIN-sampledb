<?php
include('db_connect.php');

$sql = "
    SELECT 
        -- Supply table columns
        s.SupplyID, 
        fv.VehicleName AS SupplyVehicle,
        co.Country_Name,
        ft.FoodTypeName,
        ps.Processing_Stage,
        s.Origin,
        pp.Productioncapacityvolume,
        pp.PercentageOfCapacityUsed,
        b.Brand_Name,
        s.ProductReferenceNo,
        mu1.SupplyVolumeUnit,
        mu1.PeriodicalUnit,
        s.SourceVolume,
        syt.YearType AS SupplyYearType,
        s.StartYear AS SupplyStartYear,
        s.EndYear AS SupplyEndYear,
        r.ReferenceNumber,
        r.Source,
        r.Link,
        r.ProcessToObtainData,
        r.AccessDate,
        -- Distribution table columns
        d.DistributionID,
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        fv2.VehicleName AS DistributionVehicle,
        mu12.SupplyVolumeUnit AS DistSupplyVolumeUnit,
        mu12.PeriodicalUnit AS DistPeriodicalUnit,
        d.volumeMT,
        dyt.YearType AS DistributionYearType,
        d.StartYear AS DistributionStartYear,
        d.EndYear AS DistributionEndYear,
        r2.Source AS DistSource,
        r2.Link AS DistLink,
        r2.ProcessToObtainData AS DistProcessToObtainData,
        r2.AccessDate AS DistAccessDate
    FROM supply s
    JOIN FoodVehicle fv ON s.VehicleID = fv.VehicleID
    JOIN country co ON s.CountryID = co.Country_ID
    JOIN processing_stage ps ON s.PS_ID = ps.PSID
    JOIN producer_processor pp ON s.PSPRID = pp.ProcessorID
    JOIN FoodType ft ON s.FoodTypeID = ft.FoodTypeID
    JOIN brand b ON s.BrandID = b.BrandID
    JOIN measure_unit1 mu1 ON s.UC_ID = mu1.UCID
    JOIN year_type syt ON s.YearTypeID = syt.YearTypeID
    JOIN reference r ON s.ReferenceID = r.ReferenceID
    JOIN distribution d ON 1=1
    JOIN year_type dyt ON d.YearTypeID = dyt.YearTypeID
    JOIN distribution_channel dc ON d.DistributionChannelID = dc.DistributionChannelID
    JOIN sub_distribution_channel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    JOIN FoodVehicle fv2 ON d.VehicleID = fv2.VehicleID
    JOIN measure_unit1 mu12 ON d.UCID = mu12.UCID
    JOIN reference r2 ON d.ReferenceID = r2.ReferenceID
    WHERE syt.YearType = dyt.YearType 
      AND s.StartYear = d.StartYear 
      AND s.EndYear = d.EndYear
    ORDER BY s.SupplyID
";

$result = $conn->query($sql);

echo "<div class='table-responsive'><table class='table table-bordered'>";
echo "<thead><tr>
        <th>SupplyID</th>
        <th>SupplyVehicle</th>
        <th>Country</th>
        <th>FoodTypeName</th>
        <th>Processing_Stage</th>
        <th>Origin</th>
        <th>ProductionCapacity</th>
        <th>%CapacityUsed</th>
        <th>Brand_Name</th>
        <th>ProductReferenceNo</th>
        <th>SupplyVolumeUnit</th>
        <th>PeriodicalUnit</th>
        <th>SourceVolume</th>
        <th>SupplyYearType</th>
        <th>SupplyStartYear</th>
        <th>SupplyEndYear</th>
        <th>ReferenceNumber</th>
        <th>ReferenceSource</th>
        <th>Link</th>
        <th>ProcessToObtainData</th>
        <th>AccessDate</th>
        <th>DistributionID</th>
        <th>DistributionChannel</th>
        <th>SubDistributionChannel</th>
        <th>DistributionVehicle</th>
        <th>DistSupplyVolumeUnit</th>
        <th>DistPeriodicalUnit</th>
        <th>VolumeMT</th>
        <th>DistributionYearType</th>
        <th>DistributionStartYear</th>
        <th>DistributionEndYear</th>
        <th>DistSource</th>
        <th>DistLink</th>
        <th>DistProcessToObtainData</th>
        <th>DistAccessDate</th>
    </tr></thead><tbody>";

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()){
        echo "<tr>
                <td>{$row['SupplyID']}</td>
                <td>{$row['SupplyVehicle']}</td>
                <td>{$row['Country_Name']}</td>
                <td>{$row['FoodTypeName']}</td>
                <td>{$row['Processing_Stage']}</td>
                <td>{$row['Origin']}</td>
                <td>{$row['Productioncapacityvolume']}</td>
                <td>{$row['PercentageOfCapacityUsed']}</td>
                <td>{$row['Brand_Name']}</td>
                <td>{$row['ProductReferenceNo']}</td>
                <td>{$row['SupplyVolumeUnit']}</td>
                <td>{$row['PeriodicalUnit']}</td>
                <td>{$row['SourceVolume']}</td>
                <td>{$row['SupplyYearType']}</td>
                <td>{$row['SupplyStartYear']}</td>
                <td>{$row['SupplyEndYear']}</td>
                <td>{$row['ReferenceNumber']}</td>
                <td>{$row['Source']}</td>
                <td>{$row['Link']}</td>
                <td>{$row['ProcessToObtainData']}</td>
                <td>{$row['AccessDate']}</td>
                <td>{$row['DistributionID']}</td>
                <td>{$row['DistributionChannelName']}</td>
                <td>{$row['SubDistributionChannelName']}</td>
                <td>{$row['DistributionVehicle']}</td>
                <td>{$row['DistSupplyVolumeUnit']}</td>
                <td>{$row['DistPeriodicalUnit']}</td>
                <td>{$row['volumeMT']}</td>
                <td>{$row['DistributionYearType']}</td>
                <td>{$row['DistributionStartYear']}</td>
                <td>{$row['DistributionEndYear']}</td>
                <td>{$row['DistSource']}</td>
                <td>{$row['DistLink']}</td>
                <td>{$row['DistProcessToObtainData']}</td>
                <td>{$row['DistAccessDate']}</td>
            </tr>";
    }
} else {
    echo "<tr><td colspan='33'>No data found</td></tr>";
}

echo "</tbody></table></div>";
?>
