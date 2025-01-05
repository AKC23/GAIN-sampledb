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
        Zone VARCHAR(50) NOT NULL,
        Region VARCHAR(50) NOT NULL,
        City VARCHAR(50) NOT NULL,
        Country_ID INT(11) NOT NULL,
        FOREIGN KEY (Country_ID) REFERENCES country(Country_ID)
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
        $zone = trim($data[0]);
        $region = trim($data[1]);
        $city = trim($data[2]);
        $countryID = trim($data[3]);
        
        // Remove any extra spaces between the name and comma
        $zone = preg_replace('/\s+,/', ',', $zone);
        $region = preg_replace('/\s+,/', ',', $region);
        $city = preg_replace('/\s+,/', ',', $city);
        
        // Convert to proper types
        $countryID = filter_var($countryID, FILTER_VALIDATE_INT);
        if ($countryID === false || $countryID === null) {
            echo "Error: Invalid Country_ID format in row $rowNumber: '{$data[3]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $zone = mysqli_real_escape_string($conn, $zone);
        $region = mysqli_real_escape_string($conn, $region);
        $city = mysqli_real_escape_string($conn, $city);

        // Debugging: Show extracted values
        echo "Country_ID from CSV: $countryID (Valid IDs: " . implode(", ", $validCountryIDs) . ")<br>";
        echo "Zone: '$zone', Region: '$region', City: '$city'<br>";

        // Validate Country_ID
        if (!in_array($countryID, $validCountryIDs)) {
            echo "Error: Country_ID $countryID does not exist in country table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        if (empty($zone) || empty($region) || empty($city)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO Geography (Zone, Region, City, Country_ID) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $zone, $region, $city, $countryID);

        if ($stmt->execute()) {
            $geographyID = $conn->insert_id;
            echo "✓ Inserted geography '$zone, $region, $city' with ID: $geographyID<br>";
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
                           JOIN country c ON g.Country_ID = c.Country_ID 
                           ORDER BY g.GeographyID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['GeographyID']}, Zone: {$row['Zone']}, Region: {$row['Region']}, " .
                 "City: {$row['City']}, Country_ID: {$row['Country_ID']}, Country: {$row['Country_Name']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
