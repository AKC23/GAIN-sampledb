<?php
// insert_producer_processor.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'producerprocessor' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producerprocessor";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producerprocessor' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producerprocessor': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'producerprocessor' table with foreign keys
$createTableSQL = "
    CREATE TABLE producerprocessor (
        ProducerProcessorID INT(11) AUTO_INCREMENT PRIMARY KEY,
        EntityID INT(11) NOT NULL,
        TaskDoneByEntity VARCHAR(255),
        ProductionCapacityVolume DECIMAL(20, 3),
        PercentageOfCapacityUsed DECIMAL(10, 2),
        AnnualProductionSupplyVolume DECIMAL(20, 3),
        ProducerReferenceID INT(11),
        FOREIGN KEY (EntityID) REFERENCES entity(EntityID),
        FOREIGN KEY (ProducerReferenceID) REFERENCES producerreference(ProducerReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producerprocessor' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid EntityIDs and ProducerReferenceIDs
$validEntityIDs = array();
$validProducerReferenceIDs = array();

$result = $conn->query("SELECT EntityID FROM entity");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validEntityIDs[] = $row['EntityID'];
    }
} else {
    echo "Error getting valid EntityIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT ProducerReferenceID FROM producerreference");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validProducerReferenceIDs[] = $row['ProducerReferenceID'];
    }
} else {
    echo "Error getting valid ProducerReferenceIDs: " . $conn->error . "<br>";
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
        $taskDoneByEntity = trim($data[2]);
        $productionCapacityVolume = trim($data[3]);
        $percentageOfCapacityUsed = trim($data[4]);
        $producerReferenceID = trim($data[6]);
        
        // Remove any extra spaces between the name and comma
        $taskDoneByEntity = preg_replace('/\s+,/', ',', $taskDoneByEntity);
        
        // Convert to proper types
        $entityID = filter_var($entityID, FILTER_VALIDATE_INT);
        $productionCapacityVolume = filter_var($productionCapacityVolume, FILTER_VALIDATE_FLOAT);
        $percentageOfCapacityUsed = filter_var($percentageOfCapacityUsed, FILTER_VALIDATE_FLOAT);
        $producerReferenceID = filter_var($producerReferenceID, FILTER_VALIDATE_INT);

        if ($entityID === false || $entityID === null) {
            echo "Error: Invalid EntityID format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $taskDoneByEntity = mysqli_real_escape_string($conn, $taskDoneByEntity);

        // Calculate AnnualProductionSupplyVolume
        $annualProductionSupplyVolume = ($productionCapacityVolume * $percentageOfCapacityUsed) / 100;

        // Debugging: Show extracted values
        echo "EntityID: '$entityID', TaskDoneByEntity: '$taskDoneByEntity', ProductionCapacityVolume: $productionCapacityVolume, PercentageOfCapacityUsed: $percentageOfCapacityUsed, AnnualProductionSupplyVolume: $annualProductionSupplyVolume, ProducerReferenceID: $producerReferenceID<br>";

        // Validate foreign keys
        if (!in_array($entityID, $validEntityIDs)) {
            echo "Error: EntityID $entityID does not exist in entity table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }
        if (!empty($producerReferenceID) && !in_array($producerReferenceID, $validProducerReferenceIDs)) {
            echo "Error: ProducerReferenceID $producerReferenceID does not exist in producerreference table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO producerprocessor (EntityID, TaskDoneByEntity, ProductionCapacityVolume, PercentageOfCapacityUsed, AnnualProductionSupplyVolume, ProducerReferenceID) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isdddi", $entityID, $taskDoneByEntity, $productionCapacityVolume, $percentageOfCapacityUsed, $annualProductionSupplyVolume, $producerReferenceID);

        if ($stmt->execute()) {
            $producerProcessorID = $conn->insert_id;
            echo "✓ Inserted producerprocessor with ID: $producerProcessorID<br>";
        } else {
            echo "Error inserting producerprocessor: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final producerprocessor table contents:<br>";
    $result = $conn->query("SELECT * FROM producerprocessor ORDER BY ProducerProcessorID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ProducerProcessorID']}, EntityID: {$row['EntityID']}, TaskDoneByEntity: {$row['TaskDoneByEntity']}, ProductionCapacityVolume: {$row['ProductionCapacityVolume']}, PercentageOfCapacityUsed: {$row['PercentageOfCapacityUsed']}, AnnualProductionSupplyVolume: {$row['AnnualProductionSupplyVolume']}, ProducerReferenceID: {$row['ProducerReferenceID']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
