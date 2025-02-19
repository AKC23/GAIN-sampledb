<?php
// Path: insert_distribution_channel.php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET foreign_key_checks = 0");

// SQL query to drop the 'distributionchannel' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS distributionchannel";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'distributionchannel' dropped successfully.<br>";
} else {
    echo "Error dropping table 'distributionchannel': " . $conn->error . "<br>";
}

// SQL query to create the 'distributionchannel' table with an auto-increment primary key
$createTableSQL = "
    CREATE TABLE distributionchannel (
        DistributionChannelID INT(11) AUTO_INCREMENT PRIMARY KEY,
        DistributionChannelName VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'distributionchannel' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Enable foreign key checks
$conn->query("SET foreign_key_checks = 1");

// Path to your CSV file
$csvFile = 'data/distribution_channel.csv';  // Update with the exact path of your CSV file

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
        $distributionChannelName = mysqli_real_escape_string($conn, trim($data[0]));

        $sql = "INSERT INTO distributionchannel (DistributionChannelName) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $distributionChannelName);

        if ($stmt->execute()) {
            echo "✓ Inserted distribution_channel record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting distribution_channel record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'distributionchannel' table contents:<br>";
$result = $conn->query("SELECT * FROM distributionchannel ORDER BY DistributionChannelID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['DistributionChannelID']}, DistributionChannelName: {$row['DistributionChannelName']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
