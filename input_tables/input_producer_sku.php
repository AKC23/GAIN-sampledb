<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $product_id = $_POST['product_id'];
        $company_id = $_POST['company_id'];
        $sku = $_POST['sku'];
        $unit = $_POST['unit'];
        $packaging_type_id = $_POST['packaging_type_id'];
        $price = $_POST['price'];
        $currency_id = $_POST['currency_id'];
        $reference_id = $_POST['reference_id'];

        $stmt = $conn->prepare("INSERT INTO producersku (ProductID, CompanyID, SKU, Unit, PackagingTypeID, Price, CurrencyID, ReferenceID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisdisi", $product_id, $company_id, $sku, $unit, $packaging_type_id, $price, $currency_id, $reference_id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_producer_sku.php");
        exit;
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $product_id = $_POST['product_id'];
        $company_id = $_POST['company_id'];
        $sku = $_POST['sku'];
        $unit = $_POST['unit'];
        $packaging_type_id = $_POST['packaging_type_id'];
        $price = $_POST['price'];
        $currency_id = $_POST['currency_id'];
        $reference_id = $_POST['reference_id'];

        $stmt = $conn->prepare("UPDATE producersku SET ProductID = ?, CompanyID = ?, SKU = ?, Unit = ?, PackagingTypeID = ?, Price = ?, CurrencyID = ?, ReferenceID = ? WHERE SKUID = ?");
        $stmt->bind_param("iiisdisii", $product_id, $company_id, $sku, $unit, $packaging_type_id, $price, $currency_id, $reference_id, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_producer_sku.php");
        exit;
    }
}

// Process delete requests via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM producersku WHERE SKUID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: input_producer_sku.php");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Modify Producer SKU Table</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table th,
        table td {
            text-align: center;
            vertical-align: middle;
        }

        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }
    </style>
    <script>
        function updateReferenceDetails() {
            var referenceID = document.getElementById('reference_id').value;
            var referenceDetails = document.getElementById('referenceDetails');
            var references = <?php
                                $referenceResult = $conn->query("SELECT ReferenceID, Source, Link, ProcessToObtainData, AccessDate FROM reference");
                                $references = [];
                                while ($referenceRow = $referenceResult->fetch_assoc()) {
                                    $references[$referenceRow['ReferenceID']] = $referenceRow;
                                }
                                echo json_encode($references);
                                ?>;
            if (referenceID in references) {
                var ref = references[referenceID];
                referenceDetails.innerHTML = `
                    <div class="form-group">
                        <label>Source:</label>
                        <input type="text" class="form-control" value="${ref.Source}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Link:</label>
                        <input type="text" class="form-control" value="${ref.Link}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Process To Obtain Data:</label>
                        <input type="text" class="form-control" value="${ref.ProcessToObtainData}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Access Date:</label>
                        <input type="text" class="form-control" value="${ref.AccessDate}" readonly>
                    </div>
                `;
            } else {
                referenceDetails.innerHTML = '';
            }
        }
    </script>
</head>

<body>
    <div class="container mt-5">
        <h1>Modify Producer SKU Table</h1>

        <!-- Create Form -->
        <h3>Add New SKU</h3>
        <form method="post" action="input_producer_sku.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="product_id">Product Name:</label>
                <select id="product_id" name="product_id" class="form-control" required>
                    <?php
                    $productResult = $conn->query("SELECT ProductID, ProductName FROM product ORDER BY ProductName ASC");
                    while ($productRow = $productResult->fetch_assoc()) {
                        echo "<option value='{$productRow['ProductID']}'>" . htmlspecialchars($productRow['ProductName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="company_id">Company Name:</label>
                <select id="company_id" name="company_id" class="form-control" required>
                    <?php
                    $companyResult = $conn->query("SELECT CompanyID, CompanyName FROM company ORDER BY CompanyName ASC");
                    while ($companyRow = $companyResult->fetch_assoc()) {
                        echo "<option value='{$companyRow['CompanyID']}'>" . htmlspecialchars($companyRow['CompanyName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="sku">SKU (Example: 0.5 / 1 / 5):</label>
                <input type="text" id="sku" name="sku" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="unit">Unit:</label>
                <select id="unit" name="unit" class="form-control" required>
                    <option value="Liter">Liter</option>
                    <option value="Milliliter">Milliliter</option>
                    <option value="N/A">N/A</option>
                </select>
            </div>
            <div class="form-group">
                <label for="packaging_type_id">Packaging Type:</label>
                <select id="packaging_type_id" name="packaging_type_id" class="form-control" required>
                    <?php
                    $packagingResult = $conn->query("SELECT PackagingTypeID, PackagingTypeName FROM packagingtype ORDER BY PackagingTypeName ASC");
                    while ($packagingRow = $packagingResult->fetch_assoc()) {
                        echo "<option value='{$packagingRow['PackagingTypeID']}'>" . htmlspecialchars($packagingRow['PackagingTypeName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" id="price" name="price" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="currency_id">Currency:</label>
                <select id="currency_id" name="currency_id" class="form-control" required>
                    <?php
                    $currencyResult = $conn->query("SELECT MCID, CurrencyName FROM measurecurrency ORDER BY CurrencyName ASC");
                    while ($currencyRow = $currencyResult->fetch_assoc()) {
                        echo "<option value='{$currencyRow['MCID']}'>" . htmlspecialchars($currencyRow['CurrencyName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="reference_id">Reference:</label>
                <select id="reference_id" name="reference_id" class="form-control" onchange="updateReferenceDetails()" required>
                    <option value="">Select Reference</option>
                    <?php
                    $referenceResult = $conn->query("SELECT ReferenceID, ReferenceNumber FROM reference ORDER BY ReferenceNumber ASC");
                    while ($referenceRow = $referenceResult->fetch_assoc()) {
                        echo "<option value='{$referenceRow['ReferenceID']}'>" . htmlspecialchars($referenceRow['ReferenceNumber']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div id="referenceDetails"></div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        <!-- Producer SKU Table -->
        <h2>Table: Producer SKU</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>SKU ID</th>
                        <th>Product Name</th>
                        <th>Company Name</th>
                        <th>SKU</th>
                        <th>Unit</th>
                        <th>Packaging Type</th>
                        <th>Price</th>
                        <th>Currency</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT ps.SKUID, p.ProductName, c.CompanyName, ps.SKU, ps.Unit, pt.PackagingTypeName, ps.Price, mc.CurrencyName, r.ReferenceNumber FROM producersku ps JOIN product p ON ps.ProductID = p.ProductID JOIN company c ON ps.CompanyID = c.CompanyID JOIN packagingtype pt ON ps.PackagingTypeID = pt.PackagingTypeID JOIN measurecurrency mc ON ps.CurrencyID = mc.MCID JOIN reference r ON ps.ReferenceID = r.ReferenceID ORDER BY ps.SKUID ASC");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['SKUID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CompanyName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['SKU']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Unit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['PackagingTypeName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Price']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CurrencyName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ReferenceNumber']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['SKUID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['SKUID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        <?php
        // Edit Form - show only when "edit" action is triggered
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM producersku WHERE SKUID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
        ?>
                <h2>Edit Producer SKU</h2>
                <form method="post" action="input_producer_sku.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['SKUID']); ?>">
                    <div class="form-group">
                        <label for="product_id">Product Name:</label>
                        <select id="product_id" name="product_id" class="form-control" required>
                            <?php
                            $productResult = $conn->query("SELECT ProductID, ProductName FROM product ORDER BY ProductName ASC");
                            while ($productRow = $productResult->fetch_assoc()) {
                                $selected = ($productRow['ProductID'] == $row['ProductID']) ? 'selected' : '';
                                echo "<option value='{$productRow['ProductID']}' $selected>" . htmlspecialchars($productRow['ProductName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="company_id">Company Name:</label>
                        <select id="company_id" name="company_id" class="form-control" required>
                            <?php
                            $companyResult = $conn->query("SELECT CompanyID, CompanyName FROM company ORDER BY CompanyName ASC");
                            while ($companyRow = $companyResult->fetch_assoc()) {
                                $selected = ($companyRow['CompanyID'] == $row['CompanyID']) ? 'selected' : '';
                                echo "<option value='{$companyRow['CompanyID']}' $selected>" . htmlspecialchars($companyRow['CompanyName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sku">SKU:</label>
                        <input type="text" id="sku" name="sku" class="form-control" value="<?php echo htmlspecialchars($row['SKU']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="unit">Unit:</label>
                        <select id="unit" name="unit" class="form-control" required>
                            <option value="N/A" <?php echo ($row['Unit'] == 'N/A') ? 'selected' : ''; ?>>N/A</option>
                            <option value="Liter" <?php echo ($row['Unit'] == 'Liter') ? 'selected' : ''; ?>>Liter</option>
                            <option value="Milliliter" <?php echo ($row['Unit'] == 'Milliliter') ? 'selected' : ''; ?>>Milliliter</option>

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="packaging_type_id">Packaging Type:</label>
                        <select id="packaging_type_id" name="packaging_type_id" class="form-control" required>
                            <?php
                            $packagingResult = $conn->query("SELECT PackagingTypeID, PackagingTypeName FROM packagingtype ORDER BY PackagingTypeName ASC");
                            while ($packagingRow = $packagingResult->fetch_assoc()) {
                                $selected = ($packagingRow['PackagingTypeID'] == $row['PackagingTypeID']) ? 'selected' : '';
                                echo "<option value='{$packagingRow['PackagingTypeID']}' $selected>" . htmlspecialchars($packagingRow['PackagingTypeName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="text" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($row['Price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="currency_id">Currency:</label>
                        <select id="currency_id" name="currency_id" class="form-control" required>
                            <?php
                            $currencyResult = $conn->query("SELECT MCID, CurrencyName FROM measurecurrency ORDER BY CurrencyName ASC");
                            while ($currencyRow = $currencyResult->fetch_assoc()) {
                                $selected = ($currencyRow['MCID'] == $row['CurrencyID']) ? 'selected' : '';
                                echo "<option value='{$currencyRow['MCID']}' $selected>" . htmlspecialchars($currencyRow['CurrencyName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reference_id">Reference:</label>
                        <select id="reference_id" name="reference_id" class="form-control" onchange="updateReferenceDetails()" required>
                            <option value="">Select Reference</option>
                            <?php
                            $referenceResult = $conn->query("SELECT ReferenceID, ReferenceNumber FROM reference ORDER BY ReferenceNumber ASC");
                            while ($referenceRow = $referenceResult->fetch_assoc()) {
                                $selected = ($referenceRow['ReferenceID'] == $row['ReferenceID']) ? 'selected' : '';
                                echo "<option value='{$referenceRow['ReferenceID']}' $selected>" . htmlspecialchars($referenceRow['ReferenceNumber']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div id="referenceDetails"></div>
                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                </form>
                <!-- Add space after the edit form -->
                <div class="mb-5"></div>
        <?php
            }
            $stmt->close();
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>