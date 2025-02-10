<?php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

$dropTableSQL = "DROP TABLE IF EXISTS company";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'company' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'company' table
$createTableSQL = "
    CREATE TABLE company (
        CompanyID INT(11) AUTO_INCREMENT PRIMARY KEY,
        CompanyGroup VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'company' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/company.csv';

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
        $companyGroup = mysqli_real_escape_string($conn, trim($data[0]));

        $sql = "INSERT INTO company (CompanyGroup) VALUES (?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $companyGroup);

        if ($stmt->execute()) {
            echo "✓ Inserted company record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting company record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'company' table contents:<br>";
$result = $conn->query("SELECT * FROM company ORDER BY CompanyID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['CompanyID']}, CompanyGroup: {$row['CompanyGroup']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
