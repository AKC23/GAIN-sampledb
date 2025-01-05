<?php

// Include the database connection
include('db_connect.php');

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS distributer_name";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'distributer_name' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Create distributer_name table with foreign keys
$createTableSQL = "
    CREATE TABLE distributer_name (
        DistributerID INT AUTO_INCREMENT PRIMARY KEY,
        DistributerName VARCHAR(100),
        VehicleID INT,
        FoodTypeID INT,

        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (FoodTypeID) REFERENCES foodtype(FoodTypeID)

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'distributer_name' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    die();
}

// Read and insert data from CSV
$csvFile = 'data/distrubuter_name.csv';
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    fgetcsv($handle);
    
    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO distributer_name (DistributerName, VehicleID, FoodTypeID) VALUES (?, ?, ?)");
    
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $distributerName = trim($data[0]);
            $vehicleID = (int)trim($data[2]);
            $foodTypeID = (int)trim($data[4]);
            
            $stmt->bind_param("sii", $distributerName, $vehicleID, $foodTypeID);
            
            if ($stmt->execute() === TRUE) {
                echo "Inserted: $distributerName<br>";
            } else {
                echo "Error inserting $distributerName: " . $stmt->error . "<br>";
            }
        }
        $stmt->close();
    }
    fclose($handle);
} else {
    echo "Error: Could not open file $csvFile<br>";
}

// Verify table contents
$result = $conn->query("SELECT * FROM distributer_name");
if ($result) {
    echo "<br>Distributer Name table contents:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['DistributerID']}, Name: {$row['DistributerName']}, VehicleID: {$row['VehicleID']}, FoodTypeID: {$row['FoodTypeID']}<br>";
    }
} else {
    echo "Error verifying table contents: " . $conn->error . "<br>";
}

?>