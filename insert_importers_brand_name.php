<?php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'importers_brand_name' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS importers_brand_name";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'importers_brand_name' dropped successfully.<br>";
} else {
    echo "Error dropping table 'importers_brand_name': " . $conn->error . "<br>";
}

// SQL query to create the 'importers_brand_name' table with foreign key
$createTableSQL = "
    CREATE TABLE importers_brand_name (
        BrandID INT AUTO_INCREMENT PRIMARY KEY,
        BrandName VARCHAR(20),
        ImporterID INT,
        VehicleID INT,
        
        FOREIGN KEY (ImporterID) REFERENCES importers_supply(ImporterID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'importers_brand_name' created successfully.<br>";
} else {
    echo "Error creating table 'importers_brand_name': " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFilePath = 'importers_brand_name.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO importers_brand_name (
            BrandName, ImporterID, VehicleID
        ) VALUES (?, ?, ?)
    ");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Bind parameters with each column of the CSV data
            $stmt->bind_param(
                "sii",
                $row[0],  // BrandName
                $row[2],  // ImporterID
                $row[4]   // VehicleID
            );

            // Execute the query and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for BrandName: " . $row[0] . "<br>";
            } else {
                echo "Error inserting data for BrandName: " . $row[0] . " - " . $stmt->error . "<br>";
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
