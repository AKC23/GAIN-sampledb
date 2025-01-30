<?php
// Include the database connection
include('db_connect.php');

// SQL query to drop the 'year_type' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS year_type";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'year_type' dropped successfully.<br>";
} else {
    echo "Error dropping table 'year_type': " . $conn->error . "<br>";
}

// SQL query to create the 'year_type' table with an auto-increment primary key
$createTableSQL = "
    CREATE TABLE year_type (
        YearTypeID INT(11) AUTO_INCREMENT PRIMARY KEY,
        YearType VARCHAR(255) NOT NULL,
        StartMonth VARCHAR(50) NOT NULL,
        EndMonth VARCHAR(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'year_type' created successfully.<br>";
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

        $sql = "INSERT INTO year_type (YearType, StartMonth, EndMonth) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $yearType, $startMonth, $endMonth);

        if ($stmt->execute()) {
            echo "✓ Inserted year_type record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting year_type record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'year_type' table contents:<br>";
$result = $conn->query("SELECT * FROM year_type ORDER BY YearTypeID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['YearTypeID']}, YearType: {$row['YearType']}, StartMonth: {$row['StartMonth']}, EndMonth: {$row['EndMonth']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
