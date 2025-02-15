<?php
include('db_connect.php');

try {
    // Ensure the connection is open before executing queries
    if ($conn->ping()) {
        // Disable foreign key checks for dropping tables
        $conn->query('SET FOREIGN_KEY_CHECKS = 0');
        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<h2 class='center-title'>Drop & Create Database Tables</h2>";

        // Drop tables
        foreach ($validTables as $table) {
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
        include('insert_company.php');
        include('insert_measure_unit2.php');
        include('insert_measure_unit1.php');
        include('insert_measure_period.php');
        include('insert_measure_currency.php');
        include('insert_reference.php');  
        include('insert_gender.php'); 
        include('insert_age.php'); 
        include('insert_year_type.php');

        // Level 1: Tables that depend on base tables
        echo "<h3>Creating Level 1 tables...</h3>";
        include('insert_foodtype.php');      // Depends on: FoodVehicle
        include('insert_brand.php'); // <-- Moved here, after insert_company
        include('insert_Geography_Level1.php');
        include('insert_Geography_Level2.php');
        include('insert_Geography_Level3.php');

        // Level 2: Tables depending on Level 1
        echo "<h3>Creating Level 2 tables...</h3>";
        include('insert_processing_stage.php');     // Depends on: FoodVehicle
        include('insert_geography.php');     // Depends on: country
        include('insert_entities.php'); 
        include('insert_producer_processor.php'); // Depends on: Country, FoodVehicle
        include('insert_extraction_conversion.php'); // Depends on: FoodVehicle, FoodType

        // Level 3: Tables depending on Level 2
        echo "<h3>Creating Level 3 tables...</h3>";
        include('insert_packaging_type.php');

        // Level 4: Tables depending on Level 3 or complex dependencies
        include('insert_consumption.php');

        // Level 5: Tables depending on Level 4 or complex dependencies
        echo "<h3>Creating Level 5 tables...</h3>";
        include('insert_distribution_channel.php');  // Added distribution_channel table
        include('insert_sub_distribution_channel.php');  // Added sub_distribution_channel table
        include('insert_distribution.php');  // Added distribution table
        include('insert_population.php');  // Added population table
        include('insert_producer_sku.php');  // Added producer_sku table
        include('insert_supply.php');
        include('insert_supply_in_chain_final.php');

        echo "<h3>Creating Final Level tables...</h3>";
        
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

$errors = [];

$validTables = [
    'foodvehicle',
    'foodtype',
    'processing_stage',
    'country',
    'gender',
    'age',
    'year_type',
    'Geography_Level1',
    'Geography_Level2',
    'Geography_Level3',
    'reference',
    'measure_unit1',
    'measure_unit2',
    'measure_period',
    'measure_currency',
    'geography',
    'entities',
    'producer_sku',
    'consumption',
    'extraction_conversion',
    'producer_processor',
    'packaging_type',
    'distribution',
    'company',
    'distribution_channel',
    'sub_distribution_channel',
    'population',
    'brand',
    'supply',
    'supply_in_chain_final'
];

foreach ($validTables as $table) {
    // Check if table exists
    $checkSql = "SHOW TABLES LIKE '$table'";
    $result = $conn->query($checkSql);
    if ($result->num_rows == 0) {
        $errors[] = "Table $table does not exist.";
    }
}

if (!empty($errors)) {
    echo "<div class='alert alert-danger'><strong>Errors found:</strong><ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul></div>";
} else {
    echo "<div class='alert alert-success'>All tables are properly inserted into the database.</div>";
}
?>
