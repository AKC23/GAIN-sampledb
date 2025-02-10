<?php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'producer_sku' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producer_sku";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producer_sku' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producer_sku': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'producer_sku' table with foreign keys
$createTableSQL = "
    CREATE TABLE producer_sku (
        SKU_ID INT AUTO_INCREMENT PRIMARY KEY,
        BrandID INT(11),
        CompanyID INT(11),
        SKU VARCHAR(100),
        Unit VARCHAR(50),
        PackagingTypeID INT(11),
        Price DECIMAL(10,2),
        CurrencyID INT(11),
        FOREIGN KEY (BrandID) REFERENCES brand(BrandID),
        FOREIGN KEY (CompanyID) REFERENCES company(CompanyID),
        FOREIGN KEY (PackagingTypeID) REFERENCES packaging_type(Packaging_Type_ID),
        FOREIGN KEY (CurrencyID) REFERENCES measure_currency(CurrencyID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producer_sku' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid BrandIDs, CompanyIDs, Packaging_Type_IDs, and CurrencyIDs
$validBrandIDs = array();
$validCompanyIDs = array();
$validPackagingTypeIDs = array();
$validCurrencyIDs = array();

$result = $conn->query("SELECT BrandID FROM brand");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validBrandIDs[] = $row['BrandID'];
    }
} else {
    echo "Error getting valid BrandIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT CompanyID FROM company");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCompanyIDs[] = $row['CompanyID'];
    }
} else {
    echo "Error getting valid CompanyIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT Packaging_Type_ID FROM packaging_type");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validPackagingTypeIDs[] = $row['Packaging_Type_ID'];
    }
} else {
    echo "Error getting valid Packaging_Type_IDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT CurrencyID FROM measure_currency");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCurrencyIDs[] = $row['CurrencyID'];
    }
} else {
    echo "Error getting valid CurrencyIDs: " . $conn->error . "<br>";
}

// Path to your CSV file
$csvFile = 'data/producer_sku.csv';  // Update with the exact path of your CSV file

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
        $brandID = (int)trim($data[0]);
        $companyID = (int)trim($data[2]);
        $sku = mysqli_real_escape_string($conn, trim($data[4]));
        $unit = mysqli_real_escape_string($conn, trim($data[5]));
        $packagingTypeID = (int)trim($data[6]);
        $price = (float)trim($data[7]);
        $currencyID = (int)trim($data[8]);

        // Validate IDs
        if (!in_array($brandID, $validBrandIDs)) {
            echo "Error: BrandID $brandID does not exist in brand table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($companyID, $validCompanyIDs)) {
            echo "Error: CompanyID $companyID does not exist in company table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($packagingTypeID, $validPackagingTypeIDs)) {
            echo "Error: Packaging_Type_ID $packagingTypeID does not exist in packaging_type table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($currencyID, $validCurrencyIDs)) {
            echo "Error: CurrencyID $currencyID does not exist in measure_currency table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO producer_sku (BrandID, CompanyID, SKU, Unit, Packaging_Type_ID, Price, CurrencyID) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiissdi", $brandID, $companyID, $sku, $unit, $packagingTypeID, $price, $currencyID);

        if ($stmt->execute()) {
            echo "✓ Inserted producer_sku record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting producer_sku record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'producer_sku' table contents:<br>";
$result = $conn->query("SELECT * FROM producer_sku ORDER BY BrandID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['SKU_ID']}, BrandID: {$row['BrandID']}, CompanyID: {$row['CompanyID']}, SKU: {$row['SKU']}, Unit: {$row['Unit']}, Packaging_Type_ID: {$row['Packaging_Type_ID']}, Price: {$row['Price']}, CurrencyID: {$row['CurrencyID']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
