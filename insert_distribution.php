<?php
// insert_distribution.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop the 'distribution' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS distribution";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'distribution' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'distribution' table
$createDistributionTableSQL = "
    CREATE TABLE distribution (
        DistributionID INT(11) AUTO_INCREMENT PRIMARY KEY,
        DistributionChannelID INT(11),
        SubDistributionChannelID INT(11),
        VehicleID INT(11),
        CountryID INT(11),
        StartYear INT(4),
        DistributedVolume DECIMAL(20,6),
        FOREIGN KEY (DistributionChannelID) REFERENCES distributionchannel(DistributionChannelID),
        FOREIGN KEY (SubDistributionChannelID) REFERENCES subdistributionchannel(SubDistributionChannelID),
        FOREIGN KEY (VehicleID) REFERENCES foodvehicle(VehicleID),
        FOREIGN KEY (CountryID) REFERENCES country(CountryID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createDistributionTableSQL) === TRUE) {
    echo "Table 'distribution' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid DistributionChannelIDs and SubDistributionChannelIDs
$validDistributionChannelIDs = array();
$result = $conn->query("SELECT DistributionChannelID FROM distributionchannel");
if ($result) {
    echo "<br>Valid DistributionChannelIDs in database:<br>";
    while ($row = $result->fetch_assoc()) {
        $validDistributionChannelIDs[] = $row['DistributionChannelID'];
        echo "DistributionChannelID: {$row['DistributionChannelID']}<br>";
    }
} else {
    echo "Error getting valid DistributionChannelIDs: " . $conn->error . "<br>";
}

$validSubDistributionChannelIDs = array();
$result = $conn->query("SELECT SubDistributionChannelID FROM subdistributionchannel");
if ($result) {
    echo "<br>Valid SubDistributionChannelIDs in database:<br>";
    while ($row = $result->fetch_assoc()) {
        $validSubDistributionChannelIDs[] = $row['SubDistributionChannelID'];
        echo "SubDistributionChannelID: {$row['SubDistributionChannelID']}<br>";
    }
} else {
    echo "Error getting valid SubDistributionChannelIDs: " . $conn->error . "<br>";
}

// Get all VehicleIDs and CountryIDs from the supply table
$vehicleCountryPairs = array();
$result = $conn->query("SELECT DISTINCT VehicleID, CountryID FROM supply");
if ($result) {
    echo "<br>Valid VehicleID and CountryID pairs in supply table:<br>";
    while ($row = $result->fetch_assoc()) {
        $vehicleCountryPairs[] = array('VehicleID' => $row['VehicleID'], 'CountryID' => $row['CountryID']);
        echo "VehicleID: {$row['VehicleID']}, CountryID: {$row['CountryID']}<br>";
    }
} else {
    echo "Error getting valid VehicleID and CountryID pairs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/distribution.csv';

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
$bom = pack('H*', 'EFBBBF');
if (strncmp($content, $bom, 3) === 0) {
    echo "Found and removing UTF-8 BOM from CSV file.<br>";
    $content = substr($content, 3);
}

// Normalize line endings
$content = str_replace("\r\n", "\n", $content);
$content = str_replace("\r", "\n", $content);
$lines = explode("\n", $content);

// Remove any empty lines
$lines = array_filter($lines, function ($line) {
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
        $distributionChannelID = (int)trim($data[0]);
        $subDistributionChannelID = (int)trim($data[2]);

        // Debugging: Show extracted values
        echo "DistributionChannelID: $distributionChannelID<br>";
        echo "SubDistributionChannelID: $subDistributionChannelID<br>";

        // Validate DistributionChannelID and SubDistributionChannelID
        if (!in_array($distributionChannelID, $validDistributionChannelIDs)) {
            echo "Error: DistributionChannelID $distributionChannelID does not exist in distributionchannel table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        if (!in_array($subDistributionChannelID, $validSubDistributionChannelIDs)) {
            echo "Error: SubDistributionChannelID $subDistributionChannelID does not exist in subdistributionchannel table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        // Insert data into distribution table for all combinations of VehicleID and CountryID from the supply table
        foreach ($vehicleCountryPairs as $pair) {
            $vehicleID = $pair['VehicleID'];
            $countryID = $pair['CountryID'];
            $sql = "INSERT INTO distribution (DistributionChannelID, SubDistributionChannelID, VehicleID, CountryID) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iiii", $distributionChannelID, $subDistributionChannelID, $vehicleID, $countryID);

            if ($stmt->execute()) {
                $distributionID = $conn->insert_id;
                echo "✓ Inserted distribution with ID: $distributionID<br>";
            } else {
                echo "Error inserting distribution: " . $stmt->error . "<br>";
            }

            $stmt->close();
        }
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final distribution table contents:<br>";
    $result = $conn->query("SELECT * FROM distribution ORDER BY DistributionID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['DistributionID']}, DistributionChannelID: {$row['DistributionChannelID']}, SubDistributionChannelID: {$row['SubDistributionChannelID']}, VehicleID: {$row['VehicleID']}, CountryID: {$row['CountryID']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Insert data into distribution table from supply table
$insertDistributionSQL = "
    WITH SubDistributionCounts AS (
        SELECT 
            DistributionChannelID, 
            CountryID,
            VehicleID,
            COUNT(DISTINCT CASE WHEN SubDistributionChannelID > 1 THEN SubDistributionChannelID END) AS TotalSubSpecific
        FROM distribution
        WHERE DistributionChannelID > 1
        GROUP BY DistributionChannelID, CountryID, VehicleID
    ),
    TotalSubDistribution AS (
        SELECT 
            CountryID,
            VehicleID,
            COUNT(DISTINCT CASE WHEN SubDistributionChannelID > 1 THEN SubDistributionChannelID END) AS TotalSubAll
        FROM distribution
        WHERE DistributionChannelID > 1
        GROUP BY CountryID, VehicleID
    )
    SELECT 
        d.DistributionChannelID,
        d.SubDistributionChannelID,
        d.VehicleID,
        d.CountryID,
        s.StartYear,
        SUM(s.SourceVolume * (sc.TotalSubSpecific / NULLIF(ts.TotalSubAll, 0))) AS DistributedVolume
    FROM supply s
    JOIN distribution d ON s.VehicleID = d.VehicleID AND s.CountryID = d.CountryID
    JOIN SubDistributionCounts sc 
        ON d.DistributionChannelID = sc.DistributionChannelID 
        AND d.CountryID = sc.CountryID 
        AND d.VehicleID = sc.VehicleID
    JOIN TotalSubDistribution ts 
        ON d.CountryID = ts.CountryID 
        AND d.VehicleID = ts.VehicleID
    WHERE d.DistributionChannelID > 1
    AND d.SubDistributionChannelID > 1
    AND s.StartYear IS NOT NULL AND s.StartYear != ''
    GROUP BY 
        d.DistributionChannelID, 
        d.SubDistributionChannelID, 
        d.VehicleID, 
        d.CountryID, 
        s.StartYear
    HAVING 
        DistributedVolume IS NOT NULL AND DistributedVolume != ''";

$finalInsertSQL = "
    INSERT INTO distribution (DistributionChannelID, SubDistributionChannelID, VehicleID, CountryID, StartYear, DistributedVolume)
    $insertDistributionSQL
    ON DUPLICATE KEY UPDATE
        StartYear = VALUES(StartYear),
        DistributedVolume = VALUES(DistributedVolume)";

if ($conn->query($finalInsertSQL) === TRUE) {
    echo "Data inserted into 'distribution' table successfully.<br>";
} else {
    echo "Error inserting data into 'distribution' table: " . $conn->error . "<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
