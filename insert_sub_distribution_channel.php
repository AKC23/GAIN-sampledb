<?php
// Include the database connection
include('db_connect.php');

// SQL query to drop the 'sub_distribution_channel' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS sub_distribution_channel";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'sub_distribution_channel' dropped successfully.<br>";
} else {
    echo "Error dropping table 'sub_distribution_channel': " . $conn->error . "<br>";
}

// SQL query to create the 'sub_distribution_channel' table with an auto-increment primary key
$createTableSQL = "
    CREATE TABLE sub_distribution_channel (
        SubDistributionChannelID INT(11) AUTO_INCREMENT PRIMARY KEY,
        SubDistributionChannelName VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'sub_distribution_channel' created successfully.<br>";
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

        $sql = "INSERT INTO sub_distribution_channel (SubDistributionChannelName) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $subDistributionChannelName);

        if ($stmt->execute()) {
            echo "✓ Inserted sub_distribution_channel record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting sub_distribution_channel record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'sub_distribution_channel' table contents:<br>";
$result = $conn->query("SELECT * FROM sub_distribution_channel ORDER BY SubDistributionChannelID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['SubDistributionChannelID']}, SubDistributionChannelName: {$row['SubDistributionChannelName']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
