<?php
// insert_distribution.php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

$dropTableSQL = "DROP TABLE IF EXISTS distribution";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'distribution' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'distribution' table
$createTableSQL = "
    CREATE TABLE distribution (
        DistributionID INT(11) AUTO_INCREMENT PRIMARY KEY,
        DistributionChannelID INT(11),
        SubDistributionChannelID INT(11),
        VehicleID INT(11),
        UCID INT(11),
        SourceVolume DECIMAL(20,6),
        Volume_MT_Y DECIMAL(20,6),
        CountryID INT(11),
        YearTypeID INT(11),
        StartYear INT(4),
        EndYear INT(4),
        ReferenceID INT(11),
        FOREIGN KEY (DistributionChannelID) REFERENCES distributionchannel(DistributionChannelID),
        FOREIGN KEY (SubDistributionChannelID) REFERENCES subdistributionchannel(SubDistributionChannelID),
        FOREIGN KEY (VehicleID) REFERENCES foodvehicle(VehicleID),
        FOREIGN KEY (UCID) REFERENCES measureunit1(UCID),
        FOREIGN KEY (CountryID) REFERENCES country(CountryID),
        FOREIGN KEY (YearTypeID) REFERENCES yeartype(YearTypeID),
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
        $sourceVolume = (float)trim($data[9]);
        $countryID = (int)trim($data[11]);
        $yearTypeID = (int)trim($data[13]);
        $startYear = (int)trim($data[17]);
        $endYear = (int)trim($data[18]);
        $referenceID = (int)trim($data[19]);

        // Calculate Volume_MT_Y based on UCID and SourceVolume
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error: Invalid UCID $ucid in row $rowNumber. Skipping.<br>";
            $rowNumber++;
            continue;
        }

        // Check if referenced values exist
        $checkDistributionChannel = $conn->query("SELECT 1 FROM distributionchannel WHERE DistributionChannelID = $distributionChannelID");
        $checkSubDistributionChannel = $conn->query("SELECT 1 FROM subdistributionchannel WHERE SubDistributionChannelID = $subDistributionChannelID");
        $checkVehicle = $conn->query("SELECT 1 FROM foodvehicle WHERE VehicleID = $vehicleID");
        $checkUCID = $conn->query("SELECT 1 FROM measureunit1 WHERE UCID = $ucid");
        $checkCountry = $conn->query("SELECT 1 FROM country WHERE CountryID = $countryID");
        $checkYearType = $conn->query("SELECT 1 FROM yeartype WHERE YearTypeID = $yearTypeID");
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
        if ($checkCountry->num_rows == 0) {
            echo "Error: CountryID $countryID does not exist. Skipping row $rowNumber.<br>";
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
                    SourceVolume,
                    Volume_MT_Y,
                    CountryID,
                    YearTypeID,
                    StartYear,
                    EndYear,
                    ReferenceID
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "iiiiidiiiii",
            $distributionChannelID,
            $subDistributionChannelID,
            $vehicleID,
            $ucid,
            $sourceVolume,
            $volumeMTY,
            $countryID,
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
        echo "ID: {$row['DistributionID']}, DistributionChannelID: {$row['DistributionChannelID']}, SubDistributionChannelID: {$row['SubDistributionChannelID']}, VehicleID: {$row['VehicleID']}, UCID: {$row['UCID']}, SourceVolume: {$row['SourceVolume']}, Volume_MT_Y: {$row['Volume_MT_Y']}, CountryID: {$row['CountryID']}, YearTypeID: {$row['YearTypeID']}, StartYear: {$row['StartYear']}, EndYear: {$row['EndYear']}, ReferenceID: {$row['ReferenceID']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>





