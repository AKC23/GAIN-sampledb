<?php
// Include the database connection
include('db_connect.php');

// SQL query to drop the 'table1' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS table1";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'table1' dropped successfully.<br>";
} else {
    echo "Error dropping table 'table1': " . $conn->error . "<br>";
}

// SQL query to create the 'table1' table with a foreign key
$createTableSQL = "
    CREATE TABLE table1 (
        ItemID INT(11) AUTO_INCREMENT PRIMARY KEY,
        ItemName VARCHAR(255) NOT NULL,
        ReferenceID INT(11) NOT NULL,
        FOREIGN KEY (ReferenceID) REFERENCES table2(ReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'table1' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'table1.csv';  // Update with the exact path of your CSV file

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
        $itemName = trim($data[0]);
        $referenceID = trim($data[1]);
        
        // Remove any extra spaces between the name and comma
        $itemName = preg_replace('/\s+,/', ',', $itemName);
        
        // Convert to proper types
        $referenceID = filter_var($referenceID, FILTER_VALIDATE_INT);
        if ($referenceID === false || $referenceID === null) {
            echo "Error: Invalid ReferenceID format in row $rowNumber: '{$data[1]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $itemName = mysqli_real_escape_string($conn, $itemName);

        // Debugging: Show extracted values
        echo "ItemName: '$itemName', ReferenceID: $referenceID<br>";

        if (empty($itemName)) {
            echo "Warning: Empty fields in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO table1 (ItemName, ReferenceID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $itemName, $referenceID);

        if ($stmt->execute()) {
            $itemID = $conn->insert_id;
            echo "✓ Inserted item '$itemName' with ID: $itemID<br>";
        } else {
            echo "Error inserting item: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final table1 contents:<br>";
    $result = $conn->query("SELECT * FROM table1 ORDER BY ItemID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['ItemID']}, ItemName: {$row['ItemName']}, ReferenceID: <a href='view_reference.php?reference_id=" . htmlspecialchars($row['ReferenceID']) . "' target='_blank'>" . htmlspecialchars($row['ReferenceID']) . "</a><br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
