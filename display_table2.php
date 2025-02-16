<?php
// Include the database connection
include('db_connect.php');

// Ensure that $tableName is set and valid
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
    $tableName = $_POST['tableName'];
    $vehicleName = $_POST['vehicleName'];
    $countryName = $_POST['countryName'];

    // Handle tables
    if (!empty($tableName)) {
        $query = "SELECT * FROM $tableName";
        $conditions = [];
        if (!empty($vehicleName)) {
            if ($tableName == 'foodtype') {
                // Get VehicleID based on VehicleName
                $vehicleResult = $conn->query("SELECT VehicleID FROM foodvehicle WHERE VehicleName = '$vehicleName'");
                if ($vehicleRow = $vehicleResult->fetch_assoc()) {
                    $vehicleID = $vehicleRow['VehicleID'];
                    $conditions[] = "VehicleID = '$vehicleID'";
                }
            } else {
                $conditions[] = "VehicleName = '$vehicleName'";
            }
        }
        if (!empty($countryName)) {
            $conditions[] = "CountryName = '$countryName'";
        }
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }

        try {
            $result = $conn->query($query);
            if ($result->num_rows > 0) {
                if ($tableName == 'country') {
                    include('display_tables/display_country.php');
                } elseif ($tableName == 'foodvehicle') {
                    include('display_tables/display_foodvehicle.php');
                } elseif ($tableName == 'foodtype') {
                    include('display_tables/display_foodtype.php');
                }
            } else {
                echo "No records found.";
            }
        } catch (mysqli_sql_exception $e) {
            // If filtering fails, show the full table
            $result = $conn->query("SELECT * FROM $tableName");
            if ($result->num_rows > 0) {
                if ($tableName == 'country') {
                    include('display_tables/display_country.php');
                } elseif ($tableName == 'foodvehicle') {
                    include('display_tables/display_foodvehicle.php');
                } elseif ($tableName == 'foodtype') {
                    include('display_tables/display_foodtype.php');
                }
            } else {
                echo "No records found.";
            }
        }
    }
}
// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
