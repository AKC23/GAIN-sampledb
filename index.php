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
                        while ($row = $result->fetch_array()) {
                            $table = $row[0];
                            echo "<option value='$table'>" . htmlspecialchars($table) . "</option>";
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
            
            // First drop all tables in reverse order of dependencies
            $dropTables = [
                'total_local_crop_production',
                'crude_oil',
                'total_food_import',        // Add this line
                'import_edible_oil',
                'import_amount_oilseeds',
                'distribution_channels',
                'importer_skus_price',
                'importers_brand_name',
                'producers_brand_name',
                'importers_supply',
                'producer_name',
                'foodtype',
                'raw_crops',
                'country',
                'foodvehicle',
                'packaging_type',           // Add this line
                'repacker_name'             // Add this line
            ];
            
            foreach ($dropTables as $table) {
                $sql = "DROP TABLE IF EXISTS " . $table;
                if ($conn->query($sql) === TRUE) {
                    echo "Table '$table' dropped successfully.<br>";
                } else {
                    echo "Error dropping table '$table': " . $conn->error . "<br>";
                }
            }
            
            // Re-enable foreign key checks
            $conn->query('SET FOREIGN_KEY_CHECKS = 1');
            
            // Level 0: Create and populate base tables (no dependencies)
            echo "<h3>Creating base tables (Level 0)...</h3>";
            include('insert_foodvehicle.php');  // Must be first as others depend on it
            
            // Verify FoodVehicle data
            $result = $conn->query("SELECT * FROM FoodVehicle");
            if ($result && $result->num_rows > 0) {
                echo "<br>✓ FoodVehicle table populated successfully<br>";
            } else {
                throw new Exception("FoodVehicle table is empty - cannot proceed");
            }
            
            include('insert_country.php');  // Must be second as others depend on it
            
            // Verify Country data
            $result = $conn->query("SELECT * FROM Country");
            if ($result && $result->num_rows > 0) {
                echo "<br>✓ Country table populated successfully<br>";
            } else {
                throw new Exception("Country table is empty - cannot proceed");
            }
            
            include('insert_rawcrops.php');

            // Level 1: Tables that depend on base tables
            echo "<h3>Creating tables with Level 1 dependencies...</h3>";
            include('insert_foodtype.php');  // Depends on FoodVehicle
            
            // Verify FoodType data
            $result = $conn->query("SELECT * FROM FoodType");
            if ($result && $result->num_rows > 0) {
                echo "<br>✓ FoodType table populated successfully<br>";
            } else {
                throw new Exception("FoodType table is empty - cannot proceed");
            }

            
            include('insert_importers_supply.php');  // Depends on Country
            include('insert_repacker_name.php');  // Depends on Country
            include('insert_producer_name.php');  // Depends on Country
            
            // Level 2: Tables that depend on Level 1 tables
            echo "<h3>Creating tables with Level 2 dependencies...</h3>";
            include('insert_producers_brand_name.php');  // Depends on producer_name
            include('insert_importers_brand_name.php');  // Depends on importers_supply
            include('insert_crude_oil.php');  // Depends on FoodType, Country, RawCrops

            // Level 3: Tables that depend on Level 2 tables
            echo "<h3>Creating tables with Level 3 dependencies...</h3>";
            
            // Verify required tables exist before creating importer_skus
            $requiredTables = array(
                'importers_supply' => 'ImporterID',
                'FoodVehicle' => 'VehicleID',
                'FoodType' => 'FoodTypeID'
            );
            
            foreach ($requiredTables as $table => $idColumn) {
                $result = $conn->query("SELECT $idColumn FROM $table LIMIT 1");
                if (!$result) {
                    throw new Exception("Required table '$table' does not exist. Cannot create importer_skus.");
                }
                if ($result->num_rows === 0) {
                    throw new Exception("Required table '$table' is empty. Cannot create importer_skus.");
                }
                echo "✓ Required table '$table' exists and contains data.<br>";
            }
            
            include('insert_importer_skus_price.php');  // Depends on importers_supply, FoodVehicle, FoodType
            include('insert_distribution_channels.php');  // Depends on FoodType

            // Level 4: Tables that depend on Level 3 tables
            echo "<h3>Creating tables with Level 4 dependencies...</h3>";
            include('insert_import_amount_oilseeds.php');
            include('insert_import_edible_oil.php');  
            include('insert_total_local_crop_production.php');
            include('insert_total_food_import.php');   // Add this line
            include('insert_total_crop_import.php');
            include('insert_packaging_type.php');      // Add this line
            
        } catch (Exception $e) {
            echo "<br><strong>Error: " . $e->getMessage() . "</strong><br>";
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
        if (!empty($conn) && is_object($conn)) {
            try {
                $conn->close();
                echo "<br>Database connection closed successfully.<br>";
            } catch (Exception $e) {
                // Silently handle any closing errors
            }
        }
        ?>
    </div>

    <!-- Bootstrap and jQuery scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>