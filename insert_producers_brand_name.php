<?php

// Include the database connection
include('db_connect.php');

// Drop the 'producers_brand_name' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producers_brand_name";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producers_brand_name' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producers_brand_name': " . $conn->error . "<br>";
}

// Create the 'producers_brand_name' table with the specified columns and foreign keys
$createTableSQL = "
    CREATE TABLE producers_brand_name (
        BrandID INT AUTO_INCREMENT PRIMARY KEY,
        BrandName VARCHAR(20),
        ProducerID INT,
        FoodTypeID INT,
        
        FOREIGN KEY (ProducerID) REFERENCES producer_name(ProducersID) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producers_brand_name' created successfully.<br>";
} else {
    echo "Error creating table 'producers_brand_name': " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFilePath = 'producers_brand_name.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO producers_brand_name (
            BrandName, ProducerID, FoodTypeID
        ) VALUES (?, ?, ?)
    ");

    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Bind the CSV values to the prepared statement
            $stmt->bind_param(
                "sii",  // 's' for string, 'i' for integer
                $row[0], // BrandName
                $row[1], // ProducerID (foreign key)
                $row[2]  // FoodTypeID (foreign key)
            );

            // Execute the statement and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for BrandName: " . $row[1] . "<br>";
            } else {
                echo "Error inserting data for BrandName: " . $row[1] . " - " . $stmt->error . "<br>";
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

// Close the database connection
$conn->close();
?>
