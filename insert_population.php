<?php
// Include the database connection
include('db_connect.php');

// SQL query to drop the 'population' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS population";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'population' dropped successfully.<br>";
} else {
    echo "Error dropping table 'population': " . $conn->error . "<br>";
}

// SQL query to create the 'population' table
$createTableSQL = "
    CREATE TABLE population (
        PopulationID INT(11) AUTO_INCREMENT PRIMARY KEY,
        VehicleID INT(11),
        AdminLevel1 VARCHAR(255),
        AdminLevel3 VARCHAR(255),
        PopulationGroup VARCHAR(255),
        AgeGroup VARCHAR(255),
        Value INT(11),
        AME FLOAT,
        Year VARCHAR(50),
        ReferenceNo INT(11),
        FOREIGN KEY (VehicleID) REFERENCES FoodVehicle(VehicleID),
        FOREIGN KEY (ReferenceNo) REFERENCES reference(ReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'population' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/population.csv';  // Update with the exact path of your CSV file

if (!file_exists($csvFile)) {
    die("Error: CSV file '$csvFile' not found.<br>");
}

echo "<br>Opening CSV file: $csvFile<br>";

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    // Skip header row
    $header = fgetcsv($handle, 1000, ",");
    echo "Header row: " . implode(", ", $header) . "<br>";

    $rowNumber = 2;
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // Clean and validate data
        $vehicleID = (int)trim($data[1]);
        $adminLevel1 = mysqli_real_escape_string($conn, trim($data[2]));
        $adminLevel3 = mysqli_real_escape_string($conn, trim($data[3]));
        $populationGroup = mysqli_real_escape_string($conn, trim($data[4]));
        $ageGroup = mysqli_real_escape_string($conn, trim($data[5]));
        $value = (int)trim($data[6]);
        $ame = (float)trim($data[7]);
        $year = mysqli_real_escape_string($conn, trim($data[8]));
        $referenceNo = (int)trim($data[9]);

        // Check if referenced values exist
        $checkVehicle = $conn->query("SELECT 1 FROM FoodVehicle WHERE VehicleID = $vehicleID");
        $checkReference = $conn->query("SELECT 1 FROM reference WHERE ReferenceID = $referenceNo");

        if ($checkVehicle->num_rows == 0) {
            echo "Error: VehicleID $vehicleID does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if ($checkReference->num_rows == 0) {
            echo "Error: ReferenceNo $referenceNo does not exist. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO population (
                    VehicleID,
                    AdminLevel1,
                    AdminLevel3,
                    PopulationGroup,
                    AgeGroup,
                    Value,
                    AME,
                    Year,
                    ReferenceNo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "issssidis",
            $vehicleID,
            $adminLevel1,
            $adminLevel3,
            $populationGroup,
            $ageGroup,
            $value,
            $ame,
            $year,
            $referenceNo
        );

        if ($stmt->execute()) {
            echo "✓ Inserted population record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting population record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'population' table contents:<br>";
$result = $conn->query("SELECT * FROM population ORDER BY PopulationID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['PopulationID']}, VehicleID: {$row['VehicleID']}, Value: {$row['Value']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
