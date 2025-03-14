<?php

if (isset($_POST['format'])) {
    $format = $_POST['format'];

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
        r_d.ReferenceNumber AS DistributionReferenceNumber,

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
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=supply_in_chain_final.csv');
            $output = fopen('php://output', 'w');
            if (isset($data[0])) {
                fputcsv($output, array_keys($data[0]));
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
            }
            fclose($output);
        } elseif ($format == 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=supply_in_chain_final.xls');
            echo '<table border="1">';
            if (isset($data[0])) {
                echo '<tr><th>' . implode('</th><th>', array_keys($data[0])) . '</th></tr>';
                foreach ($data as $row) {
                    echo '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
                }
            }
            echo '</table>';
        }
    } else {
        die('No data found');
    }
    $conn->close();
}
?>
