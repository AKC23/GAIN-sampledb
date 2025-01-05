
<?php
include('db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS total_local_food_production";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'total_local_food_production' dropped successfully.<br>";
}

// Create table
$createTableSQL = "
CREATE TABLE total_local_food_production (
    DataID INT PRIMARY KEY AUTO_INCREMENT,
    VehicleID INT,
    FoodTypeID INT,
    RawCropsID INT,
    Country_ID INT,
    SourceVolumeUnit VARCHAR(50),
    SourceVolume DECIMAL(15,3),
    ConvertedValue DECIMAL(15,3),
    VolumeUnit VARCHAR(50),
    PeriodicalUnit VARCHAR(50),
    StartYear VARCHAR(50),
    EndYear VARCHAR(50),
    AccessedDate VARCHAR(50),
    Source VARCHAR(255),
    Link TEXT,
    Process TEXT,
    FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
    FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
    FOREIGN KEY (RawCropsID) REFERENCES raw_crops(RawCropsID),
    FOREIGN KEY (Country_ID) REFERENCES Country(Country_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'total_local_food_production' created successfully.<br>";
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
$csvFile = 'data/total_local_food_production.csv';
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header
    fgetcsv($handle);
    
    // Prepare insert statement
    $stmt = $conn->prepare("
        INSERT INTO total_local_food_production (
            VehicleID, FoodTypeID, RawCropsID, Country_ID,
            SourceVolumeUnit, SourceVolume, ConvertedValue, VolumeUnit,
            PeriodicalUnit, StartYear, EndYear, AccessedDate,
            Source, Link, Process
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if ($stmt === FALSE) {
        die("Error preparing statement: " . $conn->error);
    }

    while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {
        // Map CSV columns to variables
        $vehicleID = (int)$data[1];
        $foodTypeID = (int)$data[3];
        $rawCropsID = (int)$data[5];
        $countryID = (int)$data[7];
        
        // Validate foreign keys
        if (!in_array($vehicleID, $validIDs['vehicle']) ||
            !in_array($foodTypeID, $validIDs['foodtype']) ||
            !in_array($rawCropsID, $validIDs['rawcrops']) ||
            !in_array($countryID, $validIDs['country'])) {
            echo "Skipping row - Invalid foreign key(s)<br>";
            continue;
        }

        $stmt->bind_param(
            "iiiisddssssssss",
            $vehicleID,
            $foodTypeID,
            $rawCropsID,
            $countryID,
            $data[8],  // SourceVolumeUnit
            $data[9],  // SourceVolume
            $data[10], // ConvertedValue
            $data[11], // VolumeUnit
            $data[12], // PeriodicalUnit
            $data[13], // StartYear
            $data[14], // EndYear
            $data[15], // AccessedDate
            $data[16], // Source
            $data[17], // Link
            $data[18]  // Process
        );

        if ($stmt->execute()) {
            echo "Inserted record for {$data[2]} ({$data[13]} - {$data[14]})<br>";
        } else {
            echo "Error inserting record: " . $stmt->error . "<br>";
        }
    }

    $stmt->close();
    fclose($handle);
    echo "Data import completed.<br>";
} else {
    echo "Error opening CSV file<br>";
}
?>