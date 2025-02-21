<?php
// insert_product.php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'product' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS product";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'product' dropped successfully.<br>";
} else {
    echo "Error dropping table 'product': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'product' table with an auto-increment primary key
$createTableSQL = "
    CREATE TABLE product (
        ProductID INT(11) AUTO_INCREMENT PRIMARY KEY,
        ProductName VARCHAR(255) NOT NULL,
		ProductCategory VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'product' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/product.csv';  // Update with the exact path of your CSV file

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
        $productName = mysqli_real_escape_string($conn, trim($data[0]));
		$productCategory = mysqli_real_escape_string($conn, trim($data[1]));

        $sql = "INSERT INTO product (ProductName, ProductCategory) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $productName, $productCategory);

        if ($stmt->execute()) {
            echo "✓ Inserted product record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting product record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'product' table contents:<br>";
$result = $conn->query("SELECT * FROM product ORDER BY ProductID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['ProductID']}, ProductName: {$row['ProductName']}, ProductCategory: {$row['ProductCategory']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
