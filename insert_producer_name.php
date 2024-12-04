<?php

// Include the database connection
include('db_connect.php');

// SQL query to drop the 'producer_name' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producer_name";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producer_name' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producer_name': " . $conn->error . "<br>";
}

// Ensure referenced tables exist
$requiredTables = ['Country', 'FoodVehicle'];
foreach ($requiredTables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        die("Error: Required table '$table' does not exist.<br>");
    }
}

// SQL query to create the 'producer_name' table with foreign keys
$createTableSQL = "
    CREATE TABLE producer_name (
        ProducersID INT AUTO_INCREMENT PRIMARY KEY,
        ProducersName VARCHAR(255),
        Location VARCHAR(255),
        CountryID INT(11),
        ProducerSize VARCHAR(50),
        VehicleID INT(11),
        
        FOREIGN KEY (CountryID) REFERENCES Country(Country_ID) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producer_name' created successfully.<br>";
} else {
    echo "Error creating table 'producer_name': " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFilePath = 'producer_name.csv'; // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO producer_name (
            ProducersName, Location, CountryID, ProducerSize, VehicleID
        ) VALUES (?, ?, ?, ?, ?)
    ");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Bind parameters with each column of the CSV data
            $stmt->bind_param(
                "ssisi",
                $row[0],  // ProducersName
                $row[1],  // Location
                $row[2],  // CountryID
                $row[3],  // ProducerSize
                $row[4]   // VehicleID
            );

            // Execute the query and check for errors
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for ProducersName: " . $row[0] . "<br>";
            } else {
                echo "Error inserting data for ProducersName: " . $row[0] . " - " . $stmt->error . "<br>";
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
