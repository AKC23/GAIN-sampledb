<?php
// insert_foodtype.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'FoodType' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS FoodType";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'FoodType' dropped successfully.<br>";
} else {
    echo "Error dropping table 'FoodType': " . $conn->error . "<br>";
}

// SQL query to create the 'FoodType' table with a foreign key to 'FoodVehicle'
$createTableSQL = "
    CREATE TABLE FoodType (
        FoodTypeID INT(11) AUTO_INCREMENT PRIMARY KEY,
        FoodTypeName VARCHAR(50) NULL,
        VehicleID INT(11) NULL,
        INDEX (VehicleID),
        
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID) 
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'FoodType' created successfully with foreign key constraint.<br>";
} else {
    echo "Error creating table 'FoodType': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file for FoodType data
$csvFile = 'foodtype.csv';  // Update with the correct path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Read through each line of the CSV file
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        // CSV data: FoodTypeName is in the first column (index 0) and VehicleID is in the second column (index 1)
        $foodTypeName = mysqli_real_escape_string($conn, trim($data[0]));
        $vehicleID = mysqli_real_escape_string($conn, trim($data[1]));

        // Ensure the FoodTypeName and VehicleID are not empty
        if (!empty($foodTypeName) && !empty($vehicleID)) {
            // Prepare SQL query to insert the data into the 'FoodType' table
            $sql = "INSERT INTO FoodType (FoodTypeName, VehicleID) VALUES ('$foodTypeName', '$vehicleID')";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                echo "Food Type '$foodTypeName' with VehicleID '$vehicleID' inserted successfully.<br>";
            } else {
                echo "Error inserting '$foodTypeName': " . $conn->error . "<br>";
            }
        } else {
            echo "Skipping empty row or missing data.<br>";
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
