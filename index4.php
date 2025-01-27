<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oil & Wheat Database - BD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .center-title {
            text-align: center;
            color: #000;
            margin-top: 20px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .current-time {
            text-align: right;
            font-size: 0.9em;
            color: #6c757d;
            margin-top: 1px;
        }

        .card {
            margin-top: 20px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #c8e5bf;
            border-radius: 1px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
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

        .vehicle-selection {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-left: 40px;
        }

        .vehicle-selection-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .vehicle-options {
            display: flex;
            gap: 10px;
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
            $('form').on('submit', function(event) {
                event.preventDefault();
                var tableName = $('select[name="tableName"]').val();
                var countryName = $('select[name="countryName"]').val();
                var vehicleNames = $('input[name="vehicleName[]"]:checked').map(function() {
                    return this.value;
                }).get();
                if (tableName) {
                    $.post('display_table2.php', {
                        tableName: tableName,
                        countryName: countryName,
                        vehicleNames: vehicleNames
                    }, function(data) {
                        $('#table-view').html('<h2 class="text-left card-title">Table Name: ' + tableName + '</h2>' + data);
                        $('.download-buttons').show();
                        $('#download-csv-btn').data('params', {
                            tableName: tableName,
                            countryName: countryName,
                            vehicleNames: vehicleNames
                        });
                        $('#download-excel-btn').data('params', {
                            tableName: tableName,
                            countryName: countryName,
                            vehicleNames: vehicleNames
                        });
                    });
                } else {
                    $('#table-view').html('');
                    $('.download-buttons').hide();
                }
            });

            $('input[name="vehicleName[]"]').on('change', function() {
                var tableName = $('select[name="tableName"]').val();
                var vehicleNames = $('input[name="vehicleName[]"]:checked').map(function() {
                    return this.value;
                }).get();
                var countryName = $('select[name="countryName"]').val();
                if (tableName) {
                    $.post('display_table2.php', {
                        tableName: tableName,
                        vehicleNames: vehicleNames,
                        countryName: countryName
                    }, function(data) {
                        $('#table-view').html('<h2 class="text-left card-title">Table Name: ' + tableName + '</h2>' + data);
                        $('.download-buttons').show();
                        $('#download-csv-btn').data('params', {
                            tableName: tableName,
                            countryName: countryName,
                            vehicleNames: vehicleNames
                        });
                        $('#download-excel-btn').data('params', {
                            tableName: tableName,
                            countryName: countryName,
                            vehicleNames: vehicleNames
                        });
                    });
                }
            });

            $('select[name="countryName"]').on('change', function() {
                var tableName = $('select[name="tableName"]').val();
                var vehicleNames = $('input[name="vehicleName[]"]:checked').map(function() {
                    return this.value;
                }).get();
                var countryName = $('select[name="countryName"]').val();
                if (tableName) {
                    $.post('display_table2.php', {
                        tableName: tableName,
                        vehicleNames: vehicleNames,
                        countryName: countryName
                    }, function(data) {
                        $('#table-view').html('<h2 class="text-left card-title">Table Name: ' + tableName + '</h2>' + data);
                        $('.download-buttons').show();
                        $('#download-csv-btn').data('params', {
                            tableName: tableName,
                            countryName: countryName,
                            vehicleNames: vehicleNames
                        });
                        $('#download-excel-btn').data('params', {
                            tableName: tableName,
                            countryName: countryName,
                            vehicleNames: vehicleNames
                        });
                    });
                }
            });

            $('#update-table-btn').on('click', function() {
                window.location.href = 'input_table.php';
            });

            $('#download-csv-btn').on('click', function() {
                var params = $(this).data('params');
                if (params) {
                    $('<form>', {
                        "id": "downloadForm",
                        "html": '<input type="hidden" name="format" value="csv">' +
                                '<input type="hidden" name="tableName" value="' + params.tableName + '">' +
                                '<input type="hidden" name="countryName" value="' + params.countryName + '">' +
                                '<input type="hidden" name="vehicleNames" value="' + params.vehicleNames.join(',') + '">',
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
                                '<input type="hidden" name="countryName" value="' + params.countryName + '">' +
                                '<input type="hidden" name="vehicleNames" value="' + params.vehicleNames.join(',') + '">',
                        "action": 'download.php',
                        "method": 'post'
                    }).appendTo(document.body).submit();
                }
            });

            $('.download-buttons').hide();
        });
    </script>
</head>

<body>
    <div class="container">
        <h1 class="center-title">Database on Edible Oil and Wheat Flour Supply for Human Consumption in Bangladesh</h1>

        <div class="card">
            <!-- Dropdown or other content on the left side -->
            <div style="display: flex; align-items: center; width: 100%;">
                <form method="post" style="display: flex; flex-direction: row; gap: 20px; width: 100%;">
                    <div class="form-group" style="flex: 1;">
                        <label for="tableName"><strong>Choose Table</strong></label>
                        <select name="tableName" class="form-control">
                            <option value="">Select a table</option>
                            <?php
                            require_once('db_connect.php');  // Changed to require_once
                            $result = $conn->query("SHOW TABLES");
                            $validTables = [
                                'foodvehicle',
                                'foodtype',
                                'country',
                                'processing_stage',
                                'reference',  // Added reference table
                                'measure_unit',
                                'measure_period',
                                'measure_currency',
                                'geography',
                                'entities',
                                'producer_skus',
                                'extraction_conversion',
                                'producer_processor',
                                'packaging_type'
                            ];
                            $selectedTable = $_POST['tableName'] ?? '';
                            while ($row = $result->fetch_array()) {
                                $table = $row[0];
                                $selected = ($table == $selectedTable) ? 'selected' : '';
                                if (in_array($table, $validTables)) {
                                    echo "<option value='$table' $selected>" . htmlspecialchars($table) . "</option>";
                                }
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-primary mt-2">Show Table</button>
                    </div>
                    <div class="vehicle-selection" style="flex: 1;">
                        <div class="vehicle-selection-title"><strong>Vehicle Name</strong></div>
                        <div class="vehicle-options">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="vehicleName[]" value="Edible Oil" id="vehicleEdibleOil">
                                <label class="form-check-label" for="vehicleEdibleOil">
                                    Edible Oil
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="vehicleName[]" value="Wheat" id="vehicleWheat">
                                <label class="form-check-label" for="vehicleWheat">
                                    Wheat
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="countryName"><strong>Choose Country</strong></label>
                        <select name="countryName" id="countryName" class="form-control">
                            <option value="">Select Country</option>
                            <?php
                            include('db_connect.php');
                            $countryQuery = "SELECT DISTINCT Country_Name FROM country ORDER BY Country_Name";
                            $countryResult = $conn->query($countryQuery);
                            if ($countryResult->num_rows > 0) {
                                while ($row = $countryResult->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['Country_Name']) . "'>" . htmlspecialchars($row['Country_Name']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Last Updated Date and Time on the right side of the card -->
            <div class="current-time">
                <?php
                echo "Last Updated: January 26, 2025, 12:30 am";
                ?>
            </div>
        </div>

        <div id="table-view" style="margin-top: 20px;"></div>
        <div class="download-buttons">
            <button id="download-csv-btn" class="btn btn-success">Download CSV</button>
            <button id="download-excel-btn" class="btn btn-success">Download Excel</button>
        </div>
        <?php
        // Display requested table (using the same connection)
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
            $tableName = $_POST['tableName'];
            $vehicleNames = $_POST['vehicleName'] ?? [];
            $countryName = $_POST['countryName'] ?? '';
            echo "<h2 class='text-left card-title'>Table Name: " . htmlspecialchars($tableName) . "</h2>";

            echo '<div class="table-responsive" style="margin-top: 20px;">';
            try {
                include('display_table2.php');
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error displaying table: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            echo '</div>';
        }

        echo "<br><br><br>";

        // Include debug_table.php for debugging information
        // include('debug_table.php');


        // Ensure the connection is not closed before all operations are completed
        if (isset($conn) && $conn instanceof mysqli) {
            $conn->close();
            //echo "<br>Database connection closed successfully.<br>";
        }

        ?>
    </div>

    <!-- Bootstrap and jQuery scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>