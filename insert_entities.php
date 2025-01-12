<?php
// insert_entities.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'entities' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS entities";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'entities' dropped successfully.<br>";
} else {
    echo "Error dropping table 'entities': " . $conn->error . "<br>";
}

// SQL query to create the 'entities' table with foreign keys
$createTableSQL = "
    CREATE TABLE entities (
        EntityID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleID INT(11) NOT NULL,
        CompanyGroup VARCHAR(255),
        ProducerProcessorName VARCHAR(255) NOT NULL,
        ProducerProcessorAddress VARCHAR(255) NOT NULL,
        Country_ID INT(11) NOT NULL,
        
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (Country_ID) REFERENCES country(Country_ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'entities' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/entities.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 5) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $vehicleID = trim($data[0]);
        $companyGroup = trim($data[1]);
        $producerProcessorName = trim($data[2]);
        $producerProcessorAddress = trim($data[3]);
        $countryID = trim($data[4]);
        
        // Remove any extra spaces between the name and comma
        $companyGroup = preg_replace('/\s+,/', ',', $companyGroup);
        $producerProcessorName = preg_replace('/\s+,/', ',', $producerProcessorName);
        $producerProcessorAddress = preg_replace('/\s+,/', ',', $producerProcessorAddress);
        
        // Convert to proper types
        $vehicleID = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $countryID = filter_var($countryID, FILTER_VALIDATE_INT);
        if ($vehicleID === false || $vehicleID === null || $countryID === false || $countryID === null) {
            echo "Error: Invalid VehicleID or Country_ID format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $companyGroup = mysqli_real_escape_string($conn, $companyGroup);
        $producerProcessorName = mysqli_real_escape_string($conn, $producerProcessorName);
        $producerProcessorAddress = mysqli_real_escape_string($conn, $producerProcessorAddress);

        // Debugging: Show extracted values
        echo "VehicleID: $vehicleID, CompanyGroup: '$companyGroup', ProducerProcessorName: '$producerProcessorName', ProducerProcessorAddress: '$producerProcessorAddress', Country_ID: $countryID<br>";

        if (empty($producerProcessorName) || empty($producerProcessorAddress)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO entities (VehicleID, CompanyGroup, ProducerProcessorName, ProducerProcessorAddress, Country_ID) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $vehicleID, $companyGroup, $producerProcessorName, $producerProcessorAddress, $countryID);

        if ($stmt->execute()) {
            $entityID = $conn->insert_id;
            echo "✓ Inserted entity '$producerProcessorName' with ID: $entityID<br>";
        } else {
            echo "Error inserting entity: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final entities table contents:<br>";
    $result = $conn->query("SELECT e.*, c.Country_Name 
                           FROM entities e 
                           JOIN country c ON e.Country_ID = c.Country_ID 
                           ORDER BY e.EntityID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['EntityID']}, VehicleID: {$row['VehicleID']}, CompanyGroup: {$row['CompanyGroup']}, ProducerProcessorName: {$row['ProducerProcessorName']}, ProducerProcessorAddress: {$row['ProducerProcessorAddress']}, Country_ID: {$row['Country_ID']}, Country: {$row['Country_Name']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
