<?php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'distribution_channels' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS distribution_channels";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'distribution_channels' dropped successfully.<br>";
} else {
    echo "Error dropping table 'distribution_channels': " . $conn->error . "<br>";
}

// SQL query to create the 'distribution_channels' table with foreign keys
$createTableSQL = "
    CREATE TABLE distribution_channels (
        DistributionID INT AUTO_INCREMENT PRIMARY KEY,
        FoodTypeID INT,
        VehicleID INT,
        ProducerID INT,
        ChannelType VARCHAR(40),
        Origin VARCHAR(40),
        Unit VARCHAR(40),
        SupplyQuantity DECIMAL(10, 2),

        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (ProducerID) REFERENCES producers_supply(ProducerID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'distribution_channels' created successfully.<br>";
} else {
    echo "Error creating table 'distribution_channels': " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFilePath = 'distribution_channels.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO distribution_channels (
            FoodTypeID, VehicleID, ProducerID, ChannelType, Origin, Unit, SupplyQuantity
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Bind parameters with each column of the CSV data
            $stmt->bind_param(
                "iiisssd",
                $row[1],  // FoodTypeID
                $row[3],  // VehicleID
                $row[4],  // ProducerID
                $row[5],  // ChannelType
                $row[6],  // Origin
                $row[7],  // Unit
                $row[8]   // SupplyQuantity
            );

            // Execute the query and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for ChannelType: " . $row[3] . "<br>";
            } else {
                echo "Error inserting data for ChannelType: " . $row[3] . " - " . $stmt->error . "<br>";
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
