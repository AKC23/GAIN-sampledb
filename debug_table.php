<?php
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
            'company'
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

        echo "<h3>Creating base tables (Level 1)...</h3>";
        include('insert_foodtype.php'); // Ensure foodtype is created after foodvehicle
        include('insert_company.php'); // Ensure company is created after foodtype

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
