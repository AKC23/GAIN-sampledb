<?php
// insert_import_edible_oil.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'import_edible_oil' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS import_edible_oil";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'import_edible_oil' dropped successfully.<br>";
} else {
    echo "Error dropping table 'import_edible_oil': " . $conn->error . "<br>";
}

// SQL query to create the 'import_edible_oil' table with foreign keys
$createTableSQL = "
    CREATE TABLE import_edible_oil (
        DataID INT PRIMARY KEY AUTO_INCREMENT,
        FoodTypeID INT NOT NULL,
        Origin VARCHAR(50) NOT NULL,
        VehicleID INT,
        SourceValue DECIMAL(10, 2),
        Value DECIMAL(15, 2),
        StartYear VARCHAR(50),
        EndYear VARCHAR(50),
        AccessedDate VARCHAR(20),
        Source VARCHAR(255),
        Link VARCHAR(255),
        DataType VARCHAR(50),
        Process VARCHAR(255),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'import_edible_oil' created successfully with foreign keys.<br>";
} else {
    echo "Error creating table 'import_edible_oil': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file
$csvFile = 'import_edible_oil.csv';  // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("INSERT INTO import_edible_oil (FoodTypeID, Origin, VehicleID, SourceValue, Value, StartYear, EndYear, AccessedDate, Source, Link, DataType, Process) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Check if there are enough columns in the CSV row
            if (count($data) < 12) { // Adjusting for 12 columns as FoodType is removed
                echo "Error: Not enough data in row.<br>";
                continue; // Skip to the next row
            }

            // Extract relevant columns from the CSV data
            $foodTypeID = (int) trim($data[1]); // FoodTypeID
            $origin = mysqli_real_escape_string($conn, trim($data[2])); // Origin
            $vehicleID = (int) trim($data[4]); // VehicleID
            $sourceValue = (float) trim($data[5]); // Source Value (Million MT)
            $value = (float) trim($data[6]); // Value (Metric Ton)
            $startYear = mysqli_real_escape_string($conn, trim($data[7])); // Start Year
            $endYear = mysqli_real_escape_string($conn, trim($data[8])); // End Year
            $accessedDate = mysqli_real_escape_string($conn, trim($data[9])); // Accessed Date
            $source = mysqli_real_escape_string($conn, trim($data[10])); // Source
            $link = mysqli_real_escape_string($conn, trim($data[11])); // Link
            $dataType = mysqli_real_escape_string($conn, trim($data[12])); // DataType
            $process = mysqli_real_escape_string($conn, trim($data[13])); // Process to Obtain Data

            // Bind parameters
            $stmt->bind_param("isiissssssss", $foodTypeID, $origin, $vehicleID, $sourceValue, $value, $startYear, $endYear, $accessedDate, $source, $link, $dataType, $process);

            // Execute the query
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for FoodTypeID: $foodTypeID<br>";
            } else {
                echo "Error inserting data for FoodTypeID: $foodTypeID - " . $stmt->error . "<br>";
            }
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the file after reading
    fclose($handle);
} else {
    echo "Error: Could not open CSV file.";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
// $conn->close(); // REMOVED: This line should NOT be uncommented
?>
