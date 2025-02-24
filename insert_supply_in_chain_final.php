<?php
// insert_supply_in_chain_final.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'supply_in_chain_final' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS supply_in_chain_final";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'supply_in_chain_final' dropped successfully.<br>";
} else {
    echo "Error dropping table 'supply_in_chain_final': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'supply_in_chain_final' table
$createTableSQL = "
    CREATE TABLE supply_in_chain_final (
        SupplyID INT(11),
        SupplyCountry VARCHAR(255),
        FoodTypeName VARCHAR(255),
        ProcessingStageName VARCHAR(255),
        Origin VARCHAR(255),
        ProducerProcessorName VARCHAR(255),
        ProductName VARCHAR(255),
        IdentifierNumber VARCHAR(255),
        SupplyVolumeUnit VARCHAR(50),
        SupplyPeriodicalUnit VARCHAR(50),
        SupplySourceVolume DECIMAL(20, 3),
        SupplyVolumeMTY DECIMAL(20, 3),
        CropToFirstProcessedFoodStageConvertedValue DECIMAL(20, 3),
        SupplyYearType VARCHAR(255),
        SupplyStartYear INT(4),
        SupplyEndYear INT(4),
        SupplyReferenceNumber INT(11),
        SupplySource VARCHAR(255),
        SupplyLink VARCHAR(255),
        SupplyProcessToObtainData VARCHAR(255),
        SupplyAccessDate DATE,
        DistributionID INT(11),
        DistributionChannelName VARCHAR(255),
        SubDistributionChannelName VARCHAR(255),
        DistributionVehicleName VARCHAR(255),
        DistributionVolumeUnit VARCHAR(50),
        DistributionPeriodicalUnit VARCHAR(50),
        DistributionSourceVolume DECIMAL(10, 2),
        DistributionVolumeMTY DECIMAL(10, 2),
        DistributionCountry VARCHAR(255),
        DistributionYearType VARCHAR(255),
        DistributionStartYear INT(4),
        DistributionEndYear INT(4),
        DistributionReferenceNumber INT(11),
        DistributionSource VARCHAR(255),
        DistributionLink VARCHAR(255),
        DistributionProcessToObtainData VARCHAR(255),
        DistributionAccessDate DATE,
        ConsumptionID INT(11),
        ConsumptionVehicleName VARCHAR(255),
        AdminLevel1 VARCHAR(50),
        AdminLevel2 VARCHAR(50),
        AdminLevel3 VARCHAR(50),
        GenderName VARCHAR(50),
        AgeRange VARCHAR(50),
        NumberOfPeople INT(11),
        ConsumptionVolumeUnit VARCHAR(50),
        ConsumptionPeriodicalUnit VARCHAR(50),
        ConsumptionSourceVolume DECIMAL(20, 4),
        ConsumptionVolumeMTY DECIMAL(20, 4),
        ConsumptionYearType VARCHAR(255),
        ConsumptionStartYear INT(11),
        ConsumptionEndYear INT(11),
        ConsumptionReferenceNumber INT(11),
        ConsumptionSource VARCHAR(255),
        ConsumptionLink VARCHAR(255),
        ConsumptionProcessToObtainData VARCHAR(255),
        ConsumptionAccessDate DATE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'supply_in_chain_final' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// SQL query to join supply, distribution, and consumption tables
$sql = "
    SELECT 
        s.SupplyID,
        c.CountryName AS SupplyCountry,
        ft.FoodTypeName,
        ps.ProcessingStageName,
        s.Origin,
        e.ProducerProcessorName,
        p.ProductName,
        pr.IdentifierNumber,
        mu1.SupplyVolumeUnit AS SupplyVolumeUnit,
        mu1.PeriodicalUnit AS SupplyPeriodicalUnit,
        s.SourceVolume AS SupplySourceVolume,
        s.VolumeMTY AS SupplyVolumeMTY,
        s.CropToFirstProcessedFoodStageConvertedValue,
        yt1.YearTypeName AS SupplyYearType,
        s.StartYear AS SupplyStartYear,
        s.EndYear AS SupplyEndYear,
        r1.ReferenceNumber AS SupplyReferenceNumber,
        r1.Source AS SupplySource,
        r1.Link AS SupplyLink,
        r1.ProcessToObtainData AS SupplyProcessToObtainData,
        r1.AccessDate AS SupplyAccessDate,
        d.DistributionID,
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        fv.VehicleName AS DistributionVehicleName,
        mu2.SupplyVolumeUnit AS DistributionVolumeUnit,
        mu2.PeriodicalUnit AS DistributionPeriodicalUnit,
        d.SourceVolume AS DistributionSourceVolume,
        d.Volume_MT_Y AS DistributionVolumeMTY,
        c2.CountryName AS DistributionCountry,
        yt2.YearTypeName AS DistributionYearType,
        d.StartYear AS DistributionStartYear,
        d.EndYear AS DistributionEndYear,
        r2.ReferenceNumber AS DistributionReferenceNumber,
        r2.Source AS DistributionSource,
        r2.Link AS DistributionLink,
        r2.ProcessToObtainData AS DistributionProcessToObtainData,
        r2.AccessDate AS DistributionAccessDate,
        con.ConsumptionID,
        fv2.VehicleName AS ConsumptionVehicleName,
        gl1.AdminLevel1,
        gl2.AdminLevel2,
        gl3.AdminLevel3,
        g.GenderName,
        a.AgeRange,
        con.NumberOfPeople,
        mu3.SupplyVolumeUnit AS ConsumptionVolumeUnit,
        mu3.PeriodicalUnit AS ConsumptionPeriodicalUnit,
        con.SourceVolume AS ConsumptionSourceVolume,
        con.VolumeMTY AS ConsumptionVolumeMTY,
        yt3.YearTypeName AS ConsumptionYearType,
        con.StartYear AS ConsumptionStartYear,
        con.EndYear AS ConsumptionEndYear,
        r3.ReferenceNumber AS ConsumptionReferenceNumber,
        r3.Source AS ConsumptionSource,
        r3.Link AS ConsumptionLink,
        r3.ProcessToObtainData AS ConsumptionProcessToObtainData,
        r3.AccessDate AS ConsumptionAccessDate
    FROM 
        supply s
    JOIN 
        country c ON s.CountryID = c.CountryID
    JOIN 
        foodtype ft ON s.FoodTypeID = ft.FoodTypeID
    JOIN 
        processingstage ps ON s.PSID = ps.PSID
    JOIN 
        entity e ON s.EntityID = e.EntityID
    JOIN 
        product p ON s.ProductID = p.ProductID
    JOIN 
        producerreference pr ON s.ProducerReferenceID = pr.ProducerReferenceID
    JOIN 
        measureunit1 mu1 ON s.UCID = mu1.UCID
    JOIN 
        yeartype yt1 ON s.YearTypeID = yt1.YearTypeID
    JOIN 
        reference r1 ON s.ReferenceID = r1.ReferenceID
    JOIN 
        distribution d ON s.SupplyID = d.DistributionID
    JOIN 
        distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    JOIN 
        subdistributionchannel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    JOIN 
        foodvehicle fv ON d.VehicleID = fv.VehicleID
    JOIN 
        measureunit1 mu2 ON d.UCID = mu2.UCID
    JOIN 
        country c2 ON d.CountryID = c2.CountryID
    JOIN 
        yeartype yt2 ON d.YearTypeID = yt2.YearTypeID
    JOIN 
        reference r2 ON d.ReferenceID = r2.ReferenceID
    JOIN 
        consumption con ON d.DistributionID = con.ConsumptionID
    JOIN 
        foodvehicle fv2 ON con.VehicleID = fv2.VehicleID
    JOIN 
        geographylevel1 gl1 ON con.GL1ID = gl1.GL1ID
    JOIN 
        geographylevel2 gl2 ON con.GL2ID = gl2.GL2ID
    JOIN 
        geographylevel3 gl3 ON con.GL3ID = gl3.GL3ID
    JOIN 
        gender g ON con.GenderID = g.GenderID
    JOIN 
        age a ON con.AgeID = a.AgeID
    JOIN 
        measureunit1 mu3 ON con.UCID = mu3.UCID
    JOIN 
        yeartype yt3 ON con.YearTypeID = yt3.YearTypeID
    JOIN 
        reference r3 ON con.ReferenceID = r3.ReferenceID
    ORDER BY 
        s.SupplyID, d.DistributionID, con.ConsumptionID";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Insert data into the supply_in_chain_final table
    while ($row = $result->fetch_assoc()) {
        $insertSQL = "
            INSERT INTO supply_in_chain_final (
                SupplyID, SupplyCountry, FoodTypeName, ProcessingStageName, Origin, ProducerProcessorName, ProductName, IdentifierNumber, 
                SupplyVolumeUnit, SupplyPeriodicalUnit, SupplySourceVolume, SupplyVolumeMTY, CropToFirstProcessedFoodStageConvertedValue, 
                SupplyYearType, SupplyStartYear, SupplyEndYear, SupplyReferenceNumber, SupplySource, SupplyLink, SupplyProcessToObtainData, 
                SupplyAccessDate, DistributionID, DistributionChannelName, SubDistributionChannelName, DistributionVehicleName, 
                DistributionVolumeUnit, DistributionPeriodicalUnit, DistributionSourceVolume, DistributionVolumeMTY, DistributionCountry, 
                DistributionYearType, DistributionStartYear, DistributionEndYear, DistributionReferenceNumber, DistributionSource, 
                DistributionLink, DistributionProcessToObtainData, DistributionAccessDate, ConsumptionID, ConsumptionVehicleName, 
                AdminLevel1, AdminLevel2, AdminLevel3, GenderName, AgeRange, NumberOfPeople, ConsumptionVolumeUnit, ConsumptionPeriodicalUnit, 
                ConsumptionSourceVolume, ConsumptionVolumeMTY, ConsumptionYearType, ConsumptionStartYear, ConsumptionEndYear, 
                ConsumptionReferenceNumber, ConsumptionSource, ConsumptionLink, ConsumptionProcessToObtainData, ConsumptionAccessDate
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insertSQL);
        $stmt->bind_param(
            'issssssssssssssssssssssssssssssssssssssssssssssssssssss',
            $row['SupplyID'], $row['SupplyCountry'], $row['FoodTypeName'], $row['ProcessingStageName'], $row['Origin'], 
            $row['ProducerProcessorName'], $row['ProductName'], $row['IdentifierNumber'], $row['SupplyVolumeUnit'], 
            $row['SupplyPeriodicalUnit'], $row['SupplySourceVolume'], $row['SupplyVolumeMTY'], $row['CropToFirstProcessedFoodStageConvertedValue'], 
            $row['SupplyYearType'], $row['SupplyStartYear'], $row['SupplyEndYear'], $row['SupplyReferenceNumber'], $row['SupplySource'], 
            $row['SupplyLink'], $row['SupplyProcessToObtainData'], $row['SupplyAccessDate'], $row['DistributionID'], $row['DistributionChannelName'], 
            $row['SubDistributionChannelName'], $row['DistributionVehicleName'], $row['DistributionVolumeUnit'], $row['DistributionPeriodicalUnit'], 
            $row['DistributionSourceVolume'], $row['DistributionVolumeMTY'], $row['DistributionCountry'], $row['DistributionYearType'], 
            $row['DistributionStartYear'], $row['DistributionEndYear'], $row['DistributionReferenceNumber'], $row['DistributionSource'], 
            $row['DistributionLink'], $row['DistributionProcessToObtainData'], $row['DistributionAccessDate'], $row['ConsumptionID'], 
            $row['ConsumptionVehicleName'], $row['AdminLevel1'], $row['AdminLevel2'], $row['AdminLevel3'], $row['GenderName'], $row['AgeRange'], 
            $row['NumberOfPeople'], $row['ConsumptionVolumeUnit'], $row['ConsumptionPeriodicalUnit'], $row['ConsumptionSourceVolume'], 
            $row['ConsumptionVolumeMTY'], $row['ConsumptionYearType'], $row['ConsumptionStartYear'], $row['ConsumptionEndYear'], 
            $row['ConsumptionReferenceNumber'], $row['ConsumptionSource'], $row['ConsumptionLink'], $row['ConsumptionProcessToObtainData'], 
            $row['ConsumptionAccessDate']
        );

        if ($stmt->execute()) {
            echo "✓ Inserted supply_in_chain_final record with SupplyID: " . $row['SupplyID'] . "<br>";
        } else {
            echo "Error inserting supply_in_chain_final record: " . $stmt->error . "<br>";
        }

        $stmt->close();
    }
} else {
    echo "0 results";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
