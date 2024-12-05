<?php

// Include the database connection
include('db_connect.php');

// Drop the table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS importers_brand_name";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'importers_brand_name' dropped successfully.<br>";
} else {
    echo "Error dropping table 'importers_brand_name': " . $conn->error . "<br>";
}

// Create the table with the specified columns
$createTableSQL = "
    CREATE TABLE importers_brand_name (
        BrandID INT AUTO_INCREMENT PRIMARY KEY,
        BrandName VARCHAR(20),
        ImporterID INT,
        FoodTypeID INT,
        
        FOREIGN KEY (ImporterID) REFERENCES importer_name(ImporterID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'importers_brand_name' created successfully.<br>";
} else {
    echo "Error creating table 'importers_brand_name': " . $conn->error . "<br>";
}

// Add verification after table creation
$verifyTable = $conn->query("SHOW TABLES LIKE 'importers_brand_name'");
if (!$verifyTable || $verifyTable->num_rows === 0) {
    die("Failed to create importers_brand_name table properly");
}

// Path to your CSV file
$csvFilePath = 'importers_brand_name.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO importers_brand_name (
            BrandName, ImporterID, FoodTypeID
        ) VALUES (?, ?, ?)
    ");

    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract values
            $brandName = trim($row[0]);
            $importerID = trim($row[2]);
            $foodTypeID = trim($row[4]);

            // Bind the CSV values to the prepared statement
            $stmt->bind_param(
                "sii",  // 's' for string, 'i' for integer
                $brandName,    // BrandName
                $importerID,   // ImporterID
                $foodTypeID    // FoodTypeID
            );

            // Execute the statement and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for BrandName: " . $brandName . "<br>";
            } else {
                echo "Error inserting data for BrandName: " . $brandName . " - " . $stmt->error . "<br>";
            }
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the CSV file after reading
    fclose($handle);
} else {
    echo "Error: Could not open CSV file.";
}

?>