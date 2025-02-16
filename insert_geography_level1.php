<?php
// insert_geography_level1.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'geographylevel1' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS geographylevel1";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'geographylevel1' dropped successfully.<br>";
} else {
    echo "Error dropping table 'geographylevel1': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'geographylevel1' table with a foreign key to 'country'
$createTableSQL = "
    CREATE TABLE geographylevel1 (
        GL1ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        AdminLevel1 VARCHAR(50) NOT NULL,
        CountryID INT(11) NOT NULL,
        FOREIGN KEY (CountryID) REFERENCES country(CountryID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'geographylevel1' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid CountryIDs
$validCountryIDs = array();
$result = $conn->query("SELECT * FROM country");
if ($result) {
    echo "<br>Valid CountryIDs in database:<br>";
    while ($row = $result->fetch_assoc()) {
        $validCountryIDs[] = $row['CountryID'];
        echo "CountryID: {$row['CountryID']}, Name: {$row['CountryName']}<br>";
    }
} else {
    echo "Error getting valid CountryIDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/geography_level1.csv';  // Update with the exact path of your CSV file

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
        $adminLevel1 = trim($data[0]);
        $countryID = trim($data[1]);
        
        // Remove any extra spaces between the name and comma
        $adminLevel1 = preg_replace('/\s+,/', ',', $adminLevel1);
        
        // Convert to proper types
        $countryID = filter_var($countryID, FILTER_VALIDATE_INT);
        if ($countryID === false || $countryID === null) {
            echo "Error: Invalid CountryID format in row $rowNumber: '{$data[1]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $adminLevel1 = mysqli_real_escape_string($conn, $adminLevel1);

        // Debugging: Show extracted values
        echo "CountryID from CSV: $countryID (Valid IDs: " . implode(", ", $validCountryIDs) . ")<br>";
        echo "AdminLevel1: '$adminLevel1'<br>";

        // Validate CountryID
        if (!in_array($countryID, $validCountryIDs)) {
            echo "Error: CountryID $countryID does not exist in country table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        if (empty($adminLevel1)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO geographylevel1 (AdminLevel1, CountryID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $adminLevel1, $countryID);

        if ($stmt->execute()) {
            $gl1id = $conn->insert_id;
            echo "✓ Inserted geography level 1 '$adminLevel1' with ID: $gl1id<br>";
        } else {
            echo "Error inserting geography level 1: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final geographylevel1 table contents:<br>";
    $result = $conn->query("SELECT gl1.*, c.CountryName 
                           FROM geographylevel1 gl1 
                           JOIN country c ON gl1.CountryID = c.CountryID 
                           ORDER BY gl1.GL1ID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['GL1ID']}, AdminLevel1: {$row['AdminLevel1']}, CountryID: {$row['CountryID']}, Country: {$row['CountryName']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
