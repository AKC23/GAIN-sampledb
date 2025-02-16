<?php
// insert_foodtype.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS foodtype";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'foodtype' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'foodtype' table
$createTableSQL = "
    CREATE TABLE foodtype (
        FoodTypeID INT(11) AUTO_INCREMENT PRIMARY KEY,
        FoodTypeName VARCHAR(100) NOT NULL,
        VehicleID INT(11),
        FOREIGN KEY (VehicleID) REFERENCES foodvehicle(VehicleID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'foodtype' created successfully.<br>";
} else {
    echo "Error creating table 'foodtype': " . $conn->error . "<br>";
}

// Get valid VehicleIDs
$validVehicleIDs = array();
$result = $conn->query("SELECT * FROM foodvehicle");
if ($result) {
    echo "<br>Valid VehicleIDs in database:<br>";
    while ($row = $result->fetch_assoc()) {
        $validVehicleIDs[] = $row['VehicleID'];
        echo "VehicleID: {$row['VehicleID']}, Name: {$row['VehicleName']}<br>";
    }
} else {
    echo "Error getting valid VehicleIDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/foodtype.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 2) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $foodTypeName = trim($data[0]);
        $vehicleID = trim($data[1]);
        
        // Remove any extra spaces between the name and comma
        $foodTypeName = preg_replace('/\s+,/', ',', $foodTypeName);
        
        // Convert to proper types
        $vehicleID = filter_var($vehicleID, FILTER_VALIDATE_INT);
        if ($vehicleID === false || $vehicleID === null) {
            echo "Error: Invalid VehicleID format in row $rowNumber: '{$data[1]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $foodTypeName = mysqli_real_escape_string($conn, $foodTypeName);

        // Debugging: Show extracted values
        echo "VehicleID from CSV: $vehicleID (Valid IDs: " . implode(", ", $validVehicleIDs) . ")<br>";
        echo "FoodTypeName: '$foodTypeName'<br>";

        // Validate VehicleID
        if (!in_array($vehicleID, $validVehicleIDs)) {
            echo "Error: VehicleID $vehicleID does not exist in foodvehicle table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        if (empty($foodTypeName)) {
            echo "Warning: Empty food type name in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO foodtype (FoodTypeName, VehicleID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $foodTypeName, $vehicleID);

        if ($stmt->execute()) {
            $foodTypeID = $conn->insert_id;
            echo "✓ Inserted food type '$foodTypeName' with ID: $foodTypeID<br>";
        } else {
            echo "Error inserting food type: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final foodtype table contents:<br>";
    $result = $conn->query("SELECT ft.*, fv.VehicleName 
                           FROM foodtype ft 
                           JOIN foodvehicle fv ON ft.VehicleID = fv.VehicleID 
                           ORDER BY ft.FoodTypeID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['FoodTypeID']}, VehicleID: {$row['VehicleID']}, " .
                 "Vehicle: {$row['VehicleName']}, Type: {$row['FoodTypeName']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
