<?php
// insert_supply.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'supply' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS supply";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'supply' dropped successfully.<br>";
} else {
    echo "Error dropping table 'supply': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'supply' table
$createTableSQL = "
    CREATE TABLE supply (
        SupplyID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleID INT(11),
        CountryID INT(11),
        FoodTypeID INT(11),
        PSID INT(11),
        Origin VARCHAR(255),
        EntityID INT(11),
        ProductID INT(11),
        ProducerReferenceID INT(11),
        UCID INT(11),
        SourceVolume DECIMAL(20, 3),
        VolumeMTY DECIMAL(20, 3),
        CropToFirstProcessedFoodStageConvertedValue DECIMAL(20, 3),
        YearTypeID INT(11),
        StartYear INT(4),
        EndYear INT(4),
        ReferenceID INT(11),
        FOREIGN KEY (VehicleID) REFERENCES foodvehicle(VehicleID),
        FOREIGN KEY (CountryID) REFERENCES country(CountryID),
        FOREIGN KEY (FoodTypeID) REFERENCES foodtype(FoodTypeID),
        FOREIGN KEY (PSID) REFERENCES processingstage(PSID),
        FOREIGN KEY (EntityID) REFERENCES entity(EntityID),
        FOREIGN KEY (ProductID) REFERENCES product(ProductID),
        FOREIGN KEY (ProducerReferenceID) REFERENCES producerreference(ProducerReferenceID),
        FOREIGN KEY (UCID) REFERENCES measureunit1(UCID),
        FOREIGN KEY (YearTypeID) REFERENCES yeartype(YearTypeID),
        FOREIGN KEY (ReferenceID) REFERENCES reference(ReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'supply' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/supply.csv';  // Update with the exact path of your CSV file

if (!file_exists($csvFile)) {
    die("Error: CSV file '$csvFile' not found.<br>");
}

echo "<br>Opening CSV file: $csvFile<br>";

// Check for BOM and remove if present
$content = file_get_contents($csvFile);
if ($content === false) {
    die("Error: Could not read CSV file.<br>");
}

// Check for UTF-8 BOM and remove it
$bom = pack('H*','EFBBBF');
if (strncmp($content, $bom, 3) === 0) {
    echo "Found and removing UTF-8 BOM from CSV file.<br>";
    $content = substr($content, 3);
}

// Normalize line endings
$content = str_replace("\r\n", "\n", $content);
$content = str_replace("\r", "\n", $content);
$lines = explode("\n", $content);

// Remove any empty lines
$lines = array_filter($lines, function($line) {
    return trim($line) !== '';
});

// Save normalized content to temp file
$tempFile = $csvFile . '.tmp';
file_put_contents($tempFile, implode("\n", $lines));
$csvFile = $tempFile;

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    $header = fgetcsv($handle, 1000, ",");
    if ($header !== FALSE) {
        echo "Header row: " . implode(", ", array_map('trim', $header)) . "<br>";
    }

    echo "<br>CSV Contents:<br>";
    if ($header !== FALSE) {
        echo "Row 1 (Header): " . implode(", ", array_map('trim', $header)) . "<br>";
    }
    
    $rowNumber = 2;
    rewind($handle);
    fgetcsv($handle); // Skip header again
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Show raw data for debugging
        echo "<br>Row $rowNumber raw data:<br>";
        foreach ($data as $index => $value) {
            echo "Column $index: '" . bin2hex($value) . "' (hex), '" . $value . "' (raw)<br>";
        }
        
        // Clean and validate data
        if (count($data) < 28) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $vehicleID = trim($data[0]);
        $countryID = trim($data[2]);
        $foodTypeID = trim($data[4]);
        $psid = trim($data[6]);
        $origin = trim($data[8]);
        $entityID = trim($data[9]);
        $productID = trim($data[13]);
        $producerReferenceID = trim($data[15]);
        $ucid = trim($data[17]);
        $sourceVolume = str_replace(',', '', trim($data[20])); // Remove commas
        $yearTypeID = trim($data[23]);
        $startYear = trim($data[25]);
        $endYear = trim($data[26]);
        $referenceID = trim($data[27]);
        
        // Remove any extra spaces between the name and comma
        $origin = preg_replace('/\s+,/', ',', $origin);
        
        // Convert to proper types
        $vehicleID = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $countryID = filter_var($countryID, FILTER_VALIDATE_INT);
        $foodTypeID = filter_var($foodTypeID, FILTER_VALIDATE_INT);
        $psid = filter_var($psid, FILTER_VALIDATE_INT);
        $entityID = filter_var($entityID, FILTER_VALIDATE_INT);
        $productID = filter_var($productID, FILTER_VALIDATE_INT);
        $producerReferenceID = filter_var($producerReferenceID, FILTER_VALIDATE_INT);
        $ucid = filter_var($ucid, FILTER_VALIDATE_INT);
        $sourceVolume = filter_var($sourceVolume, FILTER_VALIDATE_FLOAT);
        $yearTypeID = filter_var($yearTypeID, FILTER_VALIDATE_INT);
        $startYear = filter_var($startYear, FILTER_VALIDATE_INT);
        $endYear = filter_var($endYear, FILTER_VALIDATE_INT);
        $referenceID = filter_var($referenceID, FILTER_VALIDATE_INT);

        if ($vehicleID === false || $countryID === false || $foodTypeID === false || $psid === false || $entityID === false || $productID === false || $producerReferenceID === false || $ucid === false || $sourceVolume === false || $yearTypeID === false || $startYear === false || $endYear === false || $referenceID === false) {
            echo "Error: Invalid data format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $origin = mysqli_real_escape_string($conn, $origin);

        // Calculate VolumeMTY based on Source Volume and UCID
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error: Invalid UCID $ucid in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Calculate CropToFirstProcessedFoodStageConvertedValue based on conditions
        if ($countryID == 2 && $psid == 3) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 0.175;
        } elseif ($countryID == 2 && $psid == 6) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 0.15;
        } elseif ($countryID == 2 && $psid == 8) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 1;
        } else {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 1; // Default value if no condition matches
        }

        // Debugging: Show extracted values
        echo "VehicleID: $vehicleID, CountryID: $countryID, FoodTypeID: $foodTypeID, PSID: $psid, Origin: '$origin', EntityID: $entityID, ProductID: $productID, ProducerReferenceID: $producerReferenceID, UCID: $ucid, SourceVolume: $sourceVolume, VolumeMTY: $volumeMTY, CropToFirstProcessedFoodStageConvertedValue: $cropToFirstProcessedFoodStageConvertedValue, YearTypeID: $yearTypeID, StartYear: $startYear, EndYear: $endYear, ReferenceID: $referenceID<br>";

        if (empty($origin)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO supply (VehicleID, CountryID, FoodTypeID, PSID, Origin, EntityID, ProductID, ProducerReferenceID, UCID, SourceVolume, VolumeMTY, CropToFirstProcessedFoodStageConvertedValue, YearTypeID, StartYear, EndYear, ReferenceID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiiisiiidddiii", $vehicleID, $countryID, $foodTypeID, $psid, $origin, $entityID, $productID, $producerReferenceID, $ucid, $sourceVolume, $volumeMTY, $cropToFirstProcessedFoodStageConvertedValue, $yearTypeID, $startYear, $endYear, $referenceID);

        if ($stmt->execute()) {
            $supplyID = $conn->insert_id;
            echo "✓ Inserted supply record with ID: $supplyID<br>";
        } else {
            echo "Error inserting supply record: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final supply table contents:<br>";
    $result = $conn->query("SELECT * FROM supply ORDER BY SupplyID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['SupplyID']}, VehicleID: {$row['VehicleID']}, CountryID: {$row['CountryID']}, FoodTypeID: {$row['FoodTypeID']}, PSID: {$row['PSID']}, Origin: {$row['Origin']}, EntityID: {$row['EntityID']}, ProductID: {$row['ProductID']}, ProducerReferenceID: {$row['ProducerReferenceID']}, UCID: {$row['UCID']}, SourceVolume: {$row['SourceVolume']}, VolumeMTY: {$row['VolumeMTY']}, CropToFirstProcessedFoodStageConvertedValue: {$row['CropToFirstProcessedFoodStageConvertedValue']}, YearTypeID: {$row['YearTypeID']}, StartYear: {$row['StartYear']}, EndYear: {$row['EndYear']}, ReferenceID: {$row['ReferenceID']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>