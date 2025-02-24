<?php
// debug_table.php
// Include the database connection

include('db_connect.php');

echo "<br>";
echo "<br>";
echo "<br>";
echo "<h2 class='center-title'>Drop & Create Database Tables</h2>";

try {
    // Ensure the connection is open before executing queries
    if ($conn->ping()) {
        // Disable foreign key checks for dropping tables
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");

        // Update drop tables order to ensure proper dependency handling
        $dropTables = [
            'country',
            'foodvehicle',
            'foodtype',
            'company',
            'brand',
            'processingstage',
            'geographylevel1',
            'geographylevel2',
            'geographylevel3',
            'producer_reference',
            'measureunit1',
            'measurecurrency',
            'packagingtype'
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
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");

        // Level 0: Base tables with no dependencies
        echo "<h3>Creating base tables (Level 0)...</h3>";
        include('insert_country.php'); // Ensure country is created first
        include('insert_foodvehicle.php'); // Ensure foodvehicle is created first
        include('insert_company.php'); // Ensure company is created first
        include('insert_brand.php'); // Ensure brand is created first
        include('insert_measure_unit1.php'); // Ensure measureunit1 is created first
        include('insert_measure_currency.php'); // Ensure measurecurrency is created first
        include('insert_packaging_type.php'); // Ensure packagingtype is created first
        include('insert_sub_distribution_channel.php'); // Ensure packagingtype is created first
        include('insert_distribution_channel.php'); // Ensure packagingtype is created first
        include('insert_reference.php'); // Ensure reference is created first
        include('insert_age.php'); // Ensure age is created first
        include('insert_gender.php'); // Ensure year_type is created first
        include('insert_year_type.php'); // Ensure year_type is created first
        

        // Level 1: Tables that depend on base tables
        echo "<h3>Creating base tables (Level 1)...</h3>";
        include('insert_foodtype.php'); // Ensure foodtype is created after foodvehicle
        include('insert_processing_stage.php'); // Ensure processingstage is created after foodvehicle
        include('insert_geography_level1.php'); // Ensure geographylevel1 is created after country
        include('insert_geography_level2.php'); // Ensure geographylevel2 is created after geographylevel1
        include('insert_geography_level3.php'); // Ensure geographylevel3 is created after geographylevel2
        include('insert_producer_reference.php'); // Ensure producer_reference is created after company & country
        include('insert_extraction_conversion.php'); // Ensure extraction_conversion is created after foodvehicle, foodtype, reference
        include('insert_insert_adult_male_equivalent.php'); // Ensure adult_male_equivalent is created after gender & age
        include('insert_product.php');
        
        // Level 2: Tables that depend on Level 1 tables
        include('insert_entity.php');
        include('insert_producer_processor.php');
        include('insert_producer_sku.php');
        include('insert_distribution.php');
        include('insert_consumption.php');
        include('insert_individual_consumption.php');
       

        // Level 3: Tables that depend on Level 2 tables
        include('insert_supply.php');

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
