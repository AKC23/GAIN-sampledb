<?php
// Include the database connection
include('db_connect.php');

// SQL query to drop the 'import_amount_oilseeds' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS import_amount_oilseeds";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'import_amount_oilseeds' dropped successfully.<br>";
} else {
    echo "Error dropping table 'import_amount_oilseeds': " . $conn->error . "<br>";
}

// SQL query to create the 'import_amount_oilseeds' table with foreign keys
$createTableSQL = "
    CREATE TABLE import_amount_oilseeds (
        DataID INT AUTO_INCREMENT PRIMARY KEY,
        RawCropsID INT,
        VehicleID INT,
        Converted_Unit VARCHAR(50),
        Value DECIMAL(20, 3),
        Start_Year VARCHAR(15),
        End_Year VARCHAR(15),
        AccessedDate VARCHAR(15),
        Source TEXT,
        Link TEXT,
        DataType VARCHAR(50),
        ProcessToObtainData TEXT,
        FOREIGN KEY (RawCropsID) REFERENCES raw_crops(RawCropsID) ON DELETE CASCADE ON UPDATE CASCADE,
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'import_amount_oilseeds' created successfully with foreign keys.<br>";
} else {
    echo "Error creating table 'import_amount_oilseeds': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file
$csvFile = 'import_amount_oilseeds.csv';  // Update with the exact path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row (if there is one)
    fgetcsv($handle);

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("INSERT INTO import_amount_oilseeds (RawCropsID, VehicleID, Converted_Unit, Value, Start_Year, End_Year, AccessedDate, Source, Link, DataType, ProcessToObtainData) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Check if the statement was prepared successfully
    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read through each line of the CSV file
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract relevant columns from the CSV data
            $RawCropsID = $data[1];
            $VehicleID = $data[4];
            $Converted_Unit = $data[6];
            $Value = $data[7];
            $Start_Year = $data[8];
            $End_Year = $data[9];
            $AccessedDate = $data[10];
            $Source = mysqli_real_escape_string($conn, trim($data[11]));
            $Link = mysqli_real_escape_string($conn, trim($data[12]));
            $DataType = mysqli_real_escape_string($conn, trim($data[13]));
            $ProcessToObtainData = mysqli_real_escape_string($conn, trim($data[14]));

            // Bind parameters
            $stmt->bind_param("iisssssssss", $RawCropsID, $VehicleID, $Converted_Unit, $Value, $Start_Year, $End_Year, $AccessedDate, $Source, $Link, $DataType, $ProcessToObtainData);

            // Execute the query
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully for RawCropsID: $RawCropsID<br>";
            } else {
                echo "Error inserting data for RawCropsID: $RawCropsID - " . $stmt->error . "<br>";
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
