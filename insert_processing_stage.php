<?php
// insert_processing_stage.php   
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'processingstage' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS processingstage";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'processingstage' dropped successfully.<br>";
} else {
    echo "Error dropping table 'processingstage': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'processingstage' table
$createTableSQL = "
    CREATE TABLE processingstage (
        PSID INT(11) AUTO_INCREMENT PRIMARY KEY,
        ProcessingStageName VARCHAR(255) NOT NULL,
        ExtractionRate DECIMAL(10, 2),
        VehicleID INT(11),
        FOREIGN KEY (VehicleID) REFERENCES foodvehicle(VehicleID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'processingstage' created successfully.<br>";
} else {
    echo "Error creating table 'processingstage': " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/processing_stage.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 3) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $processingStageName = trim($data[0]);
        $vehicleID = trim($data[1]);
        $extractionRate = trim($data[2]);
        
        // Remove any extra spaces between the name and comma
        $processingStageName = preg_replace('/\s+,/', ',', $processingStageName);
        
        // Convert to proper types
        $vehicleID = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $extractionRate = filter_var($extractionRate, FILTER_VALIDATE_FLOAT);
        if ($vehicleID === false || $vehicleID === null || $extractionRate === false || $extractionRate === null) {
            echo "Error: Invalid VehicleID or ExtractionRate format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $processingStageName = mysqli_real_escape_string($conn, $processingStageName);

        // Debugging: Show extracted values
        echo "VehicleID: $vehicleID, ProcessingStageName: '$processingStageName', ExtractionRate: $extractionRate<br>";

        if (empty($processingStageName)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO processingstage (ProcessingStageName, VehicleID, ExtractionRate) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sid", $processingStageName, $vehicleID, $extractionRate);

        if ($stmt->execute()) {
            $psid = $conn->insert_id;
            echo "✓ Inserted processing stage '$processingStageName' with ID: $psid<br>";
        } else {
            echo "Error inserting processing stage: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final processingstage table contents:<br>";
    $result = $conn->query("SELECT ps.*, fv.VehicleName 
                           FROM processingstage ps 
                           JOIN FoodVehicle fv ON ps.VehicleID = fv.VehicleID 
                           ORDER BY ps.PSID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['PSID']}, VehicleID: {$row['VehicleID']}, " .
                 "Vehicle: {$row['VehicleName']}, Stage: {$row['ProcessingStageName']}, ExtractionRate: {$row['ExtractionRate']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>