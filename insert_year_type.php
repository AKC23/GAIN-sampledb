<?php
// insert_year_type.php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'yeartype' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS yeartype";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'yeartype' dropped successfully.<br>";
} else {
    echo "Error dropping table 'yeartype': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'yeartype' table with an auto-increment primary key
$createTableSQL = "
    CREATE TABLE yeartype (
        YearTypeID INT(11) AUTO_INCREMENT PRIMARY KEY,
        YearType VARCHAR(255) NOT NULL,
        StartMonth VARCHAR(50) NOT NULL,
        EndMonth VARCHAR(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'yeartype' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/year_type.csv';  // Update with the exact path of your CSV file

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
        $yearType = mysqli_real_escape_string($conn, trim($data[1]));
        $startMonth = mysqli_real_escape_string($conn, trim($data[2]));
        $endMonth = mysqli_real_escape_string($conn, trim($data[3]));

        $sql = "INSERT INTO yeartype (YearType, StartMonth, EndMonth) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $yearType, $startMonth, $endMonth);

        if ($stmt->execute()) {
            echo "✓ Inserted yeartype record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting yeartype record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'yeartype' table contents:<br>";
$result = $conn->query("SELECT * FROM yeartype ORDER BY YearTypeID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['YearTypeID']}, YearType: {$row['YearType']}, StartMonth: {$row['StartMonth']}, EndMonth: {$row['EndMonth']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
