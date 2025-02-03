<?php
// Include the database connection
include('db_connect.php');

// SQL query to drop the 'producer_sku' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producer_sku";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producer_sku' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producer_sku': " . $conn->error . "<br>";
}

// SQL query to create the 'producer_sku' table with foreign keys
$createTableSQL = "
    CREATE TABLE producer_sku (
        SKU_ID INT AUTO_INCREMENT PRIMARY KEY,
        BrandID INT,
        CompanyID INT,
        SKU VARCHAR(100),
        Unit VARCHAR(50),
        PackagingTypeID INT,
        Price DECIMAL(10,2),
        CurrencyID INT,
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

// Get valid CompanyIDs, Packaging_Type_IDs, CurrencyIDs, and ReferenceIDs
$validCompanyIDs = array();
$validPackagingTypeIDs = array();
$validCurrencyIDs = array();
$validReferenceIDs = array();

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

$result = $conn->query("SELECT ReferenceID FROM reference");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validReferenceIDs[] = $row['ReferenceID'];
    }
} else {
    echo "Error getting valid ReferenceIDs: " . $conn->error . "<br>";
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
        $brandName = mysqli_real_escape_string($conn, trim($data[0]));
        $companyID = (int)trim($data[2]);
        $sku = (int)trim($data[4]);
        $unit = mysqli_real_escape_string($conn, trim($data[5]));
        $packagingTypeID = (int)trim($data[6]);
        $price = (float)trim($data[7]);
        $currencyID = (int)trim($data[8]);
        $referenceID = (int)trim($data[10]);

        // Validate IDs
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
        if (!in_array($referenceID, $validReferenceIDs)) {
            echo "Error: ReferenceID $referenceID does not exist in reference table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO producer_sku (BrandName, CompanyID, SKU, Unit, Packaging_Type_ID, Price, CurrencyID, ReferenceID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siissdii", $brandName, $companyID, $sku, $unit, $packagingTypeID, $price, $currencyID, $referenceID);

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
        echo "ID: {$row['BrandID']}, BrandName: {$row['BrandName']}, CompanyID: {$row['CompanyID']}, SKU: {$row['SKU']}, Unit: {$row['Unit']}, Packaging_Type_ID: {$row['Packaging_Type_ID']}, Price: {$row['Price']}, CurrencyID: {$row['CurrencyID']}, ReferenceID: {$row['ReferenceID']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
