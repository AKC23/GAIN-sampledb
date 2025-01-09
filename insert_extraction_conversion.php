<?php
// insert_extraction_conversion.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'extraction_conversion' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS extraction_conversion";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'extraction_conversion' dropped successfully.<br>";
} else {
    echo "Error dropping table 'extraction_conversion': " . $conn->error . "<br>";
}

// SQL query to create the 'extraction_conversion' table
$createTableSQL = "
    CREATE TABLE extraction_conversion (
        ECID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleID INT(11) NOT NULL,
        FoodTypeID INT(11) NOT NULL,
        ExtractionRate DECIMAL(5,2) NOT NULL,
        AccessedDate DATE NOT NULL,
        Source VARCHAR(255),
        Link VARCHAR(255),
        ProcessToObtainData VARCHAR(255),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'extraction_conversion' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/extraction_conversion.csv';

if (!file_exists($csvFile)) {
    die("Error: CSV file '$csvFile' not found.<br>");
}

echo "<br>Opening CSV file: $csvFile<br>";

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    $header = fgetcsv($handle);
    echo "Header row: " . implode(", ", $header) . "<br>";

    echo "<br>CSV Contents:<br>";
    echo "Row 1 (Header): " . implode(", ", $header) . "<br>";
    
    $rowNumber = 2;
    rewind($handle);
    fgetcsv($handle); // Skip header again
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        echo "Row $rowNumber: " . implode(", ", $data) . "<br>";
        
        $vehicleID = filter_var(trim($data[0]), FILTER_VALIDATE_INT);
        $foodTypeID = filter_var(trim($data[1]), FILTER_VALIDATE_INT);
        $extractionRate = filter_var(trim($data[2]), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $accessedDate = date('Y-m-d', strtotime(trim($data[3])));
        $source = mysqli_real_escape_string($conn, trim($data[4]));
        $link = mysqli_real_escape_string($conn, trim($data[5]));
        $processToObtainData = mysqli_real_escape_string($conn, trim($data[6]));
        
        if ($vehicleID !== false && $foodTypeID !== false && !empty($extractionRate) && !empty($accessedDate)) {
            $sql = "INSERT INTO extraction_conversion (VehicleID, FoodTypeID, ExtractionRate, AccessedDate, Source, Link, ProcessToObtainData) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iisssss", $vehicleID, $foodTypeID, $extractionRate, $accessedDate, $source, $link, $processToObtainData);
            
            if ($stmt->execute()) {
                $ecid = $conn->insert_id;
                echo "✓ Inserted extraction conversion data with ID: $ecid<br>";
            } else {
                echo "Error inserting extraction conversion data: " . $stmt->error . "<br>";
            }
            $stmt->close();
        } else {
            echo "Warning: Invalid data in row $rowNumber<br>";
        }
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final extraction_conversion table contents:<br>";
    $result = $conn->query("SELECT * FROM extraction_conversion ORDER BY ECID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ECID']}, VehicleID: {$row['VehicleID']}, FoodTypeID: {$row['FoodTypeID']}, ExtractionRate: {$row['ExtractionRate']}, AccessedDate: {$row['AccessedDate']}, Source: {$row['Source']}, Link: {$row['Link']}, ProcessToObtainData: {$row['ProcessToObtainData']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php

?>
