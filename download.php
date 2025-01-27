<?php
require_once('db_connect.php');

if (isset($_POST['format'], $_POST['tableName'])) {
    $format = $_POST['format'];
    $tableName = $_POST['tableName'];
    $countryName = $_POST['countryName'] ?? '';
    $vehicleNames = isset($_POST['vehicleNames']) ? explode(',', $_POST['vehicleNames']) : [];

    // Check if the columns exist in the table
    $columnsResult = $conn->query("SHOW COLUMNS FROM $tableName");
    $columns = [];
    while ($column = $columnsResult->fetch_assoc()) {
        $columns[] = $column['Field'];
    }

    // Construct the query based on the parameters
    $selectFields = ["$tableName.*"];
    $joins = [];
    if (in_array('Country_ID', $columns) && $tableName !== 'country') {
        $selectFields[] = "c.Country_Name";
        $joins[] = "LEFT JOIN country c ON $tableName.Country_ID = c.Country_ID";
    }
    if (in_array('Vehicle_ID', $columns) && $tableName !== 'vehicle') {
        $selectFields[] = "v.Vehicle_Name";
        $joins[] = "LEFT JOIN vehicle v ON $tableName.Vehicle_ID = v.Vehicle_ID";
    }

    $query = "SELECT " . implode(', ', $selectFields) . " FROM $tableName";
    if ($joins) {
        $query .= ' ' . implode(' ', $joins);
    }

    $conditions = [];
    if ($countryName && in_array('Country_Name', $columns)) {
        $conditions[] = "c.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
    }
    if ($vehicleNames && in_array('Vehicle_Name', $columns)) {
        $vehicleConditions = array_map(function($vehicle) use ($conn) {
            return "v.Vehicle_Name = '" . $conn->real_escape_string($vehicle) . "'";
        }, $vehicleNames);
        $conditions[] = '(' . implode(' OR ', $vehicleConditions) . ')';
    }
    if ($conditions) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        if ($format == 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=' . $tableName . '.csv');
            $output = fopen('php://output', 'w');
            $columns = array_keys($result->fetch_assoc());
            fputcsv($output, $columns);
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                fputcsv($output, $row);
            }
            fclose($output);
        } elseif ($format == 'excel') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . $tableName . '.xls');
            echo '<table border="1">';
            $columns = array_keys($result->fetch_assoc());
            echo '<tr><th>' . implode('</th><th>', $columns) . '</th></tr>';
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                echo '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
            }
            echo '</table>';
        }
    } else {
        echo 'No data found';
    }
    $conn->close();
}
?>
