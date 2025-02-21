<?php
// insert_extraction_conversion.php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'extractionconversion' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS extractionconversion";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'extractionconversion' dropped successfully.<br>";
} else {
    echo "Error dropping table 'extractionconversion': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'extractionconversion' table
$createTableSQL = "
    CREATE TABLE extractionconversion (
        ExtractionID INT(11) AUTO_INCREMENT PRIMARY KEY,
        ExtractionRate DECIMAL(10, 2) NOT NULL,
        VehicleID INT(11) NOT NULL,
        FoodTypeID INT(11) NOT NULL,
        ReferenceID INT(11) NOT NULL,
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
        FOREIGN KEY (ReferenceID) REFERENCES reference(ReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'extractionconversion' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/extraction_conversion.csv';  // Update with the exact path of your CSV file

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
        $extractionRate = trim($data[0]);
        $vehicleID = trim($data[2]);
        $foodTypeID = trim($data[4]);
        $referenceID = trim($data[5]);
        
        // Remove any extra spaces between the name and comma
        $extractionRate = preg_replace('/\s+,/', ',', $extractionRate);
        
        // Convert to proper types
        $extractionRate = filter_var($extractionRate, FILTER_VALIDATE_FLOAT);
        $vehicleID = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $foodTypeID = filter_var($foodTypeID, FILTER_VALIDATE_INT);
        $referenceID = filter_var($referenceID, FILTER_VALIDATE_INT);

        if ($extractionRate === false || $vehicleID === false || $foodTypeID === false || $referenceID === false) {
            echo "Error: Invalid data format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Debugging: Show extracted values
        echo "ExtractionRate: $extractionRate, VehicleID: $vehicleID, FoodTypeID: $foodTypeID, ReferenceID: $referenceID<br>";

        $sql = "INSERT INTO extractionconversion (ExtractionRate, VehicleID, FoodTypeID, ReferenceID) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("diii", $extractionRate, $vehicleID, $foodTypeID, $referenceID);

        if ($stmt->execute()) {
            $extractionID = $conn->insert_id;
            echo "✓ Inserted extraction conversion with ID: $extractionID<br>";
        } else {
            echo "Error inserting extraction conversion: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final extractionconversion table contents:<br>";
    $result = $conn->query("SELECT * FROM extractionconversion ORDER BY ExtractionID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ExtractionID']}, ExtractionRate: {$row['ExtractionRate']}, " .
                 "VehicleID: {$row['VehicleID']}, FoodTypeID: {$row['FoodTypeID']}, ReferenceID: {$row['ReferenceID']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
