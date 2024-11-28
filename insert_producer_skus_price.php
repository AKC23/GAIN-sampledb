<?php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'Producer_skus' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS Producer_skus";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'Producer_skus' dropped successfully.<br>";
} else {
    echo "Error dropping table 'Producer_skus': " . $conn->error . "<br>";
}

// SQL query to create the 'Producer_skus' table with foreign keys
$createTableSQL = "
    CREATE TABLE Producer_skus (
        SKUSID INT AUTO_INCREMENT PRIMARY KEY,
        ProducerSize VARCHAR(30),
        ProducerID INT, 
        VehicleID INT,
        BrandID INT,
        SKU DECIMAL(10, 1),
        Packaging VARCHAR(20),
        Price DECIMAL(10, 1),
        
        FoodTypeID INT,
        SourceURL VARCHAR(255),
        DateAccessed VARCHAR(20),
        ProcessToObtainData VARCHAR(255),
        
        FOREIGN KEY (ProducerID) REFERENCES producers_supply(ProducerID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'Producer_skus' created successfully.<br>";
} else {
    echo "Error creating table 'Producer_skus': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file
$csvFilePath = 'producer_skus_price.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO Producer_skus (
            ProducerSize, ProducerID, VehicleID, BrandID, SKU, Packaging, Price,
            FoodTypeID, SourceURL, DateAccessed, ProcessToObtainData
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Bind parameters with each column of the CSV data
            $stmt->bind_param(
                "siiisssssss",
                $row[0], // ProducerSize
                $row[2], // ProducerID
                $row[4], // VehicleID
                $row[6], // BrandID
                $row[7], // SKU
                $row[8], // Packaging
                $row[9], // Price
                $row[11], // FoodTypeID
                $row[12], // SourceURL
                $row[13], // DateAccessed
                $row[14]  // ProcessToObtainData
            );

            // Execute the query and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for SKU: " . $row[4] . "<br>";
            } else {
                echo "Error inserting data for SKU: " . $row[4] . " - " . $stmt->error . "<br>";
            }
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the file after reading
    fclose($handle);
} else {
    echo "Error: Could not open CSV file.";
}

// Close the database connection
$conn->close();
?>
