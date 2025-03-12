<?php
// insert_consumption.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'consumption' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS consumption";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'consumption' dropped successfully.<br>";
} else {
    echo "Error dropping table 'consumption': " . $conn->error . "<br>";
}

// SQL query to create the 'consumption' table with the specified columns and foreign keys
$createTableSQL = "
    CREATE TABLE consumption (
        ConsumptionID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleID INT(11) NOT NULL,
        GL1ID INT(11) NOT NULL,
        GL2ID INT(11) NOT NULL,
        GL3ID INT(11) NOT NULL,
        GenderID INT(11) NOT NULL,
        AgeID INT(11) NOT NULL,
        NumberOfPeople INT(11) NOT NULL,
        SourceVolume DECIMAL(20, 6) NOT NULL,
        VolumeMTY DECIMAL(20, 6) NOT NULL,
        UCID INT(11) NOT NULL,
        YearTypeID INT(11) NOT NULL,
        StartYear INT(11) NOT NULL,
        EndYear INT(11) NOT NULL,
        ReferenceID INT(11) NOT NULL,
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (GL1ID) REFERENCES geographylevel1(GL1ID),
        FOREIGN KEY (GL2ID) REFERENCES geographylevel2(GL2ID),
        FOREIGN KEY (GL3ID) REFERENCES geographylevel3(GL3ID),
        FOREIGN KEY (GenderID) REFERENCES gender(GenderID),
        FOREIGN KEY (AgeID) REFERENCES age(AgeID),
        FOREIGN KEY (UCID) REFERENCES measureunit1(UCID),
        FOREIGN KEY (YearTypeID) REFERENCES yeartype(YearTypeID),
        FOREIGN KEY (ReferenceID) REFERENCES reference(ReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'consumption' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/consumption.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 22) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $vehicleID       = trim($data[0]);
        $gl1id           = trim($data[2]);
        $gl2id           = trim($data[4]);
        $gl3id           = trim($data[6]);
        $genderID        = trim($data[8]);
        $ageID           = trim($data[10]);
        $numberOfPeople  = trim($data[12]);
        $sourceVolume    = str_replace(',', '', trim($data[16])); // Remove commas
        $ucid            = trim($data[13]);
        $yearTypeID      = trim($data[18]);
        $startYear       = trim($data[20]);
        $endYear         = trim($data[21]);
        $referenceID     = trim($data[22]);
        
        // Convert to proper types
        $vehicleID       = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $gl1id           = filter_var($gl1id, FILTER_VALIDATE_INT);
        $gl2id           = filter_var($gl2id, FILTER_VALIDATE_INT);
        $gl3id           = filter_var($gl3id, FILTER_VALIDATE_INT);
        $genderID        = filter_var($genderID, FILTER_VALIDATE_INT);
        $ageID           = filter_var($ageID, FILTER_VALIDATE_INT);
        $numberOfPeople  = filter_var($numberOfPeople, FILTER_VALIDATE_INT);
        $sourceVolume    = filter_var($sourceVolume, FILTER_VALIDATE_FLOAT);
        $ucid            = filter_var($ucid, FILTER_VALIDATE_INT);
        $yearTypeID      = filter_var($yearTypeID, FILTER_VALIDATE_INT);
        $startYear       = filter_var($startYear, FILTER_VALIDATE_INT);
        $endYear         = filter_var($endYear, FILTER_VALIDATE_INT);
        $referenceID     = filter_var($referenceID, FILTER_VALIDATE_INT);

        if ($vehicleID === false || $gl1id === false || $gl2id === false || $gl3id === false || $genderID === false || $ageID === false || $numberOfPeople === false || $sourceVolume === false || $ucid === false || $yearTypeID === false || $startYear === false || $endYear === false || $referenceID === false) {
            echo "Error: Invalid data format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Calculate VolumeMTY based on UCID and SourceVolume
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error: Invalid UCID $ucid in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Debugging: Show extracted values
        echo "VehicleID: $vehicleID, GL1ID: $gl1id, GL2ID: $gl2id, GL3ID: $gl3id, GenderID: $genderID, AgeID: $ageID, NumberOfPeople: $numberOfPeople, SourceVolume: $sourceVolume, VolumeMTY: $volumeMTY, UCID: $ucid, YearTypeID: $yearTypeID, StartYear: $startYear, EndYear: $endYear, ReferenceID: $referenceID<br>";

        // Correct INSERT to match the final table structure
        $sql = "
            INSERT INTO consumption (
                VehicleID,
                GL1ID,
                GL2ID,
                GL3ID,
                GenderID,
                AgeID,
                NumberOfPeople,
                SourceVolume,
                VolumeMTY,
                UCID,
                YearTypeID,
                StartYear,
                EndYear,
                ReferenceID
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $conn->prepare($sql);

        // Updated bind_param type string: now 14 characters for 14 bind variables
        $stmt->bind_param(
            'iiiiiiiidddiii',
            $vehicleID,         // i
            $gl1id,             // i
            $gl2id,             // i
            $gl3id,             // i
            $genderID,          // i
            $ageID,             // i
            $numberOfPeople,    // i
            $sourceVolume,      // d
            $volumeMTY,         // d
            $ucid,              // i
            $yearTypeID,        // i
            $startYear,         // i
            $endYear,           // i
            $referenceID        // i
        );

        if ($stmt->execute()) {
            $consumptionID = $conn->insert_id;
            echo "✓ Inserted consumption record with ID: $consumptionID<br>";
        } else {
            echo "Error inserting consumption record: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final consumption table contents:<br>";
    $result = $conn->query("SELECT * FROM consumption ORDER BY ConsumptionID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ConsumptionID']}, VehicleID: {$row['VehicleID']}, GL1ID: {$row['GL1ID']}, GL2ID: {$row['GL2ID']}, GL3ID: {$row['GL3ID']}, GenderID: {$row['GenderID']}, AgeID: {$row['AgeID']}, NumberOfPeople: {$row['NumberOfPeople']}, SourceVolume: {$row['SourceVolume']}, VolumeMTY: {$row['VolumeMTY']}, UCID: {$row['UCID']}, YearTypeID: {$row['YearTypeID']}, StartYear: {$row['StartYear']}, EndYear: {$row['EndYear']}, ReferenceID: {$row['ReferenceID']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
