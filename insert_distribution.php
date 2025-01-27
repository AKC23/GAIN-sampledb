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
        VehicleID INT(11),
        DistributionChannel VARCHAR(255),
        SubDistributionChannel VARCHAR(255),
        PeriodicalUnit VARCHAR(255),
        SourceVolumeUnit VARCHAR(255),
        Volume FLOAT,
        YearType VARCHAR(50),
        StartYear VARCHAR(50),
        StartMonth VARCHAR(50),
        EndYear VARCHAR(50),
        EndMonth VARCHAR(50),
        ReferenceNo VARCHAR(255),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID)
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
        $distChannel = mysqli_real_escape_string($conn, trim($data[0]));
        $subDistChannel = mysqli_real_escape_string($conn, trim($data[1]));
        $vehicleID = (int)trim($data[2]);
        $periodicalUnit = mysqli_real_escape_string($conn, trim($data[4]));
        $sourceVolumeUnit = mysqli_real_escape_string($conn, trim($data[5]));
        $volume = (float)trim($data[6]);
        $yearType = mysqli_real_escape_string($conn, trim($data[7]));
        $startYear = trim($data[8]);
        $startMonth = trim($data[9]);
        $endYear = trim($data[10]);
        $endMonth = trim($data[11]);
        $referenceNo = mysqli_real_escape_string($conn, trim($data[12]));

        $sql = "INSERT INTO distribution (
                    DistributionChannel,
                    SubDistributionChannel,
                    VehicleID,
                    PeriodicalUnit,
                    SourceVolumeUnit,
                    Volume,
                    YearType,
                    StartYear,
                    StartMonth,
                    EndYear,
                    EndMonth,
                    ReferenceNo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssiissdsssss",
            $distChannel,
            $subDistChannel,
            $vehicleID,
            $periodicalUnit,
            $sourceVolumeUnit,
            $volume,
            $yearType,
            $startYear,
            $startMonth,
            $endYear,
            $endMonth,
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





