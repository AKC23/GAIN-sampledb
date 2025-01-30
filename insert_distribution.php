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
        PeriodicalUnit VARCHAR(255),
        SourceVolumeUnit VARCHAR(255),
        Volume FLOAT,
        YearTypeID INT(11),
        StartYear VARCHAR(50),
        EndYear VARCHAR(50),
        ReferenceNo INT(11),
        FOREIGN KEY (DistributionChannelID) REFERENCES distribution_channel(DistributionChannelID),
        FOREIGN KEY (SubDistributionChannelID) REFERENCES sub_distribution_channel(SubDistributionChannelID),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (YearTypeID) REFERENCES year_type(YearTypeID),
        FOREIGN KEY (ReferenceNo) REFERENCES reference(ReferenceID)
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
        $periodicalUnit = mysqli_real_escape_string($conn, trim($data[6]));
        $sourceVolumeUnit = mysqli_real_escape_string($conn, trim($data[7]));
        $volume = (float)trim($data[8]);
        $yearTypeID = (int)trim($data[9]);
        $startYear = trim($data[13]);
        $endYear = trim($data[14]);
        $referenceNo = (int)trim($data[15]);

        // Check if referenced values exist
        $checkDistributionChannel = $conn->query("SELECT 1 FROM distribution_channel WHERE DistributionChannelID = $distributionChannelID");
        $checkSubDistributionChannel = $conn->query("SELECT 1 FROM sub_distribution_channel WHERE SubDistributionChannelID = $subDistributionChannelID");
        $checkVehicle = $conn->query("SELECT 1 FROM FoodVehicle WHERE VehicleID = $vehicleID");
        $checkYearType = $conn->query("SELECT 1 FROM year_type WHERE YearTypeID = $yearTypeID");
        $checkReference = $conn->query("SELECT 1 FROM reference WHERE ReferenceID = $referenceNo");

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
        if ($checkYearType->num_rows == 0) {
            echo "Error: YearTypeID $yearTypeID does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if ($checkReference->num_rows == 0) {
            echo "Error: ReferenceNo $referenceNo does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO distribution (
                    DistributionChannelID,
                    SubDistributionChannelID,
                    VehicleID,
                    PeriodicalUnit,
                    SourceVolumeUnit,
                    Volume,
                    YearTypeID,
                    StartYear,
                    EndYear,
                    ReferenceNo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iiissdisss",
            $distributionChannelID,
            $subDistributionChannelID,
            $vehicleID,
            $periodicalUnit,
            $sourceVolumeUnit,
            $volume,
            $yearTypeID,
            $startYear,
            $endYear,
            $referenceNo
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
        echo "ID: {$row['DistributionID']}, VehicleID: {$row['VehicleID']}, Volume: {$row['Volume']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>





