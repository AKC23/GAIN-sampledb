<?php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'packaging_type' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS packaging_type";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'packaging_type' dropped successfully.<br>";
} else {
    echo "Error dropping table 'packaging_type': " . $conn->error . "<br>";
}

// SQL query to create the 'packaging_type' table
$createTableSQL = "
    CREATE TABLE packaging_type (
        Packaging_Type_ID INT AUTO_INCREMENT PRIMARY KEY,
        Packaging_Type VARCHAR(50)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'packaging_type' created successfully.<br>";
} else {
    echo "Error creating table 'packaging_type': " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFilePath = 'data/packaging_type.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip header row
    fgetcsv($handle, 1000, ",");

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO packaging_type (
            Packaging_Type
        ) VALUES (?)
    ");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Bind parameters with each column of the CSV data
            $stmt->bind_param(
                "s",
                $row[0]  // Packaging_Type
            );

            // Execute the query and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for Packaging_Type: " . $row[0] . "<br>";
            } else {
                echo "Error inserting data for Packaging_Type: " . $row[0] . " - " . $stmt->error . "<br>";
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