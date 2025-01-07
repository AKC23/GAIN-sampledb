<?php
// insert_total_local_food_production.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'total_local_food_production' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS total_local_food_production";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'total_local_food_production' dropped successfully.<br>";
} else {
    echo "Error dropping table 'total_local_food_production': " . $conn->error . "<br>";
}

// SQL query to create the 'total_local_food_production' table with foreign keys
$createTableSQL = "
    CREATE TABLE total_local_food_production (
        ProductionID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleID INT(11) NOT NULL,
        FoodTypeID INT(11) NOT NULL,
        RawCropsID INT(11) NOT NULL,
        Country_ID INT(11) NOT NULL,
        PeriodicalUnit VARCHAR(50) NOT NULL,
        Volume FLOAT NOT NULL,
        UnitID INT(11) NOT NULL,
        StartYear YEAR NOT NULL,
        EndYear YEAR NOT NULL,
        AccessedDate DATE NOT NULL,
        Source VARCHAR(255) NOT NULL,
        Link VARCHAR(255) NOT NULL,
        ProcessToObtainData VARCHAR(255) NOT NULL,
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
        FOREIGN KEY (RawCropsID) REFERENCES raw_crops(RawCropsID),
        FOREIGN KEY (Country_ID) REFERENCES country(Country_ID),
        FOREIGN KEY (UnitID) REFERENCES measure_unit(UnitID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'total_local_food_production' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/total_local_food_production.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 14) {
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
        $volume = trim($data[5]);
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
        $volume = filter_var(str_replace(',', '', $volume), FILTER_VALIDATE_FLOAT);
        $unitID = filter_var($unitID, FILTER_VALIDATE_INT);
        $startYear = filter_var(date('Y', strtotime($startYear)), FILTER_VALIDATE_INT);
        $endYear = filter_var(date('Y', strtotime($endYear)), FILTER_VALIDATE_INT);
        $accessedDate = date('Y-m-d', strtotime($accessedDate));

        if ($vehicleID === false || $foodTypeID === false || $rawCropsID === false || $countryID === false || $volume === false || $unitID === false || $startYear === false || $endYear === false || $accessedDate === false) {
            echo "Error: Invalid data format in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $periodicalUnit = mysqli_real_escape_string($conn, $periodicalUnit);
        $source = mysqli_real_escape_string($conn, $source);
        $link = mysqli_real_escape_string($conn, $link);
        $processToObtainData = mysqli_real_escape_string($conn, $processToObtainData);

        // Debugging: Show extracted values
        echo "VehicleID: $vehicleID, FoodTypeID: $foodTypeID, RawCropsID: $rawCropsID, Country_ID: $countryID, PeriodicalUnit: '$periodicalUnit', Volume: $volume, UnitID: $unitID, StartYear: $startYear, EndYear: $endYear, AccessedDate: '$accessedDate', Source: '$source', Link: '$link', ProcessToObtainData: '$processToObtainData'<br>";

        $sql = "INSERT INTO total_local_food_production (VehicleID, FoodTypeID, RawCropsID, Country_ID, PeriodicalUnit, Volume, UnitID, StartYear, EndYear, AccessedDate, Source, Link, ProcessToObtainData) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisdiisssss", $vehicleID, $foodTypeID, $rawCropsID, $countryID, $periodicalUnit, $volume, $unitID, $startYear, $endYear, $accessedDate, $source, $link, $processToObtainData);

        if ($stmt->execute()) {
            $productionID = $conn->insert_id;
            echo "✓ Inserted total local food production with ID: $productionID<br>";
        } else {
            echo "Error inserting total local food production: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final total_local_food_production table contents:<br>";
    $result = $conn->query("SELECT * FROM total_local_food_production ORDER BY ProductionID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ProductionID']}, VehicleID: {$row['VehicleID']}, FoodTypeID: {$row['FoodTypeID']}, RawCropsID: {$row['RawCropsID']}, Country_ID: {$row['Country_ID']}, PeriodicalUnit: {$row['PeriodicalUnit']}, Volume: {$row['Volume']}, UnitID: {$row['UnitID']}, StartYear: {$row['StartYear']}, EndYear: {$row['EndYear']}, AccessedDate: {$row['AccessedDate']}, Source: {$row['Source']}, Link: {$row['Link']}, ProcessToObtainData: {$row['ProcessToObtainData']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
