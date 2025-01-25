<?php
// insert_geography.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'Geography' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS Geography";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'Geography' dropped successfully.<br>";
} else {
    echo "Error dropping table 'Geography': " . $conn->error . "<br>";
}

// SQL query to create the 'Geography' table with a foreign key to 'country'
$createTableSQL = "
    CREATE TABLE Geography (
        GeographyID INT(11) AUTO_INCREMENT PRIMARY KEY,
        AdminLevel1 VARCHAR(50) NOT NULL,
        AdminLevel2 VARCHAR(50) NOT NULL,
        AdminLevel3 VARCHAR(50) NOT NULL,
        CountryID INT(11) NOT NULL,
        FOREIGN KEY (CountryID) REFERENCES country(Country_ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'Geography' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid Country_IDs
$validCountryIDs = array();
$result = $conn->query("SELECT * FROM country");
if ($result) {
    echo "<br>Valid Country_IDs in database:<br>";
    while ($row = $result->fetch_assoc()) {
        $validCountryIDs[] = $row['Country_ID'];
        echo "Country_ID: {$row['Country_ID']}, Name: {$row['Country_Name']}<br>";
    }
} else {
    echo "Error getting valid Country_IDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/geography.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 4) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $division = trim($data[0]);
        $district = trim($data[1]);
        $cityCorporation = trim($data[2]);
        $countryID = trim($data[4]);
        
        // Remove any extra spaces between the name and comma
        $division = preg_replace('/\s+,/', ',', $division);
        $district = preg_replace('/\s+,/', ',', $district);
        $cityCorporation = preg_replace('/\s+,/', ',', $cityCorporation);
        
        // Convert to proper types
        $countryID = filter_var($countryID, FILTER_VALIDATE_INT);
        if ($countryID === false || $countryID === null) {
            echo "Error: Invalid Country_ID format in row $rowNumber: '{$data[3]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $division = mysqli_real_escape_string($conn, $division);
        $district = mysqli_real_escape_string($conn, $district);
        $cityCorporation = mysqli_real_escape_string($conn, $cityCorporation);

        // Debugging: Show extracted values
        echo "Country_ID from CSV: $countryID (Valid IDs: " . implode(", ", $validCountryIDs) . ")<br>";
        echo "Division: '$division', District: '$district', City Corporation: '$cityCorporation'<br>";

        // Validate Country_ID
        if (!in_array($countryID, $validCountryIDs)) {
            echo "Error: Country_ID $countryID does not exist in country table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        if (empty($division) || empty($district) || empty($cityCorporation)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO Geography (AdminLevel1, AdminLevel2, AdminLevel3, CountryID) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $division, $district, $cityCorporation, $countryID);

        if ($stmt->execute()) {
            $geographyID = $conn->insert_id;
            echo "✓ Inserted geography '$division, $district, $cityCorporation' with ID: $geographyID<br>";
        } else {
            echo "Error inserting geography: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final Geography table contents:<br>";
    $result = $conn->query("SELECT g.*, c.Country_Name 
                           FROM Geography g 
                           JOIN country c ON g.CountryID = c.Country_ID 
                           ORDER BY g.GeographyID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['GeographyID']}, Division: {$row['AdminLevel1']}, District: {$row['AdminLevel2']}, " .
                 "City Corporation: {$row['AdminLevel3']}, CountryID: {$row['CountryID']}, Country: {$row['Country_Name']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
