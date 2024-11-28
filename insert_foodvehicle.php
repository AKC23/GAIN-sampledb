<?php
// insert_foodvehicle.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'FoodVehicle' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS FoodVehicle";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'FoodVehicle' dropped successfully.<br>";
} else {
    echo "Error dropping table 'FoodVehicle': " . $conn->error . "<br>";
}

// SQL query to create the 'FoodVehicle' table
$createTableSQL = "
    CREATE TABLE FoodVehicle (
        VehicleID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleName VARCHAR(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'FoodVehicle' created successfully.<br>";
} else {
    echo "Error creating table 'FoodVehicle': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file
$csvFile = 'foodvehicle.csv';  // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Read through each line of the CSV file
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        // CSV data: VehicleName is in the first column (index 0)
        $vehicleName = mysqli_real_escape_string($conn, trim($data[0]));

        // Ensure the VehicleName is not empty
        if (!empty($vehicleName)) {
            // Prepare SQL query to insert the data into the 'FoodVehicle' table
            $sql = "INSERT INTO FoodVehicle (VehicleName) VALUES ('$vehicleName')";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                echo "Vehicle '$vehicleName' inserted successfully.<br>";
            } else {
                echo "Error inserting '$vehicleName': " . $conn->error . "<br>";
            }
        } else {
            echo "Skipping empty row.<br>";
        }
    }

    // Close the file after reading
    fclose($handle);
} else {
    echo "Error: Could not open CSV file.";
}

// Close the database connection
$conn->close();
?>
