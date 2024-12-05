
<?php

// Include the database connection
include('db_connect.php');

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS distributer_brand";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'distributer_brand' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Create distributer_brand table
$createTableSQL = "
    CREATE TABLE distributer_brand (
        Distributer_Brand_ID INT AUTO_INCREMENT PRIMARY KEY,
        Distributer_Brand_Name VARCHAR(100),
        Brand_Product VARCHAR(100),
        Distributer_ID INT,
        FOREIGN KEY (Distributer_ID) REFERENCES distributer_name(DistributerID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'distributer_brand' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
    die();
}

// Read and insert data from CSV
$csvFile = 'distributer_brand.csv';
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    fgetcsv($handle);
    
    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO distributer_brand (Distributer_Brand_Name, Brand_Product, Distributer_ID) VALUES (?, ?, ?)");
    
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $distributerBrandName = trim($data[0]);
            $brandProduct = trim($data[1]);
            $distributerID = (int)trim($data[3]);
            
            $stmt->bind_param("ssi", $distributerBrandName, $brandProduct, $distributerID);
            
            if ($stmt->execute() === TRUE) {
                echo "Inserted: $distributerBrandName<br>";
            } else {
                echo "Error inserting $distributerBrandName: " . $stmt->error . "<br>";
            }
        }
        $stmt->close();
    }
    fclose($handle);
} else {
    echo "Error: Could not open file $csvFile<br>";
}

// Verify table contents
$result = $conn->query("SELECT * FROM distributer_brand");
if ($result) {
    echo "<br>Distributer Brand table contents:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['Distributer_Brand_ID']}, Name: {$row['Distributer_Brand_Name']}, Product: {$row['Brand_Product']}, Distributer ID: {$row['Distributer_ID']}<br>";
    }
} else {
    echo "Error verifying table contents: " . $conn->error . "<br>";
}

?>