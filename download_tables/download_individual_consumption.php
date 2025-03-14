<?php

if (isset($_POST['format'])) {
    $format = $_POST['format'];

    $sql = "
    SELECT c.ConsumptionID,
           v.VehicleName,
           g.GenderName,
           a.AgeRange,
           c.NumberOfPeople,
           m.SupplyVolumeUnit,
           m.PeriodicalUnit,
           c.SourceVolume,
           c.VolumeMTY,
           y.YearTypeName,
           c.StartYear,
           c.EndYear,
           r.ReferenceNumber
    FROM consumption c
    LEFT JOIN foodvehicle v ON c.VehicleID = v.VehicleID
    LEFT JOIN gender g ON c.GenderID = g.GenderID
    LEFT JOIN age a ON c.AgeID = a.AgeID
    LEFT JOIN measureunit1 m ON c.UCID = m.UCID
    LEFT JOIN yeartype y ON c.YearTypeID = y.YearTypeID
    LEFT JOIN reference r ON c.ReferenceID = r.ReferenceID
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=individual_consumption.csv');
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
            header('Content-Disposition: attachment;filename=individual_consumption.xls');
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
