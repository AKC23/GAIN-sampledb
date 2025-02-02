<?php
// Include the database connection
include('db_connect.php');

$dropTableSQL = "DROP TABLE IF EXISTS distribution";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'distribution' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// SQL query to create the 'distribution' table
$createTableSQL = "
    CREATE TABLE distribution (
        DistributionID INT(11) AUTO_INCREMENT PRIMARY KEY,
        DistributionChannelID INT(11),
        SubDistributionChannelID INT(11),
        VehicleID INT(11),
        UCID INT(11),
        volumeMT FLOAT,
        YearTypeID INT(11),
        StartYear VARCHAR(50),
        EndYear VARCHAR(50),
        ReferenceID INT(11),
        FOREIGN KEY (DistributionChannelID) REFERENCES distribution_channel(DistributionChannelID),
        FOREIGN KEY (SubDistributionChannelID) REFERENCES sub_distribution_channel(SubDistributionChannelID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (UCID) REFERENCES measure_unit1(UCID),
        FOREIGN KEY (YearTypeID) REFERENCES year_type(YearTypeID),
        FOREIGN KEY (ReferenceID) REFERENCES reference(ReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'distribution' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/distribution.csv';

if (!file_exists($csvFile)) {
    die("Error: CSV file '$csvFile' not found.<br>");
}

echo "<br>Opening CSV file: $csvFile<br>";

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    $header = fgetcsv($handle);
    echo "Header row: " . implode(", ", $header) . "<br>";

    $rowNumber = 2;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Clean and validate data
        $distributionChannelID = (int)trim($data[0]);
        $subDistributionChannelID = (int)trim($data[2]);
        $vehicleID = (int)trim($data[4]);
        $ucid = (int)trim($data[6]);
        $volumeMT = (float)trim($data[7]);
        $yearTypeID = (int)trim($data[10]);
        $startYear = trim($data[14]);
        $endYear = trim($data[15]);
        $referenceID = (int)trim($data[16]);

        // Check if referenced values exist
        $checkDistributionChannel = $conn->query("SELECT 1 FROM distribution_channel WHERE DistributionChannelID = $distributionChannelID");
        $checkSubDistributionChannel = $conn->query("SELECT 1 FROM sub_distribution_channel WHERE SubDistributionChannelID = $subDistributionChannelID");
        $checkVehicle = $conn->query("SELECT 1 FROM FoodVehicle WHERE VehicleID = $vehicleID");
        $checkUCID = $conn->query("SELECT 1 FROM measure_unit1 WHERE UCID = $ucid");
        $checkYearType = $conn->query("SELECT 1 FROM year_type WHERE YearTypeID = $yearTypeID");
        $checkReference = $conn->query("SELECT 1 FROM reference WHERE ReferenceID = $referenceID");

        if ($checkDistributionChannel->num_rows == 0) {
            echo "Error: DistributionChannelID $distributionChannelID does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if ($checkSubDistributionChannel->num_rows == 0) {
            echo "Error: SubDistributionChannelID $subDistributionChannelID does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if ($checkVehicle->num_rows == 0) {
            echo "Error: VehicleID $vehicleID does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if ($checkUCID->num_rows == 0) {
            echo "Error: UCID $ucid does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if ($checkYearType->num_rows == 0) {
            echo "Error: YearTypeID $yearTypeID does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if ($checkReference->num_rows == 0) {
            echo "Error: ReferenceID $referenceID does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO distribution (
                    DistributionChannelID,
                    SubDistributionChannelID,
                    VehicleID,
                    UCID,
                    volumeMT,
                    YearTypeID,
                    StartYear,
                    EndYear,
                    ReferenceID
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iiiiisssi",
            $distributionChannelID,
            $subDistributionChannelID,
            $vehicleID,
            $ucid,
            $volumeMT,
            $yearTypeID,
            $startYear,
            $endYear,
            $referenceID
        );

        if ($stmt->execute()) {
            echo "✓ Inserted distribution record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting distribution record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'distribution' table contents:<br>";
$result = $conn->query("SELECT * FROM distribution ORDER BY DistributionID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['DistributionID']}, VehicleID: {$row['VehicleID']}, UCID: {$row['UCID']}, VolumeMT: {$row['volumeMT']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>





