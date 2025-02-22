<?php
// insert_producer_sku.php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// SQL query to drop the 'producersku' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS producersku";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'producersku' dropped successfully.<br>";
} else {
    echo "Error dropping table 'producersku': " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'producersku' table with foreign keys
$createTableSQL = "
    CREATE TABLE producersku (
        SKUID INT AUTO_INCREMENT PRIMARY KEY,
        ProductID INT(11) NOT NULL,
        CompanyID INT(11) NOT NULL,
        SKU INT(11),
        Unit VARCHAR(50),
        PackagingTypeID INT(11),
        Price DECIMAL(10,2),
        CurrencyID INT(11),
		ReferenceID INT(11),
        FOREIGN KEY (ProductID) REFERENCES product(ProductID),
        FOREIGN KEY (CompanyID) REFERENCES company(CompanyID),
        FOREIGN KEY (PackagingTypeID) REFERENCES packagingtype(PackagingTypeID),
        FOREIGN KEY (CurrencyID) REFERENCES measurecurrency(MCID),
		FOREIGN KEY (ReferenceID) REFERENCES reference(ReferenceID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'producersku' created successfully.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// Get valid ProductIDs, CompanyIDs, PackagingTypeIDs, CurrencyIDs, ReferenceIDs
$validProductIDs = array();
$validCompanyIDs = array();
$validPackagingTypeIDs = array();
$validCurrencyIDs = array();
$validReferenceIDs = array();

$result = $conn->query("SELECT ProductID FROM product");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validProductIDs[] = $row['ProductID'];
    }
} else {
    echo "Error getting valid ProductIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT CompanyID FROM company");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCompanyIDs[] = $row['CompanyID'];
    }
} else {
    echo "Error getting valid CompanyIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT PackagingTypeID FROM packagingtype");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validPackagingTypeIDs[] = $row['PackagingTypeID'];
    }
} else {
    echo "Error getting valid PackagingTypeIDs: " . $conn->error . "<br>";
}

$result = $conn->query("SELECT MCID FROM measurecurrency");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $validCurrencyIDs[] = $row['MCID'];
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
        $productID = (int)trim($data[0]);
        $companyID = (int)trim($data[2]);
        $sku = (int)trim($data[4]);
        $unit = mysqli_real_escape_string($conn, trim($data[5]));
        $packagingTypeID = (int)trim($data[6]);
        $price = (float)trim($data[8]);
        $currencyID = (int)trim($data[9]);
		$referenceID = (int)trim($data[11]);

        // Validate IDs
        if (!in_array($productID, $validProductIDs)) {
            echo "Error: ProductID $productID does not exist in product table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($companyID, $validCompanyIDs)) {
            echo "Error: CompanyID $companyID does not exist in company table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($packagingTypeID, $validPackagingTypeIDs)) {
            echo "Error: PackagingTypeID $packagingTypeID does not exist in packagingtype table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
        if (!in_array($currencyID, $validCurrencyIDs)) {
            echo "Error: CurrencyID $currencyID does not exist in measurecurrency table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }
		if (!in_array($referenceID, $validReferenceIDs)) {
            echo "Error: ReferenceID $referenceID does not exist in reference table. Skipping row $rowNumber.<br>";
            $rowNumber++;
            continue;
        }

        $sql = "INSERT INTO producersku (ProductID, CompanyID, SKU, Unit, PackagingTypeID, Price, CurrencyID, ReferenceID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiissdii", $productID, $companyID, $sku, $unit, $packagingTypeID, $price, $currencyID, $referenceID);

        if ($stmt->execute()) {
            echo "✓ Inserted producersku record ID: " . $conn->insert_id . "<br>";
        } else {
            echo "Error inserting producersku record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        $rowNumber++;
    }

    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Show final contents
echo "<br>Final 'producersku' table contents:<br>";
$result = $conn->query("SELECT * FROM producersku ORDER BY SKUID");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['SKUID']}, ProductID: {$row['ProductID']}, CompanyID: {$row['CompanyID']}, SKU: {$row['SKU']}, Unit: {$row['Unit']}, PackagingTypeID: {$row['PackagingTypeID']}, Price: {$row['Price']}, CurrencyID: {$row['CurrencyID']}, ReferenceID: {$row['ReferenceID']}<br>";
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
