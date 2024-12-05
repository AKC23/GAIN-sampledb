<?php

// Include the database connection
include('db_connect.php');

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS distributer_list";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'distributer_list' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Create distributer_list table with foreign keys
$createTableSQL = "
    CREATE TABLE distributer_list (
        Distributer_List_ID INT AUTO_INCREMENT PRIMARY KEY,
        FoodTypeID INT(11),
        Distributer_ID INT(11),
        Volume DECIMAL(10, 2),
        Unit VARCHAR(50),
        Accessed_Date DATE,
        Source VARCHAR(255),
        Link VARCHAR(255),
        FOREIGN KEY (FoodTypeID) REFERENCES foodtype(FoodTypeID),
        
        FOREIGN KEY (Distributer_ID) REFERENCES distributer_name(DistributerID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'distributer_list' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    die();
}

// Read and insert data from CSV
$csvFile = 'distributer_list.csv';
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    fgetcsv($handle);
    
    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO distributer_list (FoodTypeID, Distributer_ID, Volume, Unit, Accessed_Date, Source, Link) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $foodTypeID = (int)trim($data[1]);
            $distributerID = (int)trim($data[3]);
            $volume = (float)trim($data[4]);
            $unit = trim($data[5]);
            $accessedDate = trim($data[6]);
            $source = trim($data[7]);
            $link = trim($data[8]);
            
            $stmt->bind_param("iisssss", $foodTypeID, $distributerID, $volume, $unit, $accessedDate, $source, $link);
            
            if ($stmt->execute() === TRUE) {
                echo "Inserted: FoodTypeID $foodTypeID, DistributerID $distributerID<br>";
            } else {
                echo "Error inserting FoodTypeID $foodTypeID, DistributerID $distributerID: " . $stmt->error . "<br>";
            }
        }
        $stmt->close();
    }
    fclose($handle);
} else {
    echo "Error: Could not open file $csvFile<br>";
}

// Verify table contents
$result = $conn->query("SELECT * FROM distributer_list");
if ($result) {
    echo "<br>Distributer List table contents:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['Distributer_List_ID']}, FoodTypeID: {$row['FoodTypeID']}, DistributerID: {$row['Distributer_ID']}, Volume: {$row['Volume']}, Unit: {$row['Unit']}, Accessed Date: {$row['Accessed_Date']}, Source: {$row['Source']}, Link: {$row['Link']}<br>";
    }
} else {
    echo "Error verifying table contents: " . $conn->error . "<br>";
}

?>