<?php

// Include the database connection
include('db_connect.php');

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS raw_crops";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'raw_crops' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Create simplified raw_crops table
$createTableSQL = "
    CREATE TABLE raw_crops (
        RawCropsID INT AUTO_INCREMENT PRIMARY KEY,
        CropName VARCHAR(50)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'raw_crops' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    die();
}

// Read and insert data from CSV
$csvFile = 'raw_crops.csv';
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    fgetcsv($handle);
    
    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO raw_crops (CropName) VALUES (?)");
    
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $cropName = trim($data[0]); // Only take the first column
            
            $stmt->bind_param("s", $cropName);
            
            if ($stmt->execute() === TRUE) {
                echo "Inserted: $cropName<br>";
            } else {
                echo "Error inserting $cropName: " . $stmt->error . "<br>";
            }
        }
        $stmt->close();
    }
    fclose($handle);
} else {
    echo "Error: Could not open file $csvFile<br>";
}

// Verify table contents
$result = $conn->query("SELECT * FROM raw_crops");
if ($result) {
    echo "<br>Raw Crops table contents:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['RawCropsID']}, Crop: {$row['CropName']}<br>";
    }
} else {
    echo "Error verifying table contents: " . $conn->error . "<br>";
}

?>