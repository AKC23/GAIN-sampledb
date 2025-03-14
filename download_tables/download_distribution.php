<?php

if (isset($_POST['format'])) {
    $format = $_POST['format'];

    $sql = "
    SELECT 
        d.DistributionID,
        dc.DistributionChannelName,
        sdc.SubDistributionChannelName,
        fv.VehicleName,
        mu.SupplyVolumeUnit,
        mu.PeriodicalUnit,
        d.SourceVolume,
        d.Volume_MT_Y,
        c.CountryName,
        yt.YearTypeName,
        d.StartYear,
        d.EndYear,
        r.ReferenceNumber
    FROM 
        distribution d
    JOIN 
        distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    JOIN 
        subdistributionchannel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
    JOIN 
        foodvehicle fv ON d.VehicleID = fv.VehicleID
    JOIN 
        measureunit1 mu ON d.UCID = mu.UCID
    JOIN 
        country c ON d.CountryID = c.CountryID
    JOIN 
        yeartype yt ON d.YearTypeID = yt.YearTypeID
    JOIN 
        reference r ON d.ReferenceID = r.ReferenceID
    ORDER BY 
        d.DistributionID
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=distribution.csv');
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
            header('Content-Disposition: attachment;filename=distribution.xls');
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
