<?php

// Include the database connection
include('db_connect.php');

// Fetch all table names
$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
} else {
    die("Error fetching tables: " . $conn->error);
}

// Fetch all vehicle names from the foodvehicle table
$vehicleNames = [];
$vehicleResult = $conn->query("SELECT VehicleName FROM foodvehicle ORDER BY VehicleName ASC");
if ($vehicleResult) {
    while ($vehicleRow = $vehicleResult->fetch_assoc()) {
        $vehicleNames[] = $vehicleRow['VehicleName'];
    }
} else {
    die("Error fetching vehicle names: " . $conn->error);
}

// Fetch all country names from the country table
$countryNames = [];
$countryResult = $conn->query("SELECT CountryName FROM country ORDER BY CountryName ASC");
if ($countryResult) {
    while ($countryRow = $countryResult->fetch_assoc()) {
        $countryNames[] = $countryRow['CountryName'];
    }
} else {
    die("Error fetching country names: " . $conn->error);
}

// Define the list of valid tables with formatted names
$validTables = [
    'adultmaleequivalent' => 'Adult Male Equivalent',
    'age' => 'Age',
    'brand' => 'Brand',
    'company' => 'Company',
    'consumption' => 'Consumption',
    'country' => 'Country',
    'distribution' => 'Distribution',
    'distributionchannel' => 'Distribution Channel',
    'entity' => 'Entity',
    'extractionconversion' => 'Extraction Conversion',
    'foodtype' => 'Food Type',
    'foodvehicle' => 'Food Vehicle',
    'gender' => 'Gender',
    'geographylevel1' => 'Geography Level 1',
    'geographylevel2' => 'Geography Level 2',
    'geographylevel3' => 'Geography Level 3',
    'individualconsumption' => 'Individual Consumption',
    'measurecurrency' => 'Measure Currency',
    'measureunit1' => 'Measure Unit 1',
    'packagingtype' => 'Packaging Type',
    'producerprocessor' => 'Producer Processor',
    'producerreference' => 'Producer Reference',
    'producersku' => 'Producer SKU',
    'product' => 'Product',
    'processingstage' => 'Processing Stage',
    'reference' => 'Reference',
    'subdistributionchannel' => 'Sub Distribution Channel',
    'supply' => 'Supply',
    'supply_in_chain_final' => 'Supply in Chain Final',
    'yeartype' => 'Year Type'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Essential Commodities Supply Database (Admin)</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .center-title {
            text-align: center;
            color: #000;
            margin-top: 15px;
            margin-bottom: 15px;
            font-weight: 300;
        }

        .current-time {
            text-align: right;
            font-size: 0.9em;
            color: #6c757d;
            margin-top: 1px;
        }

        .debug-card {
            margin-top: 2px;
            padding: 2px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #c8e5bf;
            border-radius: 1px;
            display: flex;
            justify-content: space-between;
            align-items: left;
            width: 100%;
            text-align: left;
            /* Center-align all text in the card */
        }

        .card {
            margin-top: 1%;
            padding: 10px;
            /* Increased padding */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #c8e5bf;
            border-radius: 1px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            text-align: center;
            /* Center-align all text in the card */
        }

        .table-selection {
            margin-bottom: 20px;
        }

        .card-title {
            color: #17a2b8;
            margin-bottom: 20px;
        }

        /* Table styling for borders */
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #d3a79e;
            text-align: center;
            /* Center-align all table content */
            vertical-align: middle;
            /* Vertically center-align all table content */
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-group .btn {
            margin-top: 20px;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }

        .download-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            var selectedTable = '';

            $('form').on('submit', function(event) {
                event.preventDefault();
                selectedTable = $('select[name="tableName"]').val();
                var vehicleName = $('select[name="vehicleName"]').val();
                var countryName = $('select[name="countryName"]').val();
                if (selectedTable) {
                    $.post('display_table_user.php', {
                        tableName: selectedTable,
                        vehicleName: vehicleName,
                        countryName: countryName
                    }, function(data) {
                        if (data.trim() === '') {
                            $('#table-view').html('<h2 class="text-left card-title">Table Name: ' +
                                <?php echo json_encode($validTables); ?>[selectedTable] + '</h2><p>No data available for the selected filter.</p>');
                        } else {
                            $('#table-view').html('<h2 class="text-left card-title">Table Name: ' +
                                <?php echo json_encode($validTables); ?>[selectedTable] + '</h2>' + data);
                        }
                        $('.download-buttons').show();
                        $('#download-csv-btn').data('params', {
                            tableName: selectedTable,
                            vehicleName: vehicleName,
                            countryName: countryName
                        });
                        $('#download-excel-btn').data('params', {
                            tableName: selectedTable,
                            vehicleName: vehicleName,
                            countryName: countryName
                        });
                        $('#modify-data-btn').show();
                    });
                } else {
                    $('#table-view').html('');
                    $('.download-buttons').hide();
                    $('#modify-data-btn').hide();
                }
            });

            $('#modify-data-btn').on('click', function() {
                if (selectedTable) {
                    if (selectedTable == 'adultmaleequivalent') {
                        window.location.href = 'input_tables/input_adult_male_equivalent.php';
                    } else if (selectedTable == 'age') {
                        window.location.href = 'input_tables/input_age.php';
                    } else if (selectedTable == 'brand') {
                        window.location.href = 'input_tables/input_brand.php';
                    } else if (selectedTable == 'company') {
                        window.location.href = 'input_tables/input_company.php';
                    } else if (selectedTable == 'consumption') {
                        window.location.href = 'input_tables/input_consumption.php';
                    } else if (selectedTable == 'country') {
                        window.location.href = 'input_tables/input_country.php';
                    } else if (selectedTable == 'distribution') {
                        window.location.href = 'input_tables/input_distribution.php';
                    } else if (selectedTable == 'distributionchannel') {
                        window.location.href = 'input_tables/input_distribution_channel.php';
                    } else if (selectedTable == 'entity') {
                        window.location.href = 'input_tables/input_entity.php';
                    } else if (selectedTable == 'extractionconversion') {
                        window.location.href = 'input_tables/input_extraction_conversion.php';
                    } else if (selectedTable == 'foodtype') {
                        window.location.href = 'input_tables/input_foodtype.php';
                    } else if (selectedTable == 'foodvehicle') {
                        window.location.href = 'input_tables/input_foodvehicle.php';
                    } else if (selectedTable == 'gender') {
                        window.location.href = 'input_tables/input_gender.php';
                    } else if (selectedTable == 'geographylevel1') {
                        window.location.href = 'input_tables/input_geography_level1.php';
                    } else if (selectedTable == 'geographylevel2') {
                        window.location.href = 'input_tables/input_geography_level2.php';
                    } else if (selectedTable == 'geographylevel3') {
                        window.location.href = 'input_tables/input_geography_level3.php';
                    } else if (selectedTable == 'individualconsumption') {
                        window.location.href = 'input_tables/input_consumption.php';
                    } else if (selectedTable == 'measurecurrency') {
                        window.location.href = 'input_tables/input_measure_currency.php';
                    } else if (selectedTable == 'measureunit1') {
                        window.location.href = 'input_tables/input_measure_unit1.php';
                    } else if (selectedTable == 'packagingtype') {
                        window.location.href = 'input_tables/input_packaging_type.php';
                    } else if (selectedTable == 'producerprocessor') {
                        window.location.href = 'input_tables/input_producer_processor.php';
                    } else if (selectedTable == 'producerreference') {
                        window.location.href = 'input_tables/input_producer_reference.php';
                    } else if (selectedTable == 'producersku') {
                        window.location.href = 'input_tables/input_producer_sku.php';
                    } else if (selectedTable == 'product') {
                        window.location.href = 'input_tables/input_product.php';
                    } else if (selectedTable == 'processingstage') {
                        window.location.href = 'input_tables/input_processing_stage.php';
                    } else if (selectedTable == 'reference') {
                        window.location.href = 'input_tables/input_reference.php';
                    } else if (selectedTable == 'subdistributionchannel') {
                        window.location.href = 'input_tables/input_sub_distribution_channel.php';
                    } else if (selectedTable == 'supply') {
                        window.location.href = 'input_tables/input_supply.php';
                    } else if (selectedTable == 'supply_in_chain_final') {
                        alert('This table can\'t be updated separately. Instead, update supply, distribution, and consumption tables to show the updated values here.');
                    } else if (selectedTable == 'yeartype') {
                        window.location.href = 'input_tables/input_year_type.php';
                    } else {
                        alert('Invalid table selected.');
                    }
                } else {
                    alert('Please select a table first.');
                }
            });


            $('#download-csv-btn').on('click', function() {
                var params = $(this).data('params');
                if (params) {
                    $('<form>', {
                        "id": "downloadForm",
                        "html": '<input type="hidden" name="format" value="csv">' +
                            '<input type="hidden" name="tableName" value="' + params.tableName + '">' +
                            '<input type="hidden" name="vehicleName" value="' + params.vehicleName + '">' +
                            '<input type="hidden" name="countryName" value="' + params.countryName + '">',
                        "action": 'download.php',
                        "method": 'post'
                    }).appendTo(document.body).submit();
                }
            });

            $('#download-excel-btn').on('click', function() {
                var params = $(this).data('params');
                if (params) {
                    $('<form>', {
                        "id": "downloadForm",
                        "html": '<input type="hidden" name="format" value="excel">' +
                            '<input type="hidden" name="tableName" value="' + params.tableName + '">' +
                            '<input type="hidden" name="vehicleName" value="' + params.vehicleName + '">' +
                            '<input type="hidden" name="countryName" value="' + params.countryName + '">',
                        "action": 'download.php',
                        "method": 'post'
                    }).appendTo(document.body).submit();
                }
            });

            $('.download-buttons').hide();
            $('#modify-data-btn').hide();
        });
    </script>
</head>

<body>
    <div class="container" style="max-width: 1200px;">
        <h1 class="center-title">Database on Essential Commodities Supply for Human Consumption (Admin)</h1>






        <div class="card">
            <div style="display: flex; align-items: center; width: 100%;">
                <form method="post" style="display: flex; flex-direction: row; gap: 20px; width: 100%;">
                    <div class="form-group" style="flex: 1;">
                        <label for="tableName"><strong>Choose Table</strong></label>
                        <select name="tableName" class="form-control">
                            <option value="">Select a table</option>
                            <?php
                            foreach ($validTables as $table => $formattedName): ?>
                                <option value="<?php echo htmlspecialchars($table); ?>">
                                    <?php echo htmlspecialchars($formattedName); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary mt-2">Show Table</button>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="vehicleName"><strong>Vehicle Name</strong></label>
                        <select name="vehicleName" class="form-control">
                            <option value="">Select a vehicle name</option>
                            <?php
                            foreach ($vehicleNames as $vehicleName): ?>
                                <option value="<?php echo htmlspecialchars($vehicleName); ?>">
                                    <?php echo htmlspecialchars($vehicleName); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="countryName"><strong>Country Name</strong></label>
                        <select name="countryName" class="form-control">
                            <option value="">Select a country name</option>
                            <?php
                            foreach ($countryNames as $countryName): ?>
                                <option value="<?php echo htmlspecialchars($countryName); ?>">
                                    <?php echo htmlspecialchars($countryName); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>

            <div class="current-time">
                <?php
                echo "Last Updated: March 13, 2025, 11:50 am";
                ?>
            </div>
        </div>

        <div id="table-view" style="margin-top: 20px;"></div>
        <div class="download-buttons">
            <button id="download-csv-btn" class="btn btn-success">Download CSV</button>
            <button id="download-excel-btn" class="btn btn-success">Download Excel</button>
            <button id="modify-data-btn" class="btn btn-primary">Modify Data</button>
        </div>
        <div id="debug-card">

            <?php
            // include('debug_table.php');
            ?>

        </div>
    </div>
    <br><br><br>


    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
// Close the database connection
$conn->close();
?>