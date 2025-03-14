<?php

if (isset($_POST['format'])) {
    $format = $_POST['format'];

    $sql = "
    SELECT 
        c.ConsumptionID,
        fv.VehicleName,
        gl1.AdminLevel1,
        gl2.AdminLevel2,
        gl3.AdminLevel3,
        co.CountryName AS CountryName,
        g.GenderName,
        a.AgeRange,
        c.NumberOfPeople,
        mu.SupplyVolumeUnit,
        mu.PeriodicalUnit,
        c.SourceVolume,
        c.VolumeMTY,
        yt.YearTypeName,
        c.StartYear,
        c.EndYear,
        r.ReferenceNumber
    FROM consumption c
    LEFT JOIN foodvehicle fv ON c.VehicleID = fv.VehicleID
    LEFT JOIN geographylevel1 gl1 ON c.GL1ID = gl1.GL1ID
    LEFT JOIN geographylevel2 gl2 ON c.GL2ID = gl2.GL2ID
    LEFT JOIN geographylevel3 gl3 ON c.GL3ID = gl3.GL3ID
    LEFT JOIN country co ON gl1.CountryID = co.CountryID
    LEFT JOIN gender g ON c.GenderID = g.GenderID
    LEFT JOIN age a ON c.AgeID = a.AgeID
    LEFT JOIN measureunit1 mu ON c.UCID = mu.UCID
    LEFT JOIN yeartype yt ON c.YearTypeID = yt.YearTypeID
    LEFT JOIN reference r ON c.ReferenceID = r.ReferenceID
    ORDER BY c.ConsumptionID
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=consumption.csv');
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
            header('Content-Disposition: attachment;filename=consumption.xls');
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
