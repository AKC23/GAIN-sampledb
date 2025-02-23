<?php

// display_table2.php 
// This script displays the contents of a table based on the user's input
// It also filters the table based on the user's input for vehicleName and countryName

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
            if ($tableName == 'foodtype' || $tableName == 'processing_stage') {
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
            if ($tableName == 'geographylevel1' || $tableName == 'producer_reference') {
                // Get CountryID based on CountryName
                $countryResult = $conn->query("SELECT CountryID FROM country WHERE CountryName = '$countryName'");
                if ($countryRow = $countryResult->fetch_assoc()) {
                    $countryID = $countryRow['CountryID'];
                    $conditions[] = "CountryID = '$countryID'";
                }
            } else {
                $conditions[] = "CountryName = '$countryName'";
            }
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
                } elseif ($tableName == 'company') {
                    include('display_tables/display_company.php');
                } elseif ($tableName == 'brand') {
                    include('display_tables/display_brand.php');
                } elseif ($tableName == 'processing_stage') {
                    include('display_tables/display_processing_stage.php');
                } elseif ($tableName == 'geographylevel1') {
                    include('display_tables/display_geography_level1.php');
                } elseif ($tableName == 'geographylevel2') {
                    include('display_tables/display_geography_level2.php');
                } elseif ($tableName == 'geographylevel3') {
                    include('display_tables/display_geography_level3.php');
                } elseif ($tableName == 'producerreference') {
                    include('display_tables/display_producer_reference.php');
                } elseif ($tableName == 'measureunit1') {
                    include('display_tables/display_measure_unit1.php');
                } elseif ($tableName == 'measurecurrency') {
                    include('display_tables/display_measure_currency.php');
                } elseif ($tableName == 'packagingtype') {
                    include('display_tables/display_packaging_type.php');
                } elseif ($tableName == 'subdistributionchannel') {
                    include('display_tables/display_sub_distribution_channel.php');
                } elseif ($tableName == 'distributionchannel') {
                    include('display_tables/display_distribution_channel.php');
                } elseif ($tableName == 'reference') {
                    include('display_tables/display_reference.php');
                } elseif ($tableName == 'yeartype') {
                    include('display_tables/display_year_type.php');
                } elseif ($tableName == 'age') {
                    include('display_tables/display_age.php');
                } elseif ($tableName == 'gender') {
                    include('display_tables/display_gender.php');
                } elseif ($tableName == 'extractionconversion') {
                    include('display_tables/display_extraction_conversion.php');
                } elseif ($tableName == 'adultmaleequivalent') {
                    include('display_tables/display_adult_male_equivalent.php');
                } elseif ($tableName == 'product') {
                    include('display_tables/display_product.php');
                } elseif ($tableName == 'entity') {
                    include('display_tables/display_entity.php');
                } elseif ($tableName == 'producerprocessor') {
                    include('display_tables/display_producer_processor.php');
                } elseif ($tableName == 'producersku') {
                    include('display_tables/display_producer_sku.php');
                } elseif ($tableName == 'distribution') {
                    include('display_tables/display_distribution.php');
                }
            } else {
                echo "No records found.";
            }
        } catch (mysqli_sql_exception $e) {
            echo "Error: " . $e->getMessage();
            // If filtering fails, show the full table
            $result = $conn->query("SELECT * FROM $tableName");
            if ($result->num_rows > 0) {
                if ($tableName == 'country') {
                    include('display_tables/display_country.php');
                } elseif ($tableName == 'foodvehicle') {
                    include('display_tables/display_foodvehicle.php');
                } elseif ($tableName == 'foodtype') {
                    include('display_tables/display_foodtype.php');
                } elseif ($tableName == 'company') {
                    include('display_tables/display_company.php');
                } elseif ($tableName == 'brand') {
                    include('display_tables/display_brand.php');
                } elseif ($tableName == 'processing_stage') {
                    include('display_tables/display_processing_stage.php');
                } elseif ($tableName == 'geographylevel1') {
                    include('display_tables/display_geography_level1.php');
                } elseif ($tableName == 'geographylevel2') {
                    include('display_tables/display_geography_level2.php');
                } elseif ($tableName == 'geographylevel3') {
                    include('display_tables/display_geography_level3.php');
                } elseif ($tableName == 'producerreference') {
                    include('display_tables/display_producer_reference.php');
                } elseif ($tableName == 'measureunit1') {
                    include('display_tables/display_measure_unit1.php');
                } elseif ($tableName == 'measurecurrency') {
                    include('display_tables/display_measure_currency.php');
                } elseif ($tableName == 'packagingtype') {
                    include('display_tables/display_packaging_type.php');
                } elseif ($tableName == 'subdistributionchannel') {
                    include('display_tables/display_sub_distribution_channel.php');
                } elseif ($tableName == 'distributionchannel') {
                    include('display_tables/display_distribution_channel.php');
                } elseif ($tableName == 'reference') {
                    include('display_tables/display_reference.php');
                } elseif ($tableName == 'yeartype') {
                    include('display_tables/display_year_type.php');
                } elseif ($tableName == 'age') {
                    include('display_tables/display_age.php');
                } elseif ($tableName == 'gender') {
                    include('display_tables/display_gender.php');
                } elseif ($tableName == 'extractionconversion') {
                    include('display_tables/display_extraction_conversion.php');
                } elseif ($tableName == 'adultmaleequivalent') {
                    include('display_tables/display_adult_male_equivalent.php');
                } elseif ($tableName == 'product') {
                    include('display_tables/display_product.php');
                } elseif ($tableName == 'entity') {
                    include('display_tables/display_entity.php');
                } elseif ($tableName == 'producerprocessor') {
                    include('display_tables/display_producer_processor.php');
                } elseif ($tableName == 'producersku') {
                    include('display_tables/display_producer_sku.php');
                } elseif ($tableName == 'distribution') {
                    include('display_tables/display_distribution.php');
                }
            } else {
                echo "No records found.";
            }
        }
    }
}
// Note: Do not close the database connection here
// The connection will be closed by index.php
