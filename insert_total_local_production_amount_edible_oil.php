<?php
// insert total_local_production_amount_edible_oil.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'total_local_production_amount_edible_oil' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS total_local_production_amount_edible_oil";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'total_local_production_amount_edible_oil' dropped successfully.<br>";
} else {
    echo "Error dropping table 'total_local_production_amount_edible_oil': " . $conn->error . "<br>";
}

// SQL query to create the 'total_local_production_amount_edible_oil' table with foreign keys
$createTableSQL = "
    CREATE TABLE total_local_production_amount_edible_oil (
        DataID INT PRIMARY KEY AUTO_INCREMENT,
        FoodTypeID INT,
        
        VehicleID INT,
        ConvertedUnit VARCHAR(50),
        CValue DECIMAL(10, 3),
        StartYear VARCHAR(50),
        EndYear VARCHAR(50),
        AccessedDate VARCHAR(50),
        Source VARCHAR(100),
        Link VARCHAR(255),
        DataType VARCHAR(50),
        ProcessToObtainData VARCHAR(80),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'total_local_production_amount_edible_oil' created successfully with foreign keys.<br>";
} else {
    echo "Error creating table 'total_local_production_amount_edible_oil': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file
$csvFile = 'total_local_production_amount_edible_oil.csv';  // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("INSERT INTO total_local_production_amount_edible_oil (FoodTypeID, VehicleID, ConvertedUnit, CValue, StartYear, EndYear, AccessedDate, Source, Link, DataType, ProcessToObtainData) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Check if there are enough columns in the CSV row
            if (count($data) < 15) { // Assuming there are at least 15 columns
                echo "Error: Not enough data in row.<br>";
                continue; // Skip to the next row
            }

            // Extract relevant columns from the CSV data
            $foodTypeID = mysqli_real_escape_string($conn, trim($data[1])); // FoodTypeID
            // $rawCrops = mysqli_real_escape_string($conn, trim($data[2])); // Raw Crops
            $vehicleID = mysqli_real_escape_string($conn, trim($data[4])); // VehicleID
            $convertedUnit = mysqli_real_escape_string($conn, trim($data[6])); // Converted Unit
            $CValue = mysqli_real_escape_string($conn, trim($data[8])); // CValue
            $startYear = mysqli_real_escape_string($conn, trim($data[9])); // Start Year
            $endYear = mysqli_real_escape_string($conn, trim($data[10])); // End Year
            $accessedDate = mysqli_real_escape_string($conn, trim($data[11])); // Accessed Date
            $source = mysqli_real_escape_string($conn, trim($data[12])); // Source
            $link = mysqli_real_escape_string($conn, trim($data[13])); // Link
            $dataType = mysqli_real_escape_string($conn, trim($data[14])); // DataType
            $ProcessToObtainData = mysqli_real_escape_string($conn, trim($data[15])); // ProcessToObtainData

            // Bind parameters
            $stmt->bind_param("issssssssss", $foodTypeID, $vehicleID, $convertedUnit, $CValue, $startYear, $endYear, $accessedDate, $source, $link, $dataType, $ProcessToObtainData);

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
