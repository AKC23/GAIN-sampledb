<?php

if (isset($_POST['format'])) {
    $format = $_POST['format'];

    $sql = "
    SELECT 
        ps.SKUID,
        p.ProductName,
        c.CompanyName,
        ps.SKU,
        ps.Unit,
        pt.PackagingTypeName,
        ps.Price,
        mc.CurrencyName,
        r.ReferenceNumber
    FROM 
        producersku ps
    JOIN 
        product p ON ps.ProductID = p.ProductID
    JOIN 
        company c ON ps.CompanyID = c.CompanyID
    JOIN 
        packagingtype pt ON ps.PackagingTypeID = pt.PackagingTypeID
    JOIN 
        measurecurrency mc ON ps.CurrencyID = mc.MCID
    JOIN 
        reference r ON ps.ReferenceID = r.ReferenceID
    ORDER BY 
        ps.SKUID
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=producer_sku.csv');
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
            header('Content-Disposition: attachment;filename=producer_sku.xls');
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
