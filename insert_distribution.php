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
        Volume FLOAT,
        UnitID INT(11),
        PeriodID INT(11),
        StartYear VARCHAR(50),
        EndYear VARCHAR(50),
        AccessedDate DATE,
        Source VARCHAR(255),
        Link VARCHAR(255),
        ProcessToObtainData VARCHAR(255),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (UnitID) REFERENCES measure_unit(UnitID),
        FOREIGN KEY (PeriodID) REFERENCES measure_period(PeriodID)
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
        $vehicleID = (int)trim($data[0]);
        $distChannel = mysqli_real_escape_string($conn, trim($data[1]));
        $subDistChannel = mysqli_real_escape_string($conn, trim($data[2]));
        $volume = (float)trim($data[3]);
        $unitID = (int)trim($data[4]);
        $periodID = (int)trim($data[5]);
        $startYear = trim($data[6]);
        $endYear = trim($data[7]);
        $accessedDate = date('Y-m-d', strtotime(trim($data[8])));
        $source = mysqli_real_escape_string($conn, trim($data[9]));
        $link = mysqli_real_escape_string($conn, trim($data[10]));
        $processData = mysqli_real_escape_string($conn, trim($data[11]));

        $sql = "INSERT INTO distribution (
                    VehicleID,
                    DistributionChannel,
                    SubDistributionChannel,
                    Volume,
                    UnitID,
                    PeriodID,
                    StartYear,
                    EndYear,
                    AccessedDate,
                    Source,
                    Link,
                    ProcessToObtainData
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "issfiissssss",
            $vehicleID,
            $distChannel,
            $subDistChannel,
            $volume,
            $unitID,
            $periodID,
            $startYear,
            $endYear,
            $accessedDate,
            $source,
            $link,
            $processData
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





