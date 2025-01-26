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
            text-align: center; /* Center-align all table content */
            vertical-align: middle; /* Vertically center-align all table content */
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
    </style>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(event) {
                event.preventDefault();
                var tableName = $('select[name="tableName"]').val();
                var countryName = $('select[name="countryName"]').val();
                if (tableName) {
                    $.post('display_table2.php', { tableName: tableName, countryName: countryName }, function(data) {
                        $('#table-view').html(data);
                    });
                } else {
                    $('#table-view').html('');
                }
            });

            $('input[name="vehicleName"]').on('change', function() {
                var tableName = $('select[name="tableName"]').val();
                var vehicleName = $('input[name="vehicleName"]:checked').val();
                var countryName = $('select[name="countryName"]').val();
                if (tableName) {
                    $.post('display_table2.php', { tableName: tableName, vehicleName: vehicleName, countryName: countryName }, function(data) {
                        $('#table-view').html(data);
                    });
                }
            });

            $('select[name="countryName"]').on('change', function() {
                var tableName = $('select[name="tableName"]').val();
                var vehicleName = $('input[name="vehicleName"]:checked').val();
                var countryName = $('select[name="countryName"]').val();
                if (tableName) {
                    $.post('display_table2.php', { tableName: tableName, vehicleName: vehicleName, countryName: countryName }, function(data) {
                        $('#table-view').html(data);
                    });
                }
            });

            $('#update-table-btn').on('click', function() {
                window.location.href = 'input_table.php';
            });
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
                        <div class="vehicle-selection-title">Vehicle Name</div>
                        <div class="vehicle-options">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="vehicleName" value="Edible Oil" id="vehicleEdibleOil">
                                <label class="form-check-label" for="vehicleEdibleOil">
                                    Edible Oil
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="vehicleName" value="Wheat" id="vehicleWheat">
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

        <?php
        // Display requested table (using the same connection)
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
            $tableName = $_POST['tableName'];
            $vehicleName = $_POST['vehicleName'] ?? '';
            $countryName = $_POST['countryName'] ?? '';
            echo "<h2 class='text-left card-title'>Data Table: " . htmlspecialchars($tableName) . "</h2>";

            echo '<div class="table-responsive" style="margin-top: 20px;">';
            try {
                include('display_table2.php');
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error displaying table: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            echo '</div>';
        } 

        try {
            // Ensure the connection is open before executing queries
            if ($conn->ping()) {
                // Disable foreign key checks for dropping tables
                $conn->query('SET FOREIGN_KEY_CHECKS = 0');
                echo "<br>";
                echo "<br>";
                echo "<br>";
                echo "<h2 class='center-title'>Drop & Create Database Tables</h2>";

                // Update drop tables order to ensure proper dependency handling
                $dropTables = [
                    'extraction_conversion', 
                    'entities',
                    'geography',
                    'producer_processor',
                    'measure_unit',
                    'measure_period',
                    'measure_currency',
                    'processing_stage',
                    'reference',  // Added reference table
                    'producer_skus',
                    'packaging_type',
                    'foodvehicle',
                    'foodtype',
                    'country',
                    'table1', // Temporary tables
                    'table2' // Temporary tables
                ];

                foreach ($dropTables as $table) {
                    $sql = "DROP TABLE IF EXISTS " . $table;
                    if ($conn->query($sql) === TRUE) {
                        echo "Table '$table' dropped successfully.<br>";
                    }
                }

                // Re-enable foreign key checks
                $conn->query('SET FOREIGN_KEY_CHECKS = 1');

                // Level 0: Base tables with no dependencies
                echo "<h3>Creating base tables (Level 0)...</h3>";
                include('insert_foodvehicle.php');
                include('insert_country.php');
                include('insert_measure_unit.php');
                include('insert_measure_period.php');
                include('insert_measure_currency.php');
                include('insert_reference.php');  

                // Level 1: Tables that depend on base tables
                echo "<h3>Creating Level 1 tables...</h3>";
                include('insert_foodtype.php');      // Depends on: FoodVehicle
                include('insert_processing_stage.php');     // Depends on: FoodVehicle
                include('insert_geography.php');     // Depends on: country
                include('insert_entities.php');   

                // Level 2: Tables depending on Level 1
                echo "<h3>Creating Level 2 tables...</h3>";
                
                          
                include('insert_producer_processor.php'); // Depends on: Country, FoodVehicle
                include('insert_extraction_conversion.php'); // Depends on: FoodVehicle, FoodType

                // Level 3: Tables depending on Level 2
                echo "<h3>Creating Level 3 tables...</h3>";
                include('insert_total_food_import.php');
                include('insert_total_crop_import.php');
                include('insert_packaging_type.php');
                

                // Level 4: Tables depending on Level 3 or complex dependencies
                
                // Level 5: Tables depending on Level 4 or complex dependencies
                echo "<h3>Creating Level 5 tables...</h3>";
                //include('insert_table2.php'); // Add this line
                //include('insert_table1.php'); // Add this line
                

                // Move total_local_crop_production to the very end
                // after all its dependencies are created
                echo "<h3>Creating Final Level tables...</h3>";
                //include('insert_total_local_crop_production.php');
                //include('insert_total_local_food_production.php');
                // include('insert_distribution.php'); // Add this line
            } else {
                throw new Exception("Database connection is closed.");
            }
        } catch (Exception $e) {
            echo "<br><strong>Error: " . $e->getMessage() . "</strong><br>";
            // Add detailed error logging
            if ($conn->error) {
                echo "<br>Database Error: " . $conn->error . "<br>";
            }
        }

        // Ensure the connection is not closed before all operations are completed
        if (isset($conn) && $conn instanceof mysqli && $conn->ping()) {
            $conn->close();
            echo "<br>Database connection closed successfully.<br>";
        }
        ?>
    </div>

    <!-- Bootstrap and jQuery scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>