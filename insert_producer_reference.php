<?php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'producer_reference' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producer_reference";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producer_reference' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producer_reference': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'producer_reference' table with foreign keys
$createTableSQL = "
    CREATE TABLE producer_reference (
        Identifier_No INT(11) AUTO_INCREMENT PRIMARY KEY,
        Regulatory_Body VARCHAR(255),
        CountryID INT(11),
        FOREIGN KEY (CountryID) REFERENCES country(Country_ID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producer_reference' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid CountryIDs
$validCountryIDs = array();

$result = $conn->query("SELECT Country_ID FROM country");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCountryIDs[] = $row['Country_ID'];
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
        $regulatoryBody = mysqli_real_escape_string($conn, trim($data[1]));
        $countryID = (int)trim($data[2]);

        // Validate CountryID
        if (!in_array($countryID, $validCountryIDs)) {
            echo "Error: CountryID $countryID does not exist in country table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO producer_reference (Regulatory_Body, CountryID) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $regulatoryBody, $countryID);

        if ($stmt->execute()) {
            echo "✓ Inserted producer_reference record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting producer_reference record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'producer_reference' table contents:<br>";
$result = $conn->query("SELECT * FROM producer_reference ORDER BY Identifier_No");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['Identifier_No']}, Regulatory Body: {$row['Regulatory_Body']}, CountryID: {$row['CountryID']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
