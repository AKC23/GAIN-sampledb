<?php
// Include the database connection
include('db_connect.php');


$dropTableSQL = "DROP TABLE IF EXISTS crude_oil";

// SQL query to create the 'crude_oil' table with the required columns
$createTableSQL = "
    CREATE TABLE crude_oil (
        DataID INT PRIMARY KEY AUTO_INCREMENT,
        VehicleID INT,
        FoodTypeID INT,
        PSID INT,
        Country_ID INT,
        SourceVolume DECIMAL(15, 3),
        ConvertedValue DECIMAL(15, 3),
        VolumeUnit VARCHAR(50),
        PeriodicalUnit VARCHAR(50),
        StartYear VARCHAR(50),
        EndYear VARCHAR(50),
        AccessedDate VARCHAR(50),
        Source VARCHAR(255),
        Link VARCHAR(255),
        Process VARCHAR(255),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (FoodTypeID) REFERENCES FoodType(FoodTypeID),
        FOREIGN KEY (PSID) REFERENCES processing_stage(PSID),
        FOREIGN KEY (Country_ID) REFERENCES Country(Country_ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'crude_oil' created successfully.<br>";
} else {
    echo "Error creating table 'crude_oil': " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/crude_oil.csv'; // Update this with the actual path to your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row
    $header = fgetcsv($handle);
    echo "Header row: " . implode(", ", $header) . "<br>";

    // Get all valid IDs from reference tables
    $validIDs = array(
        'vehicle' => array(),
        'foodtype' => array(),
        'rawcrops' => array(),
        'country' => array()
    );

    // Get valid VehicleIDs
    $result = $conn->query("SELECT VehicleID FROM FoodVehicle");
    if ($result) {
        echo "<br>Valid VehicleIDs in database:<br>";
        while ($row = $result->fetch_assoc()) {
            $validIDs['vehicle'][] = $row['VehicleID'];
            echo "VehicleID: {$row['VehicleID']}<br>";
        }
    }

    // Get valid FoodTypeIDs
    $result = $conn->query("SELECT FoodTypeID FROM FoodType");
    if ($result) {
        echo "<br>Valid FoodTypeIDs in database:<br>";
        while ($row = $result->fetch_assoc()) {
            $validIDs['foodtype'][] = $row['FoodTypeID'];
            echo "FoodTypeID: {$row['FoodTypeID']}<br>";
        }
    }

    // Get valid PSIDs
    $result = $conn->query("SELECT PSID FROM processing_stage");
    if ($result) {
        echo "<br>Valid PSIDs in database:<br>";
        while ($row = $result->fetch_assoc()) {
            $validIDs['rawcrops'][] = $row['PSID'];
            echo "PSID: {$row['PSID']}<br>";
        }
    }

    // Get valid Country_IDs
    $result = $conn->query("SELECT Country_ID FROM Country");
    if ($result) {
        echo "<br>Valid Country_IDs in database:<br>";
        while ($row = $result->fetch_assoc()) {
            $validIDs['country'][] = $row['Country_ID'];
            echo "Country_ID: {$row['Country_ID']}<br>";
        }
    }

    // Prepare the SQL statement with placeholders
    $stmt = $conn->prepare("
        INSERT INTO crude_oil (
            VehicleID, FoodTypeID, PSID, Country_ID,
            SourceVolume, ConvertedValue, VolumeUnit, PeriodicalUnit,
            StartYear, EndYear, AccessedDate, Source, Link, Process
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if ($stmt === FALSE) {
        echo "Error preparing statement: " . $conn->error . "<br>";
    } else {
        // Read each line of the CSV file
        $rowNumber = 2; // Start from 2 since row 1 is header
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Extract the relevant columns from the CSV file
            $vehicleID = (int) trim($data[1]);
            $foodTypeID = (int) trim($data[3]);
            $PSID = (int) trim($data[5]);
            $countryID = (int) trim($data[7]);

            echo "<br>Processing Row $rowNumber:<br>";
            echo "VehicleID from CSV: $vehicleID (Valid IDs: " . implode(", ", $validIDs['vehicle']) . ")<br>";
            echo "FoodTypeID from CSV: $foodTypeID (Valid IDs: " . implode(", ", $validIDs['foodtype']) . ")<br>";
            echo "PSID from CSV: $PSID (Valid IDs: " . implode(", ", $validIDs['rawcrops']) . ")<br>";
            echo "Country_ID from CSV: $countryID (Valid IDs: " . implode(", ", $validIDs['country']) . ")<br>";

            // Validate foreign key values
            $isValid = true;
            if (!in_array($vehicleID, $validIDs['vehicle'])) {
                echo "Error: Invalid VehicleID $vehicleID in row $rowNumber. Skipping.<br>";
                $isValid = false;
            }
            if (!in_array($foodTypeID, $validIDs['foodtype'])) {
                echo "Error: Invalid FoodTypeID $foodTypeID in row $rowNumber. Skipping.<br>";
                $isValid = false;
            }
            if (!in_array($PSID, $validIDs['rawcrops'])) {
                echo "Error: Invalid PSID $PSID in row $rowNumber. Skipping.<br>";
                $isValid = false;
            }
            if (!in_array($countryID, $validIDs['country'])) {
                echo "Error: Invalid Country_ID $countryID in row $rowNumber. Skipping.<br>";
                $isValid = false;
            }

            if (!$isValid) {
                $rowNumber++;
                continue;
            }

            $sourceVolume = (float) trim($data[8]);
            $convertedValue = (float) trim($data[9]);
            $volumeUnit = trim($data[10]);
            $periodicalUnit = trim($data[11]);
            $startYear = trim($data[12]);
            $endYear = trim($data[13]);
            $accessedDate = trim($data[14]);
            $source = trim($data[15]);
            $link = trim($data[16]);
            $process = trim($data[17]);

            // Bind the parameters to the statement
            $stmt->bind_param(
                "iiiiddssssssss",
                $vehicleID, $foodTypeID, $PSID, $countryID,
                $sourceVolume, $convertedValue, $volumeUnit, $periodicalUnit,
                $startYear, $endYear, $accessedDate, $source, $link, $process
            );

            // Execute the query
            if ($stmt->execute() === TRUE) {
                echo "Data inserted successfully.<br>";
            } else {
                echo "Error inserting data: " . $stmt->error . "<br>";
            }
            $rowNumber++;
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the file after reading
    fclose($handle);
} else {
    echo "Error: Could not open the CSV file.<br>";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
// $conn->close(); // REMOVED: This line should NOT be uncommented
?>
