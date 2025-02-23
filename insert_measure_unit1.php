<?php
// insert_measure_unit1.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'measureunit1' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS measureunit1";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'measureunit1' dropped successfully.<br>";
} else {
    echo "Error dropping table 'measureunit1': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'measureunit1' table
$createTableSQL = "
    CREATE TABLE measureunit1 (
        UCID INT(11) AUTO_INCREMENT PRIMARY KEY,
        SupplyVolumeUnit VARCHAR(50) NOT NULL,
        PeriodicalUnit VARCHAR(50) NOT NULL,
        UnitValue DECIMAL(30, 2) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'measureunit1' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/measure_unit1.csv';  // Update with the exact path of your CSV file

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
        $supplyVolumeUnit = trim($data[0]);
        $periodicalUnit = trim($data[1]);
        $unitValue = trim($data[2]);
        
        // Remove any extra spaces between the name and comma
        $supplyVolumeUnit = preg_replace('/\s+,/', ',', $supplyVolumeUnit);
        $periodicalUnit = preg_replace('/\s+,/', ',', $periodicalUnit);
        
        // Convert to proper types
        $unitValue = filter_var($unitValue, FILTER_VALIDATE_FLOAT);
        if ($unitValue === false || $unitValue === null) {
            echo "Error: Invalid UnitValue format in row $rowNumber: '{$data[2]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $supplyVolumeUnit = mysqli_real_escape_string($conn, $supplyVolumeUnit);
        $periodicalUnit = mysqli_real_escape_string($conn, $periodicalUnit);

        // Debugging: Show extracted values
        echo "SupplyVolumeUnit: '$supplyVolumeUnit'<br>";
        echo "PeriodicalUnit: '$periodicalUnit'<br>";
        echo "UnitValue: $unitValue<br>";

        if (empty($supplyVolumeUnit) || empty($periodicalUnit)) {
            echo "Warning: Empty unit selection in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO measureunit1 (SupplyVolumeUnit, PeriodicalUnit, UnitValue) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $supplyVolumeUnit, $periodicalUnit, $unitValue);

        if ($stmt->execute()) {
            $ucid = $conn->insert_id;
            echo "✓ Inserted measure unit with ID: $ucid<br>";
        } else {
            echo "Error inserting measure unit: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final measureunit1 table contents:<br>";
    $result = $conn->query("SELECT * FROM measureunit1 ORDER BY UCID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['UCID']}, SupplyVolumeUnit: {$row['SupplyVolumeUnit']}, PeriodicalUnit: {$row['PeriodicalUnit']}, UnitValue: {$row['UnitValue']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
