<?php
// insert_adult_male_equivalent.php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'adultmaleequivalent' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS adultmaleequivalent";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'adultmaleequivalent' dropped successfully.<br>";
} else {
    echo "Error dropping table 'adultmaleequivalent': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'adultmaleequivalent' table with an auto-increment primary key
$createTableSQL = "
    CREATE TABLE adultmaleequivalent (
        AMEID INT(11) AUTO_INCREMENT PRIMARY KEY,
        AME DECIMAL(10, 2) NOT NULL,
        GenderID INT(11) NOT NULL,
        AgeID INT(11) NOT NULL,
        FOREIGN KEY (GenderID) REFERENCES gender(GenderID),
        FOREIGN KEY (AgeID) REFERENCES age(AgeID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'adultmaleequivalent' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/adult_male_equivalent.csv';  // Update with the exact path of your CSV file

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
        $ame = mysqli_real_escape_string($conn, trim($data[0]));
        $genderID = mysqli_real_escape_string($conn, trim($data[1]));
        $ageID = mysqli_real_escape_string($conn, trim($data[3]));

        $sql = "INSERT INTO adultmaleequivalent (AME, GenderID, AgeID) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $ame, $genderID, $ageID);

        if ($stmt->execute()) {
            echo "✓ Inserted adultmaleequivalent record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting adultmaleequivalent record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'adultmaleequivalent' table contents:<br>";
$result = $conn->query("SELECT * FROM adultmaleequivalent ORDER BY AMEID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['AMEID']}, AME: {$row['AME']}, GenderID: {$row['GenderID']}, AgeID: {$row['AgeID']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
