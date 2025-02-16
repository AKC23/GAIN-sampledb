<?php
// insert_foodvehicle.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'foodvehicle' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS foodvehicle";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'foodvehicle' dropped successfully.<br>";
} else {
    echo "Error dropping table 'foodvehicle': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'foodvehicle' table
$createTableSQL = "
    CREATE TABLE foodvehicle (
        VehicleID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleName VARCHAR(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'foodvehicle' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/foodvehicle.csv';

if (!file_exists($csvFile)) {
    die("Error: CSV file '$csvFile' not found.<br>");
}

echo "<br>Opening CSV file: $csvFile<br>";

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    $header = fgetcsv($handle);
    echo "Header row: " . implode(", ", $header) . "<br>";

    echo "<br>CSV Contents:<br>";
    echo "Row 1 (Header): " . implode(", ", $header) . "<br>";
    
    $rowNumber = 2;
    rewind($handle);
    fgetcsv($handle); // Skip header again
    
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        echo "Row $rowNumber: " . implode(", ", $data) . "<br>";
        
        $vehicleName = mysqli_real_escape_string($conn, trim($data[0]));
        
        if (!empty($vehicleName)) {
            $sql = "INSERT INTO foodvehicle (VehicleName) VALUES (?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $vehicleName);
            
            if ($stmt->execute()) {
                $lastId = $conn->insert_id;
                echo "✓ Inserted '$vehicleName' with ID: $lastId<br>";
            } else {
                echo "Error inserting '$vehicleName': " . $stmt->error . "<br>";
            }
            $stmt->close();
        } else {
            echo "Warning: Empty vehicle name in row $rowNumber<br>";
        }
        $rowNumber++;
    }

    // After inserting, show what's in the table
    echo "<br>Final foodvehicle table contents:<br>";
    $result = $conn->query("SELECT * FROM foodvehicle ORDER BY VehicleID");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['VehicleID']}, Name: {$row['VehicleName']}<br>";
        }
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php

?>
