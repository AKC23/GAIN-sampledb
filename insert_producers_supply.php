<?php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'producers_supply' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producers_supply";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producers_supply' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producers_supply': " . $conn->error . "<br>";
}

// SQL query to create the 'producers_supply' table with foreign keys
$createTableSQL = "
    CREATE TABLE producers_supply (
        ProducerID INT AUTO_INCREMENT PRIMARY KEY,
        ProducerProcessorType VARCHAR(20),
        ProducerName VARCHAR(20),
        VehicleID INT,
        Unit VARCHAR(20),
        SupplyQuantity DECIMAL(10, 2),
        
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producers_supply' created successfully.<br>";
} else {
    echo "Error creating table 'producers_supply': " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFilePath = 'producers_supply.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO producers_supply (
            ProducerProcessorType, ProducerName, VehicleID, Unit, SupplyQuantity
        ) VALUES (?, ?, ?, ?, ?)
    ");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Bind parameters with each column of the CSV data
            $stmt->bind_param(
                "ssisd",
                $row[0],  // ProducerProcessorType
                $row[1],  // ProducerName
                $row[3],  // VehicleID
                $row[4],  // Unit
                $row[5]   // SupplyQuantity
            );

            // Execute the query and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for ProducerName: " . $row[2] . "<br>";
            } else {
                echo "Error inserting data for ProducerName: " . $row[2] . " - " . $stmt->error . "<br>";
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
