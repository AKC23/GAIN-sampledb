<?php

// Include the database connection
include('db_connect.php');

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS brand";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'brand' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Create brand table
$createTableSQL = "
    CREATE TABLE brand (
        BrandID INT AUTO_INCREMENT PRIMARY KEY,
        BrandName VARCHAR(100)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'brand' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    die();
}

// Read and insert data from CSV
$csvFile = 'data/brand.csv';
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    fgetcsv($handle);
    
    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO brand (BrandName) VALUES (?)");
    
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $brandName = trim($data[0]);
            
            $stmt->bind_param("s", $brandName);
            
            if ($stmt->execute() === TRUE) {
                echo "Inserted: $brandName<br>";
            } else {
                echo "Error inserting $brandName: " . $stmt->error . "<br>";
            }
        }
        $stmt->close();
    }
    fclose($handle);
} else {
    echo "Error: Could not open file $csvFile<br>";
}

// Verify table contents
$result = $conn->query("SELECT * FROM brand");
if ($result) {
    echo "<br>Brand table contents:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['BrandID']}, Name: {$row['BrandName']}<br>";
    }
} else {
    echo "Error verifying table contents: " . $conn->error . "<br>";
}

?>