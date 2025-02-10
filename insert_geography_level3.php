<?php
// insert_geography_level3.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'Geography_Level3' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS Geography_Level3";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'Geography_Level3' dropped successfully.<br>";
} else {
    echo "Error dropping table 'Geography_Level3': " . $conn->error . "<br>";
}

// SQL query to create the 'Geography_Level3' table with a foreign key to 'Geography_Level1'
$createTableSQL = "
    CREATE TABLE Geography_Level3 (
        GL3ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        AdminLevel3 VARCHAR(50) NOT NULL,
        GL1ID INT(11) NOT NULL,
        FOREIGN KEY (GL1ID) REFERENCES Geography_Level1(GL1ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'Geography_Level3' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid GL1IDs
$validGL1IDs = array();
$result = $conn->query("SELECT * FROM Geography_Level1");
if ($result) {
    echo "<br>Valid GL1IDs in database:<br>";
    while ($row = $result->fetch_assoc()) {
        $validGL1IDs[] = $row['GL1ID'];
        echo "GL1ID: {$row['GL1ID']}, AdminLevel1: {$row['AdminLevel1']}<br>";
    }
} else {
    echo "Error getting valid GL1IDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/geography_level3.csv';  // Update with the exact path of your CSV file

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
        $adminLevel3 = trim($data[0]);
        $gl1id = trim($data[1]);
        
        // Remove any extra spaces between the name and comma
        $adminLevel3 = preg_replace('/\s+,/', ',', $adminLevel3);
        
        // Convert to proper types
        $gl1id = filter_var($gl1id, FILTER_VALIDATE_INT);
        if ($gl1id === false || $gl1id === null) {
            echo "Error: Invalid GL1ID format in row $rowNumber: '{$data[1]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $adminLevel3 = mysqli_real_escape_string($conn, $adminLevel3);

        // Debugging: Show extracted values
        echo "GL1ID from CSV: $gl1id (Valid IDs: " . implode(", ", $validGL1IDs) . ")<br>";
        echo "AdminLevel3: '$adminLevel3'<br>";

        // Validate GL1ID
        if (!in_array($gl1id, $validGL1IDs)) {
            echo "Error: GL1ID $gl1id does not exist in Geography_Level1 table. Skipping row.<br>";
            $rowNumber++;
            continue;
        }

        if (empty($adminLevel3)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO Geography_Level3 (AdminLevel3, GL1ID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $adminLevel3, $gl1id);

        if ($stmt->execute()) {
            $gl3id = $conn->insert_id;
            echo "✓ Inserted geography level 3 '$adminLevel3' with ID: $gl3id<br>";
        } else {
            echo "Error inserting geography level 3: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final Geography_Level3 table contents:<br>";
    $result = $conn->query("SELECT gl3.*, gl1.AdminLevel1 
                           FROM Geography_Level3 gl3 
                           JOIN Geography_Level1 gl1 ON gl3.GL1ID = gl1.GL1ID 
                           ORDER BY gl3.GL3ID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['GL3ID']}, AdminLevel3: {$row['AdminLevel3']}, GL1ID: {$row['GL1ID']}, AdminLevel1: {$row['AdminLevel1']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
