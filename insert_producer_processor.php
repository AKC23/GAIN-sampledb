<?php
// insert_producer_processor.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'producer_processor' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producer_processor";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producer_processor' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producer_processor': " . $conn->error . "<br>";
}

// SQL query to create the 'producer_processor' table with foreign keys
$createTableSQL = "
    CREATE TABLE producer_processor (
        ProcessorID INT(11) AUTO_INCREMENT PRIMARY KEY,
        EntityID INT(11) NOT NULL,
        TaskDoneByEntity VARCHAR(255),
        Productioncapacityvolume DECIMAL(10, 2),
        PercentageOfCapacityUsed DECIMAL(5, 2),
        AnnualProductionSupplyVolume DECIMAL(10, 2),
        BSTIReferenceNo VARCHAR(255),
        FOREIGN KEY (EntityID) REFERENCES entities(EntityID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producer_processor' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid EntityIDs
$validEntityIDs = array();
$result = $conn->query("SELECT EntityID FROM entities");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validEntityIDs[] = $row['EntityID'];
    }
} else {
    echo "Error getting valid EntityIDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/producer_processor.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 6) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $entityID = trim($data[0]);
        $taskDoneByEntity = trim($data[1]);
        $productionCapacityVolume = trim($data[2]);
        $capacityUsed = trim($data[3]);
        $annualProductionVolume = trim($data[4]);
        $bstiReferenceNumber = trim($data[5]);
        
        // Remove any extra spaces between the name and comma
        $taskDoneByEntity = preg_replace('/\s+,/', ',', $taskDoneByEntity);
        $bstiReferenceNumber = preg_replace('/\s+,/', ',', $bstiReferenceNumber);
        
        // Convert to proper types
        $entityID = filter_var($entityID, FILTER_VALIDATE_INT);
        $productionCapacityVolume = filter_var($productionCapacityVolume, FILTER_VALIDATE_FLOAT);
        $capacityUsed = filter_var($capacityUsed, FILTER_VALIDATE_FLOAT);
        $annualProductionVolume = filter_var($annualProductionVolume, FILTER_VALIDATE_FLOAT);
        if ($entityID === false || $entityID === null) {
            echo "Error: Invalid EntityID format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $taskDoneByEntity = mysqli_real_escape_string($conn, $taskDoneByEntity);
        $bstiReferenceNumber = mysqli_real_escape_string($conn, $bstiReferenceNumber);

        // Debugging: Show extracted values
        echo "EntityID: $entityID, Task Done By Entity: '$taskDoneByEntity', Production capacity volume (MT/Y): $productionCapacityVolume, % of capacity used: $capacityUsed, Annual production/ supply Volume (MT/Y): $annualProductionVolume, BSTI Reference Number: '$bstiReferenceNumber'<br>";

        // Validate EntityID
        if (!in_array($entityID, $validEntityIDs)) {
            echo "Error: EntityID $entityID does not exist in entities table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO producer_processor (EntityID, TaskDoneByEntity, Productioncapacityvolume, PercentageOfCapacityUsed, AnnualProductionSupplyVolume, BSTIReferenceNo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isddds", $entityID, $taskDoneByEntity, $productionCapacityVolume, $capacityUsed, $annualProductionVolume, $bstiReferenceNumber);

        if ($stmt->execute()) {
            $processorID = $conn->insert_id;
            echo "✓ Inserted producer/processor with ID: $processorID<br>";
        } else {
            echo "Error inserting producer/processor: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final producer_processor table contents:<br>";
    $result = $conn->query("SELECT pp.*, e.`Producer / Processor name` 
                           FROM producer_processor pp 
                           JOIN entities e ON pp.EntityID = e.EntityID 
                           ORDER BY pp.ProcessorID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ProcessorID']}, EntityID: {$row['EntityID']}, Task Done By Entity: {$row['TaskDoneByEntity']}, Production capacity volume (MT/Y): {$row['Productioncapacityvolume']}, % of capacity used: {$row['PercentageOfCapacityUsed']}, Annual production/ supply Volume (MT/Y): {$row['AnnualProductionSupplyVolume']}, BSTI Reference Number: {$row['BSTIReferenceNo']}, Producer / Processor name: {$row['Producer / Processor name']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
