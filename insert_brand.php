<?php

// Include the database connection
include('db_connect.php');

// Ensure referenced tables exist
$requiredTables = ['company', 'FoodType'];
foreach ($requiredTables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        die("Error: Referenced table '$table' does not exist.<br>");
    }
}

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
        Brand_Name VARCHAR(100),
        CompanyID INT,
        FoodTypeID INT,
        FOREIGN KEY (CompanyID) REFERENCES company(CompanyID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID)
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
    $stmt = $conn->prepare("INSERT INTO brand (Brand_Name, CompanyID, FoodTypeID) VALUES (?, ?, ?)");
    
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $brandName = trim($data[0]);
            $companyID = (int)trim($data[2]);
            $foodTypeID = (int)trim($data[4]);
            
            $stmt->bind_param("sii", $brandName, $companyID, $foodTypeID);
            
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
        echo "ID: {$row['BrandID']}, Name: {$row['Brand_Name']}, Company ID: {$row['CompanyID']}, Food Type ID: {$row['FoodTypeID']}<br>";
    }
} else {
    echo "Error verifying table contents: " . $conn->error . "<br>";
}

?>