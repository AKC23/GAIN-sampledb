<?php

if (isset($_POST['format'])) {
    $format = $_POST['format'];

    $sql = "
    SELECT 
        ec.ExtractionID,
        ec.ExtractionRate,
        fv.VehicleName,
        ft.FoodTypeName,
        r.ReferenceNumber
    FROM 
        extractionconversion ec
    JOIN 
        foodvehicle fv ON ec.VehicleID = fv.VehicleID
    JOIN 
        foodtype ft ON ec.FoodTypeID = ft.FoodTypeID
    JOIN 
        reference r ON ec.ReferenceID = r.ReferenceID
    ORDER BY 
        ec.ExtractionID
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=extraction_conversion.csv');
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
            header('Content-Disposition: attachment;filename=extraction_conversion.xls');
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
