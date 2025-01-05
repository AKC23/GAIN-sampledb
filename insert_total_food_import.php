<?php
include('db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS total_food_import";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'total_food_import' dropped successfully.<br>";
} else {
    echo "Error dropping table 'total_food_import': " . $conn->error . "<br>";
}

// Create table
$createTableSQL = "
CREATE TABLE total_food_import (
    ImportID INT(11) AUTO_INCREMENT PRIMARY KEY,
    VehicleID INT(11) NOT NULL,
    FoodTypeID INT(11) NOT NULL,
    RawCropsID INT(11) NOT NULL,
    Country_ID INT(11) NOT NULL,
    PeriodicalUnit VARCHAR(50) NOT NULL,
    SourceVolume FLOAT NOT NULL,
    UnitID INT(11) NOT NULL,
    StartYear YEAR NOT NULL,
    EndYear YEAR NOT NULL,
    AccessedDate DATE NOT NULL,
    Source VARCHAR(255) NOT NULL,
    Link VARCHAR(255) NOT NULL,
    ProcessToObtainData TEXT NOT NULL,

    FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (RawCropsID) REFERENCES raw_crops(RawCropsID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Country_ID) REFERENCES country(Country_ID) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (UnitID) REFERENCES measure_unit(UnitID) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'total_food_import' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid IDs from reference tables
$validIDs = array(
    'vehicle' => array(),
    'foodtype' => array(),
    'rawcrops' => array(),
    'country' => array()
);

// Get all valid IDs
$tables = array(
    'vehicle' => "SELECT VehicleID FROM FoodVehicle",
    'foodtype' => "SELECT FoodTypeID FROM FoodType",
    'rawcrops' => "SELECT RawCropsID FROM raw_crops",
    'country' => "SELECT Country_ID FROM Country"
);

foreach ($tables as $key => $sql) {
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $validIDs[$key][] = $row[array_key_first($row)];
        }
    }
}

// Read and insert CSV data
$csvFile = 'data/total_food_import.csv';
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
        if (count($data) < 13) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $vehicleID = trim($data[0]);
        $foodTypeID = trim($data[1]);
        $rawCropsID = trim($data[2]);
        $countryID = trim($data[3]);
        $periodicalUnit = trim($data[4]);
        $sourceVolume = trim($data[5]);
        $unitID = trim($data[6]);
        $startYear = trim($data[7]);
        $endYear = trim($data[8]);
        $accessedDate = trim($data[9]);
        $source = trim($data[10]);
        $link = trim($data[11]);
        $processToObtainData = trim($data[12]);
        
        // Remove any extra spaces between the name and comma
        $periodicalUnit = preg_replace('/\s+,/', ',', $periodicalUnit);
        $source = preg_replace('/\s+,/', ',', $source);
        $link = preg_replace('/\s+,/', ',', $link);
        $processToObtainData = preg_replace('/\s+,/', ',', $processToObtainData);
        
        // Convert to proper types
        $vehicleID = filter_var($vehicleID, FILTER_VALIDATE_INT);
        $foodTypeID = filter_var($foodTypeID, FILTER_VALIDATE_INT);
        $rawCropsID = filter_var($rawCropsID, FILTER_VALIDATE_INT);
        $countryID = filter_var($countryID, FILTER_VALIDATE_INT);
        $sourceVolume = filter_var(str_replace(',', '', $sourceVolume), FILTER_VALIDATE_FLOAT);
        $unitID = filter_var($unitID, FILTER_VALIDATE_INT);
        $startYear = filter_var(date('Y', strtotime($startYear)), FILTER_VALIDATE_INT);
        $endYear = filter_var(date('Y', strtotime($endYear)), FILTER_VALIDATE_INT);
        $accessedDate = date('Y-m-d', strtotime($accessedDate));

        if ($vehicleID === false || $foodTypeID === false || $rawCropsID === false || $countryID === false || $sourceVolume === false || $unitID === false || $startYear === false || $endYear === false || $accessedDate === false) {
            echo "Error: Invalid data format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $periodicalUnit = mysqli_real_escape_string($conn, $periodicalUnit);
        $source = mysqli_real_escape_string($conn, $source);
        $link = mysqli_real_escape_string($conn, $link);
        $processToObtainData = mysqli_real_escape_string($conn, $processToObtainData);

        // Debugging: Show extracted values
        echo "VehicleID: $vehicleID, FoodTypeID: $foodTypeID, RawCropsID: $rawCropsID, Country_ID: $countryID, PeriodicalUnit: '$periodicalUnit', SourceVolume: $sourceVolume, UnitID: $unitID, StartYear: $startYear, EndYear: $endYear, AccessedDate: '$accessedDate', Source: '$source', Link: '$link', ProcessToObtainData: '$processToObtainData'<br>";

        $sql = "INSERT INTO total_food_import (VehicleID, FoodTypeID, RawCropsID, Country_ID, PeriodicalUnit, SourceVolume, UnitID, StartYear, EndYear, AccessedDate, Source, Link, ProcessToObtainData) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisdiisssss", $vehicleID, $foodTypeID, $rawCropsID, $countryID, $periodicalUnit, $sourceVolume, $unitID, $startYear, $endYear, $accessedDate, $source, $link, $processToObtainData);

        if ($stmt->execute()) {
            $importID = $conn->insert_id;
            echo "✓ Inserted total food import with ID: $importID<br>";
        } else {
            echo "Error inserting total food import: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final total_food_import table contents:<br>";
    $result = $conn->query("SELECT * FROM total_food_import ORDER BY ImportID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ImportID']}, VehicleID: {$row['VehicleID']}, FoodTypeID: {$row['FoodTypeID']}, RawCropsID: {$row['RawCropsID']}, Country_ID: {$row['Country_ID']}, PeriodicalUnit: {$row['PeriodicalUnit']}, SourceVolume: {$row['SourceVolume']}, UnitID: {$row['UnitID']}, StartYear: {$row['StartYear']}, EndYear: {$row['EndYear']}, AccessedDate: {$row['AccessedDate']}, Source: {$row['Source']}, Link: {$row['Link']}, ProcessToObtainData: {$row['ProcessToObtainData']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}
?>