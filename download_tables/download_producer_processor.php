<?php

if (isset($_POST['format'])) {
    $format = $_POST['format'];

    $sql = "
    SELECT 
    pp.ProducerProcessorID,
    e.ProducerProcessorName,
    c.CompanyName,
    fv.VehicleName,
    gl1.AdminLevel1,
    gl2.AdminLevel2,
    gl3.AdminLevel3,
    co.CountryName,
    pp.TaskDoneByEntity,
    pp.ProductionCapacityVolumeMTY,
    pp.PercentageOfCapacityUsed,
    pp.AnnualProductionSupplyVolumeMTY,
    pr.IdentifierNumber,
    pr.IdentifierReferenceSystem
FROM 
    producerprocessor pp
JOIN 
    entity e ON pp.EntityID = e.EntityID
JOIN 
    company c ON e.CompanyID = c.CompanyID
JOIN 
    foodvehicle fv ON e.VehicleID = fv.VehicleID
LEFT JOIN 
    geographylevel1 gl1 ON e.GL1ID = gl1.GL1ID
LEFT JOIN 
    geographylevel2 gl2 ON e.GL2ID = gl2.GL2ID
LEFT JOIN 
    geographylevel3 gl3 ON e.GL3ID = gl3.GL3ID
JOIN 
    country co ON e.CountryID = co.CountryID
LEFT JOIN 
    producerreference pr ON pp.ProducerReferenceID = pr.ProducerReferenceID
ORDER BY 
    pp.ProducerProcessorID
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=producer_processor.csv');
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
            header('Content-Disposition: attachment;filename=producer_processor.xls');
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
