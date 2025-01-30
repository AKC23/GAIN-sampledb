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
            'producer_sku',  // Added producer_sku table
            'packaging_type',
            'foodvehicle',
            'foodtype',
            'country',
            'distribution',  // Added distribution table
            'company',
            'distribution_channel',  // Added distribution_channel table
            'sub_distribution_channel',  // Added sub_distribution_channel table
            'year_type',  // Added year_type table
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
        include('insert_company.php');

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
        include('insert_packaging_type.php');
        include('insert_producer_sku.php');  // Added producer_sku table
        

        // Level 4: Tables depending on Level 3 or complex dependencies
        
        // Level 5: Tables depending on Level 4 or complex dependencies
        echo "<h3>Creating Level 5 tables...</h3>";
        include('insert_distribution_channel.php');  // Added distribution_channel table
        include('insert_sub_distribution_channel.php');  // Added sub_distribution_channel table
        include('insert_year_type.php');  // Added year_type table
        include('insert_distribution.php');  // Added distribution table
        

        // Move total_local_crop_production to the very end
        // after all its dependencies are created
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

?>
