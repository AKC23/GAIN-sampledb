<?php
// insertlocal_production_amount_oilseed_updated.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'local_production_amount_oilseed_updated' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS local_production_amount_oilseed_updated";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'local_production_amount_oilseed_updated' dropped successfully.<br>";
} else {
    echo "Error dropping table 'local_production_amount_oilseed_updated': " . $conn->error . "<br>";
}

// SQL query to create the 'local_production_amount_oilseed_updated' table with foreign keys
$createTableSQL = "
    CREATE TABLE local_production_amount_oilseed_updated (
        DataID INT PRIMARY KEY AUTO_INCREMENT,
        FoodTypeID INT,
        VehicleID INT,
        RawCropsID INT,
        CountryID INT,
        SourceVolumeUnit VARCHAR(50),
        SourceVolume DECIMAL(15, 3),
        ConvertedValue DECIMAL(15, 3),
        ConvertedUnit VARCHAR(50),
        PeriodicalUnit VARCHAR(50),
        CropToFoodConvertedValue DECIMAL(15, 3),
        StartYear VARCHAR(50),
        EndYear VARCHAR(50),
        AccessedDate VARCHAR(50),
        Source VARCHAR(255),
        Link VARCHAR(255),
        Process VARCHAR(255),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID) ON DELETE SET NULL ON UPDATE CASCADE,
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID) ON DELETE SET NULL ON UPDATE CASCADE,
        FOREIGN KEY (CountryID) REFERENCES Country(CountryID) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'local_production_amount_oilseed_updated' created successfully.<br>";
} else {
    echo "Error creating table 'local_production_amount_oilseed_updated': " . $conn->error . "<br>";
}

// Path to your updated CSV file
$csvFile = 'local_production_amount_oilseed_updated.csv';  // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders for all columns
    $stmt = $conn->prepare("INSERT INTO local_production_amount_oilseed_updated (FoodTypeID, VehicleID, RawCropsID, CountryID, SourceVolumeUnit, SourceVolume, ConvertedValue, ConvertedUnit, PeriodicalUnit, CropToFoodConvertedValue, StartYear, EndYear, AccessedDate, Source, Link, Process) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract relevant columns from the CSV data
            $foodTypeID = (int) trim($data[3]); // FoodTypeID
            $vehicleID = (int) trim($data[1]); // VehicleID
            $rawCropsID = (int) trim($data[5]); // RawCropsID
            $countryID = (int) trim($data[7]); // CountryID
            $sourceVolumeUnit = trim($data[8]); // SourceVolumeUnit
            $sourceVolume = (float) trim($data[9]); // SourceVolume
            $convertedValue = (float) trim($data[10]); // ConvertedValue
            $convertedUnit = trim($data[11]); // ConvertedUnit
            $periodicalUnit = trim($data[12]); // PeriodicalUnit
            $cropToFoodConvertedValue = (float) trim($data[13]); // CropToFoodConvertedValue
            $startYear = trim($data[14]); // StartYear
            $endYear = trim($data[15]); // EndYear
            $accessedDate = trim($data[16]); // AccessedDate
            $source = trim($data[17]); // Source
            $link = trim($data[18]); // Link
            $process = trim($data[19]); // Process

            // Bind parameters
            $stmt->bind_param("iiiissdssdsssss", $foodTypeID, $vehicleID, $rawCropsID, $countryID, $sourceVolumeUnit, $sourceVolume, $convertedValue, $convertedUnit, $periodicalUnit, $cropToFoodConvertedValue, $startYear, $endYear, $accessedDate, $source, $link, $process);

            // Execute the query
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully.<br>";
            } else {
                echo "Error inserting data: " . $stmt->error . "<br>";
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

// Close the database connection
$conn->close();
?>
