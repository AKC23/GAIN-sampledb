<?php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'reference' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS reference";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'reference' dropped successfully.<br>";
} else {
    echo "Error dropping table 'reference': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'reference' table
$createTableSQL = "
    CREATE TABLE reference (
        ReferenceID INT(11) AUTO_INCREMENT PRIMARY KEY,
        ReferenceNumber INT(11) NOT NULL,
        Source VARCHAR(255) NOT NULL,
        Link VARCHAR(255),
        ProcessToObtainData VARCHAR(255),
        AccessDate DATE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'reference' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/reference.csv';  // Update with the exact path of your CSV file

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
        if (count($data) < 5) {
            echo "Warning: Row $rowNumber has insufficient columns. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Clean the data more thoroughly
        $referenceNumber = trim($data[0]);
        $source = trim($data[1]);
        $link = trim($data[2]);
        $processToObtainData = trim($data[3]);
        $accessDate = trim($data[4]);
        
        // Remove any extra spaces between the name and comma
        $source = preg_replace('/\s+,/', ',', $source);
        $link = preg_replace('/\s+,/', ',', $link);
        $processToObtainData = preg_replace('/\s+,/', ',', $processToObtainData);
        
        // Convert to proper types
        $referenceNumber = filter_var($referenceNumber, FILTER_VALIDATE_INT);
        if ($referenceNumber === false || $referenceNumber === null) {
            echo "Error: Invalid Reference Number format in row $rowNumber: '{$data[0]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $source = mysqli_real_escape_string($conn, $source);
        $link = mysqli_real_escape_string($conn, $link);
        $processToObtainData = mysqli_real_escape_string($conn, $processToObtainData);
        $accessDate = mysqli_real_escape_string($conn, $accessDate);

        // Debugging: Show extracted values
        echo "Reference Number: $referenceNumber, Source: '$source', Link: '$link', Process to Obtain Data: '$processToObtainData', Access Date: '$accessDate'<br>";

        if (empty($source) || empty($processToObtainData) || empty($accessDate)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO reference (ReferenceNumber, Source, Link, ProcessToObtainData, AccessDate) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $referenceNumber, $source, $link, $processToObtainData, $accessDate);

        if ($stmt->execute()) {
            $referenceID = $conn->insert_id;
            echo "✓ Inserted reference '$source' with ID: $referenceID<br>";
        } else {
            echo "Error inserting reference: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final reference table contents:<br>";
    $result = $conn->query("SELECT * FROM reference ORDER BY ReferenceID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ReferenceID']}, Reference Number: {$row['ReferenceNumber']}, Source: {$row['Source']}, Link: {$row['Link']}, Process to Obtain Data: {$row['ProcessToObtainData']}, Access Date: {$row['AccessDate']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
