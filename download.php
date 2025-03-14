<?php
require_once('db_connect.php');

if (isset($_POST['format'], $_POST['tableName'])) {
    $format = $_POST['format'];
    $tableName = $_POST['tableName'];
    $countryName = $_POST['countryName'] ?? '';
    $vehicleNames = isset($_POST['vehicleNames']) ? explode(',', $_POST['vehicleNames']) : [];

    // Check if the table exists or is a special case
    if ($tableName !== 'individualconsumption' && $tableName !== 'supply_in_chain_final' && $tableName !== 'adultmaleequivalent' && $tableName !== 'consumption') {
        $tableExistsQuery = "SHOW TABLES LIKE '$tableName'";
        $tableExistsResult = $conn->query($tableExistsQuery);

        if ($tableExistsResult->num_rows == 0) {
            die("Error: Table '$tableName' doesn't exist.");
        }
    }

    // Fetch data for special cases
    if ($tableName === 'individualconsumption') {
        include('download_tables/download_individual_consumption.php');
        exit();
    } elseif ($tableName === 'supply_in_chain_final') {
        include('download_tables/download_supply_in_chain_final.php');
        exit();
    } elseif ($tableName === 'adultmaleequivalent') {
        include('download_tables/download_adult_male_equivalent.php');
        exit();
    } elseif ($tableName === 'consumption') {
        include('download_tables/download_consumption.php');
        exit();
    } else {
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
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            die('No data found');
        }
    }

    if ($format == 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=' . $tableName . '.csv');
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
        header('Content-Disposition: attachment;filename=' . $tableName . '.xls');
        echo '<table border="1">';
        if (isset($data[0])) {
            echo '<tr><th>' . implode('</th><th>', array_keys($data[0])) . '</th></tr>';
            foreach ($data as $row) {
                echo '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
            }
        }
        echo '</table>';
    }
    $conn->close();
}
?>
