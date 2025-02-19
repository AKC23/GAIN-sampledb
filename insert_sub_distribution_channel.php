<?php
// insert_sub_distribution_channel.php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'subdistributionchannel' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS subdistributionchannel";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'subdistributionchannel' dropped successfully.<br>";
} else {
    echo "Error dropping table 'subdistributionchannel': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'subdistributionchannel' table with an auto-increment primary key
$createTableSQL = "
    CREATE TABLE subdistributionchannel (
        SubDistributionChannelID INT(11) AUTO_INCREMENT PRIMARY KEY,
        SubDistributionChannelName VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'subdistributionchannel' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/sub_distribution_channel.csv';  // Update with the exact path of your CSV file

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
        $subDistributionChannelName = mysqli_real_escape_string($conn, trim($data[0]));

        $sql = "INSERT INTO subdistributionchannel (SubDistributionChannelName) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $subDistributionChannelName);

        if ($stmt->execute()) {
            echo "✓ Inserted subdistributionchannel record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting subdistributionchannel record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'subdistributionchannel' table contents:<br>";
$result = $conn->query("SELECT * FROM subdistributionchannel ORDER BY SubDistributionChannelID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['SubDistributionChannelID']}, SubDistributionChannelName: {$row['SubDistributionChannelName']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
