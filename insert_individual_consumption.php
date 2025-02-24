<?php
// insert_individual_consumption.php

// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'individualconsumption' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS individualconsumption";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'individualconsumption' dropped successfully.<br>";
} else {
    echo "Error dropping table 'individualconsumption': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'individualconsumption' table with the specified columns and foreign keys
$createTableSQL = "
    CREATE TABLE IF NOT EXISTS individualconsumption (
        ConsumptionID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleID INT(11) NOT NULL,
        GenderID INT(11) NOT NULL,
        AgeID INT(11) NOT NULL,
        NumberOfPeople INT(11) NOT NULL,
        SourceVolume DECIMAL(20, 4) NOT NULL,
        VolumeMTY DECIMAL(20, 4) NOT NULL,
        UCID INT(11) NOT NULL,
        YearTypeID INT(11) NOT NULL,
        StartYear INT(11) NOT NULL,
        EndYear INT(11) NOT NULL,
        ReferenceID INT(11) NOT NULL,
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (GenderID) REFERENCES gender(GenderID),
        FOREIGN KEY (AgeID) REFERENCES age(AgeID),
        FOREIGN KEY (UCID) REFERENCES measureunit1(UCID),
        FOREIGN KEY (YearTypeID) REFERENCES yeartype(YearTypeID),
        FOREIGN KEY (ReferenceID) REFERENCES reference(ReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'individualconsumption' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Function to insert data into the individualconsumption table
function insertIndividualConsumption($conn, $vehicleID, $genderID, $ageID, $numberOfPeople, $sourceVolume, $ucid, $yearTypeID, $startYear, $endYear, $referenceID) {
    // Calculate VolumeMTY based on UCID and SourceVolume
    $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
    if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
        $unitValue = (float)$unitValueRow['UnitValue'];
        $volumeMTY = $sourceVolume * $unitValue;
    } else {
        echo "Error: Invalid UCID $ucid.<br>";
        return;
    }

    // Insert data into the individualconsumption table
    $sql = "
        INSERT INTO individualconsumption (
            VehicleID,
            GenderID,
            AgeID,
            NumberOfPeople,
            SourceVolume,
            VolumeMTY,
            UCID,
            YearTypeID,
            StartYear,
            EndYear,
            ReferenceID
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'iiiiidddiii',
        $vehicleID,         // i
        $genderID,          // i
        $ageID,             // i
        $numberOfPeople,    // i
        $sourceVolume,      // d
        $volumeMTY,         // d
        $ucid,              // i
        $yearTypeID,        // i
        $startYear,         // i
        $endYear,           // i
        $referenceID        // i
    );

    if ($stmt->execute()) {
        $consumptionID = $conn->insert_id;
        echo "✓ Inserted individual consumption record with ID: $consumptionID<br>";
    } else {
        echo "Error inserting individual consumption record: " . $stmt->error . "<br>";
    }

    $stmt->close();
}

// Fetch all rows from the consumption table
$consumptionResult = $conn->query("SELECT VehicleID, GenderID, AgeID, NumberOfPeople, SourceVolume, UCID, YearTypeID, StartYear, EndYear, ReferenceID FROM consumption");
if ($consumptionResult->num_rows > 0) {
    while ($row = $consumptionResult->fetch_assoc()) {
        insertIndividualConsumption(
            $conn,
            $row['VehicleID'],
            $row['GenderID'],
            $row['AgeID'],
            $row['NumberOfPeople'],
            $row['SourceVolume'],
            $row['UCID'],
            $row['YearTypeID'],
            $row['StartYear'],
            $row['EndYear'],
            $row['ReferenceID']
        );
    }
} else {
    echo "No records found in the consumption table.<br>";
}

// Note: Do not close the database connection here
// because it needs to remain open for subsequent operations in index.php
?>
