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
        ProducerProcessorName VARCHAR(255) NOT NULL,
        CompanyGroup VARCHAR(255),
        VehicleID INT(11) NOT NULL,
        AdminLevel1 VARCHAR(255),
        AdminLevel2 VARCHAR(255),
        AdminLevel3 VARCHAR(255),
        UDC VARCHAR(255),
        Thana VARCHAR(255),
        Upazila VARCHAR(255),
        CountryID INT(11) NOT NULL,
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (CountryID) REFERENCES country(Country_ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'entities' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid VehicleIDs and CountryIDs
$validVehicleIDs = array();
$validCountryIDs = array();

$result = $conn->query("SELECT VehicleID FROM FoodVehicle");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validVehicleIDs[] = $row['VehicleID'];
    }
} else {
    echo "Error getting valid VehicleIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT Country_ID FROM country");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCountryIDs[] = $row['Country_ID'];
    }
} else {
    echo "Error getting valid CountryIDs: " . $conn->error . "<br>";
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
        if (count($data) < 10) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $producerProcessorName = trim($data[0]);
        $companyGroup = trim($data[1]);
        $vehicleID = trim($data[3]);
        $admin1 = trim($data[4]);
        $admin2 = trim($data[5]);
        $admin3 = trim($data[6]);
        $udc = trim($data[7]);
        $thana = trim($data[8]);
        $upazila = trim($data[9]);
        $countryID = trim($data[11]);
        
        // Remove any extra spaces between the name and comma
        $companyGroup = preg_replace('/\s+,/', ',', $companyGroup);
        $producerProcessorName = preg_replace('/\s+,/', ',', $producerProcessorName);
        $admin1 = preg_replace('/\s+,/', ',', $admin1);
        $admin2 = preg_replace('/\s+,/', ',', $admin2);
        $admin3 = preg_replace('/\s+,/', ',', $admin3);
        $udc = preg_replace('/\s+,/', ',', $udc);
        $thana = preg_replace('/\s+,/', ',', $thana);
        $upazila = preg_replace('/\s+,/', ',', $upazila);
        
        // Convert to proper types
        $vehicleID = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $countryID = filter_var($countryID, FILTER_VALIDATE_INT);
        if ($vehicleID === false || $vehicleID === null || $countryID === false || $countryID === null) {
            echo "Error: Invalid VehicleID or CountryID format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $companyGroup = mysqli_real_escape_string($conn, $companyGroup);
        $producerProcessorName = mysqli_real_escape_string($conn, $producerProcessorName);
        $admin1 = mysqli_real_escape_string($conn, $admin1);
        $admin2 = mysqli_real_escape_string($conn, $admin2);
        $admin3 = mysqli_real_escape_string($conn, $admin3);
        $udc = mysqli_real_escape_string($conn, $udc);
        $thana = mysqli_real_escape_string($conn, $thana);
        $upazila = mysqli_real_escape_string($conn, $upazila);

        // Debugging: Show extracted values
        echo "ProducerProcessorName: '$producerProcessorName', CompanyGroup: '$companyGroup', VehicleID: $vehicleID, AdminLevel1: '$admin1', AdminLevel2: '$admin2', AdminLevel3: '$admin3', UDC: '$udc', Thana: '$thana', Upazila: '$upazila', CountryID: $countryID<br>";

        // Validate VehicleID and CountryID
        if (!in_array($vehicleID, $validVehicleIDs)) {
            echo "Error: VehicleID $vehicleID does not exist in FoodVehicle table. Skipping row.<br>";
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

        $sql = "INSERT INTO entities (ProducerProcessorName, CompanyGroup, VehicleID, AdminLevel1, AdminLevel2, AdminLevel3, UDC, Thana, Upazila, CountryID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissssssi", $producerProcessorName, $companyGroup, $vehicleID, $admin1, $admin2, $admin3, $udc, $thana, $upazila, $countryID);

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
    $result = $conn->query("SELECT e.*, fv.VehicleName, c.Country_Name 
                           FROM entities e 
                           JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID 
                           JOIN country c ON e.CountryID = c.Country_ID 
                           ORDER BY e.EntityID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['EntityID']}, ProducerProcessorName: {$row['ProducerProcessorName']}, CompanyGroup: {$row['CompanyGroup']}, VehicleID: {$row['VehicleID']}, AdminLevel1: {$row['AdminLevel1']}, AdminLevel2: {$row['AdminLevel2']}, AdminLevel3: {$row['AdminLevel3']}, UDC: {$row['UDC']}, Thana: {$row['Thana']}, Upazila: {$row['Upazila']}, CountryID: {$row['CountryID']}, VehicleName: {$row['VehicleName']}, Country: {$row['Country_Name']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
