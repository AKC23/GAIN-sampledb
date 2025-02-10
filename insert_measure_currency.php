<?php
// insert_measure_currency.php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'measure_currency' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS measure_currency";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'measure_currency' dropped successfully.<br>";
} else {
    echo "Error dropping table 'measure_currency': " . $conn->error . "<br>";
}

// SQL query to create the 'measure_currency' table
$createTableSQL = "
    CREATE TABLE measure_currency (
        CurrencyID INT(11) AUTO_INCREMENT PRIMARY KEY,
        CurrencySelection VARCHAR(50) NOT NULL,
        CurrencyValue FLOAT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'measure_currency' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/measure_currency.csv';  // Update with the exact path of your CSV file

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
        $currencySelection = trim($data[0]);
        $currencyValue = trim($data[1]);
        
        // Remove any extra spaces between the name and comma
        $currencySelection = preg_replace('/\s+,/', ',', $currencySelection);
        
        // Convert to proper types
        $currencyValue = filter_var($currencyValue, FILTER_VALIDATE_FLOAT);
        if ($currencyValue === false || $currencyValue === null) {
            echo "Error: Invalid CurrencyValue format in row $rowNumber: '{$data[1]}'. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $currencySelection = mysqli_real_escape_string($conn, $currencySelection);

        // Debugging: Show extracted values
        echo "CurrencySelection: '$currencySelection'<br>";
        echo "CurrencyValue: $currencyValue<br>";

        if (empty($currencySelection)) {
            echo "Warning: Empty currency selection in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO measure_currency (CurrencySelection, CurrencyValue) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sd", $currencySelection, $currencyValue);

        if ($stmt->execute()) {
            $currencyID = $conn->insert_id;
            echo "✓ Inserted measure currency '$currencySelection' with ID: $currencyID<br>";
        } else {
            echo "Error inserting measure currency: " . $stmt->error . "<br>";
        }

        $stmt->close();
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final measure_currency table contents:<br>";
    $result = $conn->query("SELECT * FROM measure_currency ORDER BY CurrencyID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['CurrencyID']}, CurrencySelection: {$row['CurrencySelection']}, CurrencyValue: {$row['CurrencyValue']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: We do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
