<?php
// insert_raw_crops.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'raw_crops' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS raw_crops";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'raw_crops' dropped successfully.<br>";
} else {
    echo "Error dropping table 'raw_crops': " . $conn->error . "<br>";
}

// SQL query to create the 'raw_crops' table
$createTableSQL = "
    CREATE TABLE raw_crops (
        RawCropsID INT PRIMARY KEY AUTO_INCREMENT,
        RawCrops VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'raw_crops' created successfully.<br>";
} else {
    echo "Error creating table 'raw_crops': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file
$csvFile = 'raw_crops.csv';  // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("INSERT INTO raw_crops (RawCrops) VALUES (?)");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract relevant column from the CSV data
            $rawCrops = mysqli_real_escape_string($conn, trim($data[0])); // Raw Crops

            // Bind parameters
            $stmt->bind_param("s", $rawCrops);

            // Execute the query
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for RawCrops: $rawCrops<br>";
            } else {
                echo "Error inserting data for RawCrops: $rawCrops - " . $stmt->error . "<br>";
            }
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the file after reading
    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
