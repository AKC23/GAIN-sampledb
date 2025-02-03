<?php
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop table if exists
$dropSQL = "DROP TABLE IF EXISTS supply_in_chain_final";
$conn->query($dropSQL);

// Create table as a SELECT join of supply and distribution tables
$createSQL = "
    CREATE TABLE supply_in_chain_final AS
    SELECT 
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
";
if ($conn->query($createSQL) === TRUE) {
    echo "Table 'supply_in_chain_final' created successfully.";
} else {
    echo "Error creating table: " . $conn->error;
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");
?>
