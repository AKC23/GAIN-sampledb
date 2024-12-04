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
        FOREIGN KEY (ProducerID) REFERENCES producer_name(ProducersID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'distribution_channels' created successfully.<br>";
} else {
    echo "Error creating table 'distribution_channels': " . $conn->error . "<br>";
}

// Get valid FoodTypeIDs
$validFoodTypeIDs = array();
$result = $conn->query("SELECT * FROM FoodType");
if ($result) {
    echo "<br>Valid FoodTypeIDs in database:<br>";
    while ($row = $result->fetch_assoc()) {
        $validFoodTypeIDs[] = $row['FoodTypeID'];
        echo "FoodTypeID: {$row['FoodTypeID']}, Name: {$row['FoodTypeName']}<br>";
    }
} else {
    echo "Error getting valid FoodTypeIDs: " . $conn->error . "<br>";
    die("Cannot proceed without valid FoodTypeIDs");
}

// Path to your CSV file
$csvFilePath = 'distribution_channels.csv'; // Update with the exact path of your CSV file

if (!file_exists($csvFilePath)) {
    die("Error: CSV file '$csvFilePath' not found.<br>");
}

echo "<br>Opening CSV file: $csvFilePath<br>";

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row (if there is one)
    $header = fgetcsv($handle);
    echo "Header row: " . implode(", ", $header) . "<br>";

    $rowNumber = 2; // Start from 2 since row 1 is header
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
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $foodTypeID = (int)trim($data[1]);
            $vehicleID = (int)trim($data[3]);
            $producerID = (int)trim($data[4]);
            $channelType = mysqli_real_escape_string($conn, trim($data[5]));
            $origin = mysqli_real_escape_string($conn, trim($data[6]));
            $unit = mysqli_real_escape_string($conn, trim($data[7]));
            $supplyQuantity = (float)trim($data[8]);

            echo "<br>Processing Row $rowNumber:<br>";
            echo "FoodTypeID: $foodTypeID (Valid IDs: " . implode(", ", $validFoodTypeIDs) . ")<br>";
            echo "VehicleID: $vehicleID<br>";
            echo "ProducerID: $producerID<br>";
            echo "ChannelType: $channelType<br>";
            echo "Origin: $origin<br>";
            echo "Unit: $unit<br>";
            echo "SupplyQuantity: $supplyQuantity<br>";

            // Validate FoodTypeID
            if (!in_array($foodTypeID, $validFoodTypeIDs)) {
                echo "Row $rowNumber: Invalid FoodTypeID: $foodTypeID - Skipping this row<br>";
                $rowNumber++;
                continue;
            }

            // Bind parameters with each column of the CSV data
            $stmt->bind_param(
                "iiisssd",
                $foodTypeID,  // FoodTypeID
                $vehicleID,  // VehicleID
                $producerID,  // ProducerID
                $channelType,  // ChannelType
                $origin,  // Origin
                $unit,  // Unit
                $supplyQuantity   // SupplyQuantity
            );

            // Execute the query and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Row $rowNumber: Data inserted successfully<br>";
            } else {
                echo "Row $rowNumber: Error inserting data: " . $stmt->error . "<br>";
            }

            $rowNumber++;
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
