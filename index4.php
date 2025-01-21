<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oil & Wheat Database - BD</title>
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
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
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #c8e5bf;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-selection {
            margin-bottom: 20px;
        }

        .card-title {
            color: #17a2b8;
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
            align-items: center;
        }

        .form-group .btn {
            margin-top: 20px;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
    <script src="js/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('form').on('submit', function(event) {
                event.preventDefault();
                var tableName = $('select[name="tableName"]').val();
                if (tableName) {
                    $.post('display_table2.php', { tableName: tableName }, function(data) {
                        $('#table-view').html(data);
                    });
                } else {
                    $('#table-view').html('<p class="text-center text-muted">Select a table to display data.</p>');
                }
            });

            $('input[name="vehicleName"]').on('change', function() {
                var tableName = $('select[name="tableName"]').val();
                var vehicleName = $('input[name="vehicleName"]:checked').val();
                if (tableName) {
                    $.post('display_table2.php', { tableName: tableName, vehicleName: vehicleName }, function(data) {
                        $('#table-view').html(data);
                    });
                }
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h1 class="center-title">Database on Edible Oil and Wheat Flour Supply for Human Consumption in Bangladesh</h1>

        <div class="card">
            <!-- Dropdown or other content on the left side -->
            <div style="display: flex; align-items: center;">
                <form method="post" style="display: flex; flex-direction: column;">
                    <div class="form-group">
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
                            
                            'producer_skus',
                            'extraction_conversion',
                            'total_local_crop_production',
                            'total_local_food_production',
                            'total_food_import',
                            'total_crop_import',
                            'crude_oil',
                            'entities',
                            'producer_processor',
                            'packaging_type',
                            'distribution',  // Add this line
                            'table1',
                            'table2'  // Ensure table2 is included
                        ];
                        while ($row = $result->fetch_array()) {
                            $table = $row[0];
                            if (in_array($table, $validTables)) {
                                echo "<option value='$table'>" . htmlspecialchars($table) . "</option>";
                            }
                        }
                        ?>
                        </select>
                        <div class="vehicle-selection mt-3">
                            <div class="vehicle-selection-title">Vehicle Name</div>
                            <div class="vehicle-options form-check">
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="vehicleName" value="Edible Oil"> Edible Oil
                                </label>
                                <label class="form-check-label">
                                    <input class="form-check-input" type="radio" name="vehicleName" value="Wheat"> Wheat
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Show Table</button>
                </form>
            </div>

            <!-- Last Updated Date and Time on the right side of the card -->
            <div class="current-time">
                <?php
                echo "Last Updated: January 15, 2025, 3:30 am";
                ?>
            </div>
        </div>

        <div id="table-view">
            <p class="text-center text-muted">Select a table to display data.</p>
        </div>

        <?php
        // Display requested table (using the same connection)
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
            $tableName = $_POST['tableName'];
            $vehicleName = $_POST['vehicleName'] ?? '';
            echo "<h2 class='text-center card-title'>Data Table: " . htmlspecialchars($tableName) . "</h2>";

            echo '<div class="table-responsive">';
            try {
                include('display_table2.php');
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error displaying table: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            echo '</div>';
        } else {
            echo "<p class='text-center text-muted'>Select a table to display data.</p>";
        }

        try {
            // Ensure the connection is open before executing queries
            if ($conn->ping()) {
                // Disable foreign key checks for dropping tables
                $conn->query('SET FOREIGN_KEY_CHECKS = 0');

                echo "<h2 class='center-title'>Drop & Create Database Tables</h2>";

                // Update drop tables order to ensure proper dependency handling
                $dropTables = [
                    'total_local_crop_production',  // Should be created last
                    'total_local_food_production',  // Should be created last
                    'total_food_import',            // Should be created last
                    'total_crop_import',            // Should be created last
                    'extraction_conversion', 
                    'crude_oil',
                    'entities',
                    'producer_processor',
                    'distribution',
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
                


                // Level 2: Tables depending on Level 1
                echo "<h3>Creating Level 2 tables...</h3>";
                
                include('insert_entities.php');             
                
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
                include('insert_total_local_crop_production.php');
                include('insert_total_local_food_production.php');
                include('insert_distribution.php'); // Add this line
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
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>