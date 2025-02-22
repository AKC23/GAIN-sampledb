<?php
// insert_entities.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'entity' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS entity";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'entity' dropped successfully.<br>";
} else {
    echo "Error dropping table 'entity': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'entity' table with foreign keys
$createTableSQL = "
    CREATE TABLE entity (
        EntityID INT(11) AUTO_INCREMENT PRIMARY KEY,
        ProducerProcessorName VARCHAR(255) NOT NULL,
        CompanyID INT(11) NOT NULL,
        VehicleID INT(11) NOT NULL,
        GL1ID INT(11),
        GL2ID INT(11),
        GL3ID INT(11),
        CountryID INT(11) NOT NULL,
        FOREIGN KEY (CompanyID) REFERENCES company(CompanyID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (GL1ID) REFERENCES geographylevel1(GL1ID),
        FOREIGN KEY (GL2ID) REFERENCES geographylevel2(GL2ID),
        FOREIGN KEY (GL3ID) REFERENCES geographylevel3(GL3ID),
        FOREIGN KEY (CountryID) REFERENCES country(CountryID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'entity' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid CompanyIDs, VehicleIDs, and CountryIDs
$validCompanyIDs = array();
$validVehicleIDs = array();
$validGL1IDs = array();
$validGL2IDs = array();
$validGL3IDs = array();
$validCountryIDs = array();

$result = $conn->query("SELECT CompanyID FROM company");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCompanyIDs[] = $row['CompanyID'];
    }
} else {
    echo "Error getting valid CompanyIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT VehicleID FROM FoodVehicle");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validVehicleIDs[] = $row['VehicleID'];
    }
} else {
    echo "Error getting valid VehicleIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT GL1ID FROM geographylevel1");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validGL1IDs[] = $row['GL1ID'];
    }
} else {
    echo "Error getting valid GL1IDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT GL2ID FROM geographylevel2");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validGL2IDs[] = $row['GL2ID'];
    }
} else {
    echo "Error getting valid GL2IDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT GL3ID FROM geographylevel3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validGL3IDs[] = $row['GL3ID'];
    }
} else {
    echo "Error getting valid GL3IDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT CountryID FROM country");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCountryIDs[] = $row['CountryID'];
    }
} else {
    echo "Error getting valid CountryIDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/entity.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 7) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $producerProcessorName = trim($data[0]);
        $companyID = trim($data[1]);
        $vehicleID = trim($data[3]);
        $gl1ID = trim($data[5]);
        $gl2ID = trim($data[7]);
        $gl3ID = trim($data[9]);
        $countryID = trim($data[11]);
        
        // Remove any extra spaces between the name and comma
        $producerProcessorName = preg_replace('/\s+,/', ',', $producerProcessorName);
        
        // Convert to proper types
        $companyID = filter_var($companyID, FILTER_VALIDATE_INT);
        $vehicleID = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $gl1ID = filter_var($gl1ID, FILTER_VALIDATE_INT);
        $gl2ID = filter_var($gl2ID, FILTER_VALIDATE_INT);
        $gl3ID = filter_var($gl3ID, FILTER_VALIDATE_INT);
        $countryID = filter_var($countryID, FILTER_VALIDATE_INT);
        
		// Prepare variables for bind_param
		$null_gl1id = empty($gl1ID) && $gl1ID !== 0 ? NULL : $gl1ID;
		$null_gl2id = empty($gl2ID) && $gl2ID !== 0 ? NULL : $gl2ID;
		$null_gl3id = empty($gl3ID) && $gl3ID !== 0 ? NULL : $gl3ID;

        if ($companyID === false || $companyID === null || $vehicleID === false || $vehicleID === null || $countryID === false || $countryID === null) {
            echo "Error: Invalid CompanyID, VehicleID or CountryID format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $producerProcessorName = mysqli_real_escape_string($conn, $producerProcessorName);

        // Debugging: Show extracted values
        echo "ProducerProcessorName: '$producerProcessorName', CompanyID: '$companyID', VehicleID: $vehicleID, GL1ID: '$gl1ID', GL2ID: '$gl2ID', GL3ID: '$gl3ID', CountryID: $countryID<br>";

        // Validate foreign keys
        if (!in_array($companyID, $validCompanyIDs)) {
            echo "Error: CompanyID $companyID does not exist in company table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($vehicleID, $validVehicleIDs)) {
            echo "Error: VehicleID $vehicleID does not exist in FoodVehicle table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }
        if (!empty($gl1ID) && !in_array($gl1ID, $validGL1IDs)) {
            echo "Error: GL1ID $gl1ID does not exist in geographylevel1 table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }
        if (!empty($gl2ID) && !in_array($gl2ID, $validGL2IDs)) {
            echo "Error: GL2ID $gl2ID does not exist in geographylevel2 table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }
        if (!empty($gl3ID) && !in_array($gl3ID, $validGL3IDs)) {
            echo "Error: GL3ID $gl3ID does not exist in geographylevel3 table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($countryID, $validCountryIDs)) {
            echo "Error: CountryID $countryID does not exist in country table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        if (empty($producerProcessorName)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO entity (ProducerProcessorName, CompanyID, VehicleID, GL1ID, GL2ID, GL3ID, CountryID) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Bind parameters correctly, handling NULL values
		$types = "siisiii"; // Define types of parameters
		$params = array(&$types, &$producerProcessorName, &$companyID, &$vehicleID, &$null_gl1id, &$null_gl2id, &$null_gl3id, &$countryID);

		call_user_func_array(array($stmt, 'bind_param'), $params);

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
    echo "<br>Final entity table contents:<br>";
    $result = $conn->query("SELECT * FROM entity ORDER BY EntityID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['EntityID']}, ProducerProcessorName: {$row['ProducerProcessorName']}, CompanyID: {$row['CompanyID']}, VehicleID: {$row['VehicleID']}, GL1ID: {$row['GL1ID']}, GL2ID: {$row['GL2ID']}, GL3ID: {$row['GL3ID']}, CountryID: {$row['CountryID']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
