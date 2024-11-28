<?php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'importer_skus' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS importer_skus";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'importer_skus' dropped successfully.<br>";
} else {
    echo "Error dropping table 'importer_skus': " . $conn->error . "<br>";
}

// SQL query to create the 'importer_skus' table with foreign keys
$createTableSQL = "
    CREATE TABLE importer_skus (
        SKUSID INT AUTO_INCREMENT PRIMARY KEY,
        ImporterID INT,
        VehicleID INT,
        BrandID INT,
        SKU VARCHAR(50),
        Packaging VARCHAR(50),
        Price DECIMAL(10, 2),
        FoodTypeID INT,
        SourceURL VARCHAR(255),
        DateAccessed VARCHAR(20),
        ProcessToObtainData VARCHAR(255),

        FOREIGN KEY (ImporterID) REFERENCES importers_supply(ImporterID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'importer_skus' created successfully.<br>";
} else {
    echo "Error creating table 'importer_skus': " . $conn->error . "<br>";
}

// Get valid IDs from reference tables
$validIDs = array(
    'importers' => array(),
    'vehicle' => array(),
    'foodtype' => array()
);

// Get valid ImporterIDs
$result = $conn->query("SELECT ImporterID FROM importers_supply");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validIDs['importers'][] = $row['ImporterID'];
    }
    echo "Valid ImporterIDs: " . implode(", ", $validIDs['importers']) . "<br>";
} else {
    echo "Error getting valid ImporterIDs: " . $conn->error . "<br>";
}

// Get valid VehicleIDs
$result = $conn->query("SELECT VehicleID FROM FoodVehicle");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validIDs['vehicle'][] = $row['VehicleID'];
    }
    echo "Valid VehicleIDs: " . implode(", ", $validIDs['vehicle']) . "<br>";
} else {
    echo "Error getting valid VehicleIDs: " . $conn->error . "<br>";
}

// Get valid FoodTypeIDs
$result = $conn->query("SELECT FoodTypeID FROM FoodType");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validIDs['foodtype'][] = $row['FoodTypeID'];
    }
    echo "Valid FoodTypeIDs: " . implode(", ", $validIDs['foodtype']) . "<br>";
} else {
    echo "Error getting valid FoodTypeIDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFilePath = 'importer_skus_price.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO importer_skus (
            ImporterID, VehicleID, BrandID, SKU, Packaging, Price,
            FoodTypeID, SourceURL, DateAccessed, ProcessToObtainData
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Validate foreign keys
            $importerID = (int)trim($row[1]);
            $vehicleID = (int)trim($row[3]);
            $foodTypeID = (int)trim($row[10]);

            $isValid = true;
            if (!in_array($importerID, $validIDs['importers'])) {
                echo "Error: Invalid ImporterID $importerID. Skipping.<br>";
                $isValid = false;
            }
            if (!in_array($vehicleID, $validIDs['vehicle'])) {
                echo "Error: Invalid VehicleID $vehicleID. Skipping.<br>";
                $isValid = false;
            }
            if (!in_array($foodTypeID, $validIDs['foodtype'])) {
                echo "Error: Invalid FoodTypeID $foodTypeID. Skipping.<br>";
                $isValid = false;
            }

            if (!$isValid) {
                continue;
            }

            // Bind parameters with each column of the CSV data
            $stmt->bind_param(
                "iiissdisss",
                $row[1],  // ImporterID
                $row[3],  // VehicleID
                $row[5],  // BrandID
                $row[6],  // SKU
                $row[7],  // Packaging
                $row[8],  // Price
                $row[10],  // FoodTypeID
                $row[11],  // SourceURL
                $row[12],  // DateAccessed
                $row[13]   // ProcessToObtainData
            );

            // Execute the query and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for SKU: " . $row[3] . "<br>";
            } else {
                echo "Error inserting data for SKU: " . $row[3] . " - " . $stmt->error . "<br>";
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
