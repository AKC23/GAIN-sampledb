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
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="center-title">Database on Edible Oil and Wheat Flour Supply for Human Consumption in Bangladesh</h1>

        <div class="card">
            <!-- Dropdown or other content on the left side -->
            <div>
                <form method="post" action="">
                    <select name="tableName" class="form-control">
                        <option value="">Select a table</option>
                        <?php
                        require_once('db_connect.php');  // Changed to require_once
                        $result = $conn->query("SHOW TABLES");
                        $validTables = [
                            'foodvehicle',
                            'foodtype',
                            'country',
                            'measure_unit',
                            'measure_period',
                            'measure_currency',
                            'geography',
                            'raw_crops',
                            'producer_name',
                            'producers_brand_name',
                            'producer_skus',
                            'local_production_amount_oilseed',
                            'importer_name',
                            'importers_brand_name',
                            'import_edible_oil',
                            'total_local_production_amount_edible_oil',
                            //'distribution_channels',
                            'total_local_crop_production',
                            'total_local_food_production',
                            'total_food_import',
                            'total_crop_import',
                            'crude_oil',
                            'entities',
                            'producer_processor',
                            'packaging_type',
                            'repacker_name',
                            'distributer_list',
                            'distributer_brand',
                            'distributer_name',
                            'distribution'  // Add this line
                        ];
                        while ($row = $result->fetch_array()) {
                            $table = $row[0];
                            if (in_array($table, $validTables)) {
                                echo "<option value='$table'>" . htmlspecialchars($table) . "</option>";
                            }
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn btn-primary mt-2">Show Table</button>
                </form>
            </div>

            <!-- Last Updated Date and Time on the right side of the card -->
            <div class="current-time">
                <?php
                echo "Last Updated: October 25, 2024, 3:30 pm";
                ?>
            </div>
        </div>

        <?php

        // Remove this include since we already have the connection
        // include('db_connect.php');

        try {
            // Disable foreign key checks for dropping tables
            $conn->query('SET FOREIGN_KEY_CHECKS = 0');

            // Update drop tables order to ensure proper dependency handling
            $dropTables = [
                'total_local_crop_production',  // Should be created last
                'total_local_food_production',  // Should be created last
                'total_food_import',
                'total_crop_import',
                'crude_oil',
                'entities',
                'producer_processor',
                'producers_brand_name',
                'importers_brand_name',
                'importer_name',
                'import_edible_oil',
                'distribution',
                'measure_unit',
                'measure_period',
                'measure_currency',
                'foodtype',
                'raw_crops',
                'producer_name',
                'country',
                'foodvehicle',
                'packaging_type',
                'repacker_name',
                'distributer_list', // Level 5 tables
                'distributer_brand', // Level 5 tables
                'distributer_name' // Level 5 tables
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
            

            // Level 1: Tables that depend on base tables
            echo "<h3>Creating Level 1 tables...</h3>";
            include('insert_foodtype.php');      // Depends on: FoodVehicle
            include('insert_producer_name.php'); // Depends on: Country, FoodVehicle
            include('insert_raw_crops.php');     // Depends on: FoodVehicle
            include('insert_geography.php');     // Depends on: country



            // Level 2: Tables depending on Level 1
            echo "<h3>Creating Level 2 tables...</h3>";
            include('insert_crude_oil.php');        // Depends on: raw_crops, FoodType
            include('insert_entities.php');             
            include('insert_importer_name.php');    // Depends on: Country, producer_name
            include('insert_repacker_name.php');    // Depends on: FoodVehicle, FoodType
            include('insert_producer_processor.php'); // Depends on: Country, FoodVehicle

            // Level 3: Tables depending on Level 2
            echo "<h3>Creating Level 3 tables...</h3>";
            include('insert_producers_brand_name.php'); // Depends on: producer_name, FoodType
            include('insert_importers_brand_name.php'); // Depends on: importer_name, FoodType
            //include('insert_distribution_channels.php'); // Depends on: FoodType

            // Level 4: Tables depending on Level 3 or complex dependencies
            echo "<h3>Creating Level 4 tables...</h3>";
            include('insert_import_edible_oil.php');
            include('insert_total_food_import.php');
            include('insert_total_crop_import.php');
            include('insert_packaging_type.php');

            // Level 5: Tables depending on Level 4 or complex dependencies
            echo "<h3>Creating Level 5 tables...</h3>";
            include('insert_distributer_name.php');
            include('insert_distributer_brand.php');
            include('insert_distributer_list.php');

            // Move total_local_crop_production to the very end
            // after all its dependencies are created
            echo "<h3>Creating Final Level tables...</h3>";
            include('insert_total_local_crop_production.php');
            include('insert_total_local_food_production.php');
            include('insert_distribution.php'); // Add this line
        } catch (Exception $e) {
            echo "<br><strong>Error: " . $e->getMessage() . "</strong><br>";
            // Add detailed error logging
            if ($conn->error) {
                echo "<br>Database Error: " . $conn->error . "<br>";
            }
        }

        // Display requested table (using the same connection)
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
            $tableName = $_POST['tableName'];
            echo "<h2 class='text-center card-title'>Data Table: " . htmlspecialchars($tableName) . "</h2>";

            echo '<div class="table-responsive">';
            try {
                include('display_table.php');
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error displaying table: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            echo '</div>';
        } else {
            echo "<p class='text-center text-muted'>Select a table to display data.</p>";
        }

        // Modified connection closing
        if (isset($conn) && $conn instanceof mysqli) {
            if ($conn->ping()) {
                $conn->close();
                echo "<br>Database connection closed successfully.<br>";
            }
        }
        ?>
    </div>

    <!-- Bootstrap and jQuery scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>