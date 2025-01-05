<?php
// total_local_crop_production.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'total_local_crop_production' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS total_local_crop_production";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'total_local_crop_production' dropped successfully.<br>";
} else {
    echo "Error dropping table 'total_local_crop_production': " . $conn->error . "<br>";
}

// First verify required tables exist
$requiredTables = ['FoodType', 'FoodVehicle', 'raw_crops', 'Country'];
foreach ($requiredTables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        die("Error: Required table '$table' does not exist.<br>");
    }
}

// SQL query to create the 'total_local_crop_production' table
$createTableSQL = "
    CREATE TABLE total_local_crop_production (
        DataID INT PRIMARY KEY AUTO_INCREMENT,
        VehicleID INT,
        FoodTypeID INT,
        RawCropsID INT,
        CountryID INT,
        SourceVolumeUnit VARCHAR(50),
        SourceVolume DECIMAL(30, 2),
        ConvertedValue DECIMAL(30, 2),
        ConvertedUnit VARCHAR(50),
        PeriodicalUnit VARCHAR(50),
        CropToFoodConvertedValue DECIMAL(30, 2),
        StartYear VARCHAR(50),
        EndYear VARCHAR(50),
        AccessedDate VARCHAR(50),
        Source VARCHAR(255),
        Link VARCHAR(255),
        Process VARCHAR(255),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (RawCropsID) REFERENCES raw_crops(RawCropsID),
        FOREIGN KEY (CountryID) REFERENCES Country(Country_ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'total_local_crop_production' created successfully.<br>";
} else {
    echo "Error creating table 'total_local_crop_production': " . $conn->error . "<br>";
}

// Path to your updated CSV file
$csvFile = 'data/total_local_crop_production.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders for the required columns
    $stmt = $conn->prepare("INSERT INTO total_local_crop_production (
                                VehicleID, FoodTypeID, RawCropsID, CountryID, 
                                SourceVolumeUnit, SourceVolume, ConvertedValue, 
                                ConvertedUnit, PeriodicalUnit, CropToFoodConvertedValue, 
                                StartYear, EndYear, AccessedDate, Source, Link, Process
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract only the required columns
            $vehicleID = (int) trim($data[1]);
            $foodTypeID = (int) trim($data[3]);
            $rawCropsID = (int) trim($data[5]);
            $countryID = (int) trim($data[7]);
            $sourceVolumeUnit = trim($data[8]);
            $sourceVolume = str_replace(',', '', trim($data[9])); // Remove any commas and preserve exact decimal
            $convertedValue = str_replace(',', '', trim($data[10])); // Remove any commas and preserve exact decimal
            $convertedUnit = trim($data[11]);
            $periodicalUnit = trim($data[12]);
            $cropToFoodConvertedValue = str_replace(',', '', trim($data[13])); // Remove any commas and preserve exact decimal
            $startYear = trim($data[14]);
            $endYear = trim($data[15]);
            $accessedDate = trim($data[16]);
            $source = trim($data[17]);
            $link = trim($data[18]);
            $process = trim($data[19]);

            // Bind the parameters
            $stmt->bind_param("iiiissdssdssssss", $vehicleID, $foodTypeID, $rawCropsID, $countryID,
                $sourceVolumeUnit, $sourceVolume, $convertedValue, $convertedUnit, 
                $periodicalUnit, $cropToFoodConvertedValue, $startYear, $endYear, 
                $accessedDate, $source, $link, $process);

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

// Connection will be closed by index.php
?>
