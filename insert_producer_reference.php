<?php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'producerreference' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producerreference";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producerreference' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producerreference': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'producerreference' table with foreign keys
$createTableSQL = "
    CREATE TABLE producerreference (
        ProducerReferenceID INT(11) AUTO_INCREMENT PRIMARY KEY,
        CompanyID INT(11),
        IdentifierNumber VARCHAR(255),
        IdentifierReferenceSystem VARCHAR(255),
        CountryID INT(11),
        FOREIGN KEY (CompanyID) REFERENCES company(CompanyID),
        FOREIGN KEY (CountryID) REFERENCES country(CountryID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producerreference' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid CompanyIDs and CountryIDs
$validCompanyIDs = array();
$validCountryIDs = array();

$result = $conn->query("SELECT CompanyID FROM company");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCompanyIDs[] = $row['CompanyID'];
    }
} else {
    echo "Error getting valid CompanyIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT CountryID FROM country");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCountryIDs[] = $row['CountryID'];
    }
} else {
    echo "Error getting valid CountryIDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/producer_reference.csv';  // Update with the exact path of your CSV file

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
        $companyID = (int)trim($data[0]);
        $identifierNumber = mysqli_real_escape_string($conn, trim($data[2]));
        $identifierReferenceSystem = mysqli_real_escape_string($conn, trim($data[3]));
        $countryID = (int)trim($data[4]);

        // Validate CompanyID and CountryID
        if (!in_array($companyID, $validCompanyIDs)) {
            echo "Error: CompanyID $companyID does not exist in company table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($countryID, $validCountryIDs)) {
            echo "Error: CountryID $countryID does not exist in country table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO producerreference (CompanyID, IdentifierNumber, IdentifierReferenceSystem, CountryID) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $companyID, $identifierNumber, $identifierReferenceSystem, $countryID);

        if ($stmt->execute()) {
            echo "✓ Inserted producerreference record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting producerreference record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'producerreference' table contents:<br>";
$result = $conn->query("SELECT * FROM producerreference ORDER BY ProducerReferenceID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['ProducerReferenceID']}, CompanyID: {$row['CompanyID']}, IdentifierNumber: {$row['IdentifierNumber']}, IdentifierReferenceSystem: {$row['IdentifierReferenceSystem']}, CountryID: {$row['CountryID']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
