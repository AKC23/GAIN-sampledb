<?php

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
        PS_ID INT(11),
        Origin VARCHAR(255),
        PSPRID INT(11),
        BrandID INT(11),
        ProductReferenceNo INT(11),
        UC_ID INT(11),
        SourceVolume FLOAT,
        YearTypeID INT(11),
        StartYear INT(11),
        EndYear INT(11),
        ReferenceID INT(11), 
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (CountryID) REFERENCES Country(Country_ID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
        FOREIGN KEY (PS_ID) REFERENCES processing_stage(PSID),
        FOREIGN KEY (PSPRID) REFERENCES producer_processor(ProcessorID),
        FOREIGN KEY (BrandID) REFERENCES brand(BrandID),
        FOREIGN KEY (UC_ID) REFERENCES measure_unit1(UCID),
        FOREIGN KEY (YearTypeID) REFERENCES year_type(YearTypeID),  
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
        if (count($data) < 24) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $vehicleID           = trim($data[1]);
        $countryID           = trim($data[3]);
        $foodTypeID          = trim($data[5]);
        $psID                = trim($data[7]);
        $origin              = trim($data[8]);
        $psprID              = trim($data[9]);
        $brandID             = trim($data[13]);
        $productReferenceNo  = trim($data[15]);
        $ucID                = trim($data[16]);
        $sourceVolume        = trim($data[19]);
        $yearTypeID          = trim($data[20]);
        $startYear           = trim($data[21]);
        $endYear             = trim($data[22]);
        $referenceID         = trim($data[23]);
        
        // Convert to proper types
        $vehicleID           = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $countryID           = filter_var($countryID, FILTER_VALIDATE_INT);
        $foodTypeID          = filter_var($foodTypeID, FILTER_VALIDATE_INT);
        $psID                = filter_var($psID, FILTER_VALIDATE_INT);
        $psprID              = filter_var($psprID, FILTER_VALIDATE_INT);
        $brandID             = filter_var($brandID, FILTER_VALIDATE_INT);
        $productReferenceNo  = filter_var($productReferenceNo, FILTER_VALIDATE_INT);
        $ucID                = filter_var($ucID, FILTER_VALIDATE_INT);
        $sourceVolume        = filter_var($sourceVolume, FILTER_VALIDATE_FLOAT);
        $yearTypeID          = filter_var($yearTypeID, FILTER_VALIDATE_INT);
        $startYear           = filter_var($startYear, FILTER_VALIDATE_INT);
        $endYear             = filter_var($endYear, FILTER_VALIDATE_INT);
        $referenceID         = filter_var($referenceID, FILTER_VALIDATE_INT);

        if ($vehicleID === false || $countryID === false || $foodTypeID === false || $psID === false || $psprID === false || $brandID === false || $productReferenceNo === false || $ucID === false || $sourceVolume === false || $yearTypeID === false || $startYear === false || $endYear === false || $referenceID === false) {
            echo "Error: Invalid data format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Debugging: Show extracted values
        echo "VehicleID: $vehicleID, CountryID: $countryID, FoodTypeID: $foodTypeID, PS_ID: $psID, Origin: $origin, PSPRID: $psprID, BrandID: $brandID, ProductReferenceNo: $productReferenceNo, UC_ID: $ucID, SourceVolume: $sourceVolume, YearTypeID: $yearTypeID, StartYear: $startYear, EndYear: $endYear, ReferenceID: $referenceID<br>";

        // Correct INSERT to match the final table structure
        $sql = "
            INSERT INTO supply (
                VehicleID,
                CountryID,
                FoodTypeID,
                PS_ID,
                Origin,
                PSPRID,
                BrandID,
                ProductReferenceNo,
                UC_ID,
                SourceVolume,
                YearTypeID,
                StartYear,
                EndYear,
                ReferenceID
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($sql);

        // Updated bind_param type string: now 14 characters for 14 bind variables
        $stmt->bind_param(
            'iiiisiiidiiiii',
            $vehicleID,         // i
            $countryID,         // i
            $foodTypeID,        // i
            $psID,              // i
            $origin,            // s
            $psprID,            // i
            $brandID,           // i
            $productReferenceNo,// i
            $ucID,              // i
            $sourceVolume,      // d
            $yearTypeID,        // i
            $startYear,         // i
            $endYear,           // i
            $referenceID        // i
        );

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
            echo "ID: {$row['SupplyID']}, VehicleID: {$row['VehicleID']}, CountryID: {$row['CountryID']}, FoodTypeID: {$row['FoodTypeID']}, PS_ID: {$row['PS_ID']}, Origin: {$row['Origin']}, PSPRID: {$row['PSPRID']}, BrandID: {$row['BrandID']}, ProductReferenceNo: {$row['ProductReferenceNo']}, UC_ID: {$row['UC_ID']}, SourceVolume: {$row['SourceVolume']}, YearTypeID: {$row['YearTypeID']}, StartYear: {$row['StartYear']}, EndYear: {$row['EndYear']}, ReferenceID: {$row['ReferenceID']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>








