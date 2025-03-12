<?php
include('../db_connect.php');

// Helper to fetch dropdown options
function fetchOptions($conn, $table, $idField, $nameField) {
    $options = [];
    $result = $conn->query("SELECT $idField, $nameField FROM $table ORDER BY $nameField");
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
    return $options;
}

// Fetch dropdown data
$vehicles   = fetchOptions($conn, 'foodvehicle', 'VehicleID', 'VehicleName');
$countries  = fetchOptions($conn, 'country', 'CountryID', 'CountryName');
$foodTypes  = fetchOptions($conn, 'foodtype', 'FoodTypeID', 'FoodTypeName');
$stages     = fetchOptions($conn, 'processingstage', 'PSID', 'ProcessingStageName');
$entities   = fetchOptions($conn, 'entity', 'EntityID', 'ProducerProcessorName');
$products   = fetchOptions($conn, 'product', 'ProductID', 'ProductName');
$producers  = fetchOptions($conn, 'producerreference', 'ProducerReferenceID', 'IdentifierReferenceSystem');
$measures   = $conn->query("SELECT UCID, SupplyVolumeUnit, PeriodicalUnit FROM measureunit1 ORDER BY SupplyVolumeUnit");
$units      = [];
while ($m = $measures->fetch_assoc()) {
    $units[] = $m;
}
$references = fetchOptions($conn, 'reference', 'ReferenceID', 'ReferenceNumber');
$yearTypes  = fetchOptions($conn, 'yeartype', 'YearTypeID', 'YearTypeName');

// Fetch full reference data for showing details
$refDataResult = $conn->query("SELECT ReferenceID, ReferenceNumber, Source, Link, ProcessToObtainData, AccessDate FROM reference");
$fullRefs = [];
while ($refRow = $refDataResult->fetch_assoc()) {
    $fullRefs[$refRow['ReferenceID']] = $refRow;
}

// Handle create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $vehicleID = $_POST['VehicleID'];
        $countryID = $_POST['CountryID'];
        $foodTypeID = $_POST['FoodTypeID'];
        $psid = $_POST['PSID'];
        $origin = $_POST['Origin'];
        $entityID = $_POST['EntityID'];
        $productID = $_POST['ProductID'];
        $producerReferenceID = $_POST['ProducerReferenceID'];
        $ucid = $_POST['UCID'];
        $sourceVolume = (float)$_POST['SourceVolume'];
        $cropToFirstProcessedFoodStageConvertedValue = (float)$_POST['CropToFirstProcessedFoodStageConvertedValue'];

        // Calculate VolumeMTY based on UCID and SourceVolume
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error calculating volume: " . $conn->error . "<br>";
            exit;
        }

        // Calculate CropToFirstProcessedFoodStageConvertedValue based on conditions
        if ($countryID == 2 && $psid == 3) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 0.175;
        } elseif ($countryID == 2 && $psid == 6) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 0.15;
        } elseif ($countryID == 2 && $psid == 8) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 1;
        } else {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 1;
        }

        $yearTypeID = $_POST['YearTypeID'];
        $startYear = $_POST['StartYear'];
        $endYear = $_POST['EndYear'];
        $referenceID = $_POST['ReferenceID'];

        $sql = "INSERT INTO supply
                (VehicleID, CountryID, FoodTypeID, PSID, Origin, EntityID, ProductID, ProducerReferenceID, UCID,
                 SourceVolume, VolumeMTY, CropToFirstProcessedFoodStageConvertedValue,
                 YearTypeID, StartYear, EndYear, ReferenceID)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisiiidddiiiii", $vehicleID, $countryID, $foodTypeID, $psid, $origin, $entityID, $productID, $producerReferenceID, $ucid, $sourceVolume, $volumeMTY, $cropToFirstProcessedFoodStageConvertedValue, $yearTypeID, $startYear, $endYear, $referenceID);

        if ($stmt->execute()) {
            echo "✓ Supply record inserted successfully.<br>";
        } else {
            echo "Error inserting supply record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        header("Location: input_supply.php");
        exit;
    } else if (isset($_POST['action']) && $_POST['action'] === 'update') {
        $supplyID = $_POST['SupplyID'];
        $vehicleID = $_POST['VehicleID'];
        $countryID = $_POST['CountryID'];
        $foodTypeID = $_POST['FoodTypeID'];
        $psid = $_POST['PSID'];
        $origin = $_POST['Origin'];
        $entityID = $_POST['EntityID'];
        $productID = $_POST['ProductID'];
        $producerReferenceID = $_POST['ProducerReferenceID'];
        $ucid = $_POST['UCID'];
        $sourceVolume = (float)$_POST['SourceVolume'];
        $cropToFirstProcessedFoodStageConvertedValue = (float)$_POST['CropToFirstProcessedFoodStageConvertedValue'];

        // Calculate VolumeMTY based on UCID and SourceVolume
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error calculating volume: " . $conn->error . "<br>";
            exit;
        }

        // Calculate CropToFirstProcessedFoodStageConvertedValue based on conditions
        if ($countryID == 2 && $psid == 3) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 0.175;
        } elseif ($countryID == 2 && $psid == 6) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 0.15;
        } elseif ($countryID == 2 && $psid == 8) {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 1;
        } else {
            $cropToFirstProcessedFoodStageConvertedValue = $volumeMTY * 1;
        }

        $yearTypeID = $_POST['YearTypeID'];
        $startYear = $_POST['StartYear'];
        $endYear = $_POST['EndYear'];
        $referenceID = $_POST['ReferenceID'];

        $sql = "UPDATE supply
                SET VehicleID = ?, CountryID = ?, FoodTypeID = ?, PSID = ?, Origin = ?, EntityID = ?, ProductID = ?, ProducerReferenceID = ?, UCID = ?, SourceVolume = ?, VolumeMTY = ?, CropToFirstProcessedFoodStageConvertedValue = ?, YearTypeID = ?, StartYear = ?, EndYear = ?, ReferenceID = ?
                WHERE SupplyID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisiiidddiiiiii", $vehicleID, $countryID, $foodTypeID, $psid, $origin, $entityID, $productID, $producerReferenceID, $ucid, $sourceVolume, $volumeMTY, $cropToFirstProcessedFoodStageConvertedValue, $yearTypeID, $startYear, $endYear, $referenceID, $supplyID);

        if ($stmt->execute()) {
            echo "✓ Supply record updated successfully.<br>";
        } else {
            echo "Error updating supply record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        header("Location: input_supply.php");
        exit;
    }
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $supplyID = $_GET['id'];
    $sql = "DELETE FROM supply WHERE SupplyID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $supplyID);
    if ($stmt->execute()) {
        echo "✓ Supply record deleted successfully.<br>";
    } else {
        echo "Error deleting supply record: " . $stmt->error . "<br>";
    }
    $stmt->close();
    header("Location: input_supply.php");
    exit;
}

// Fetch supply records with LEFT JOIN for readable names
$supplyRecords = $conn->query("
    SELECT s.*,
           v.VehicleName,
           co.CountryName,
           ft.FoodTypeName,
           ps.ProcessingStageName,
           e.ProducerProcessorName,
           p.ProductName,
           pr.IdentifierReferenceSystem,
           mu.SupplyVolumeUnit,
           y.YearTypeName,
           r.ReferenceNumber
    FROM supply s
    LEFT JOIN foodvehicle v ON s.VehicleID = v.VehicleID
    LEFT JOIN country co ON s.CountryID = co.CountryID
    LEFT JOIN foodtype ft ON s.FoodTypeID = ft.FoodTypeID
    LEFT JOIN processingstage ps ON s.PSID = ps.PSID
    LEFT JOIN entity e ON s.EntityID = e.EntityID
    LEFT JOIN product p ON s.ProductID = p.ProductID
    LEFT JOIN producerreference pr ON s.ProducerReferenceID = pr.ProducerReferenceID
    LEFT JOIN measureunit1 mu ON s.UCID = mu.UCID
    LEFT JOIN yeartype y ON s.YearTypeID = y.YearTypeID
    LEFT JOIN reference r ON s.ReferenceID = r.ReferenceID
");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Input Supply</title>
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
</head>
<body>
<div class="container mt-5">
    <h1>Input Supply</h1>

    <!-- Create Form -->
    <h3>Add New Supply</h3>
    <form method="post" action="input_supply.php" class="mb-4" onsubmit="return validateYears()">
        <input type="hidden" name="action" value="create">
        <label for="VehicleID">Vehicle:</label>
        <select name="VehicleID" id="VehicleID" class="form-control">
            <?php foreach ($vehicles as $vehicle): ?>
                <option value="<?= $vehicle['VehicleID'] ?>"><?= $vehicle['VehicleName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="CountryID">Country:</label>
        <select name="CountryID" id="CountryID" class="form-control">
            <?php foreach ($countries as $country): ?>
                <option value="<?= $country['CountryID'] ?>"><?= $country['CountryName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="FoodTypeID">Food Type:</label>
        <select name="FoodTypeID" id="FoodTypeID" class="form-control">
            <?php foreach ($foodTypes as $foodType): ?>
                <option value="<?= $foodType['FoodTypeID'] ?>"><?= $foodType['FoodTypeName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="PSID">Processing Stage:</label>
        <select name="PSID" id="PSID" class="form-control">
            <?php foreach ($stages as $stage): ?>
                <option value="<?= $stage['PSID'] ?>"><?= $stage['ProcessingStageName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="Origin">Origin:</label>
        <select name="Origin" id="Origin" class="form-control">
            <option value="Local">Local</option>
            <option value="Import">Import</option>
        </select><br>

        <label for="EntityID">Entity:</label>
        <select name="EntityID" id="EntityID" class="form-control">
            <?php foreach ($entities as $entity): ?>
                <option value="<?= $entity['EntityID'] ?>"><?= $entity['ProducerProcessorName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="ProductID">Product:</label>
        <select name="ProductID" id="ProductID" class="form-control">
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['ProductID'] ?>"><?= $product['ProductName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="ProducerReferenceID">Producer Reference:</label>
        <select name="ProducerReferenceID" id="ProducerReferenceID" class="form-control">
            <?php foreach ($producers as $producer): ?>
                <option value="<?= $producer['ProducerReferenceID'] ?>"><?= $producer['IdentifierReferenceSystem'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="UCID">Supply Volume Unit:</label>
        <select name="UCID" id="UCID" class="form-control">
            <?php foreach ($units as $unit): ?>
                <option value="<?= $unit['UCID'] ?>"><?= $unit['SupplyVolumeUnit'] ?> / <?= $unit['PeriodicalUnit'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="SourceVolume">Source Volume:</label>
        <input type="text" name="SourceVolume" id="SourceVolume" class="form-control"><br>

        <label for="VolumeMTY">Volume (MT/Y):</label>
        <input type="text" id="VolumeMTY" class="form-control" readonly value=""><br>

        <label for="CropToFirstProcessedFoodStageConvertedValue">Crop to First Processed Food Stage Converted Value:</label>
        <input type="text" name="CropToFirstProcessedFoodStageConvertedValue" id="CropToFirstProcessedFoodStageConvertedValue" class="form-control" readonly><br>

        <label for="YearTypeID">Year Type:</label>
        <select name="YearTypeID" id="YearTypeID" class="form-control">
            <?php foreach ($yearTypes as $yearType): ?>
                <option value="<?= $yearType['YearTypeID'] ?>"><?= $yearType['YearTypeName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="StartYear">Start Year:</label>
        <input type="text" name="StartYear" id="StartYear" class="form-control"><br>

        <label for="EndYear">End Year:</label>
        <input type="text" name="EndYear" id="EndYear" class="form-control"><br>

        <label for="ReferenceID">Reference:</label>
        <select name="ReferenceID" id="ReferenceID" class="form-control" onchange="updateReferenceDetails()">
            <?php foreach ($references as $reference): ?>
                <option value="<?= $reference['ReferenceID'] ?>"><?= $reference['ReferenceNumber'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <div id="referenceDetails" style="display:none;"></div>

        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>
    <div class="mb-5"></div>

    <!-- Existing Records Table -->
    <h2>Supply Records</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Vehicle</th>
                <th>Country</th>
                <th>Food Type</th>
                <th>Processing Stage</th>
                <th>Origin</th>
                <th>Entity</th>
                <th>Product</th>
                <th>Producer Reference</th>
                <th>Supply Volume Unit</th>
                <th>Source Volume</th>
                <th>Volume (MT/Y)</th>
                <th>Crop to First Processed Food Stage Converted Value</th>
                <th>Year Type</th>
                <th>Start Year</th>
                <th>End Year</th>
                <th>Reference</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $supplyRecords->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['SupplyID'] ?></td>
                    <td><?= $row['VehicleName'] ?></td>
                    <td><?= $row['CountryName'] ?></td>
                    <td><?= $row['FoodTypeName'] ?></td>
                    <td><?= $row['ProcessingStageName'] ?></td>
                    <td><?= $row['Origin'] ?></td>
                    <td><?= $row['ProducerProcessorName'] ?></td>
                    <td><?= $row['ProductName'] ?></td>
                    <td><?= $row['IdentifierReferenceSystem'] ?></td>
                    <td><?= $row['SupplyVolumeUnit'] ?></td>
                    <td><?= $row['SourceVolume'] ?></td>
                    <td><?= $row['VolumeMTY'] ?></td>
                    <td><?= $row['CropToFirstProcessedFoodStageConvertedValue'] ?></td>
                    <td><?= $row['YearTypeName'] ?></td>
                    <td><?= $row['StartYear'] ?></td>
                    <td><?= $row['EndYear'] ?></td>
                    <td><?= $row['ReferenceNumber'] ?></td>
                    <td>
                        <a href="?action=edit&id=<?= $row['SupplyID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?action=delete&id=<?= $row['SupplyID'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this record?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="mb-5"></div>

    <!-- Edit Form -->
    <?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])): ?>
        <?php
        $supplyID = $_GET['id'];
        $result = $conn->query("SELECT * FROM supply WHERE SupplyID = $supplyID");
        if ($row = $result->fetch_assoc()):
        ?>
            <h3>Edit Supply</h3>
            <form method="post" action="input_supply.php" class="mb-4" onsubmit="return validateYears()">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="SupplyID" value="<?= $row['SupplyID'] ?>">
                <label for="VehicleID">Vehicle:</label>
                <select name="VehicleID" id="VehicleID" class="form-control">
                    <?php foreach ($vehicles as $vehicle): ?>
                        <option value="<?= $vehicle['VehicleID'] ?>" <?= $vehicle['VehicleID'] == $row['VehicleID'] ? 'selected' : '' ?>><?= $vehicle['VehicleName'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="CountryID">Country:</label>
                <select name="CountryID" id="CountryID" class="form-control">
                    <?php foreach ($countries as $country): ?>
                        <option value="<?= $country['CountryID'] ?>" <?= $country['CountryID'] == $row['CountryID'] ? 'selected' : '' ?>><?= $country['CountryName'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="FoodTypeID">Food Type:</label>
                <select name="FoodTypeID" id="FoodTypeID" class="form-control">
                    <?php foreach ($foodTypes as $foodType): ?>
                        <option value="<?= $foodType['FoodTypeID'] ?>" <?= $foodType['FoodTypeID'] == $row['FoodTypeID'] ? 'selected' : '' ?>><?= $foodType['FoodTypeName'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="PSID">Processing Stage:</label>
                <select name="PSID" id="PSID" class="form-control">
                    <?php foreach ($stages as $stage): ?>
                        <option value="<?= $stage['PSID'] ?>" <?= $stage['PSID'] == $row['PSID'] ? 'selected' : '' ?>><?= $stage['ProcessingStageName'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="Origin">Origin:</label>
                <select name="Origin" id="Origin" class="form-control">
                    <option value="Local" <?= $row['Origin'] == 'Local' ? 'selected' : '' ?>>Local</option>
                    <option value="Import" <?= $row['Origin'] == 'Import' ? 'selected' : '' ?>>Import</option>
                </select><br>

                <label for="EntityID">Entity:</label>
                <select name="EntityID" id="EntityID" class="form-control">
                    <?php foreach ($entities as $entity): ?>
                        <option value="<?= $entity['EntityID'] ?>" <?= $entity['EntityID'] == $row['EntityID'] ? 'selected' : '' ?>><?= $entity['ProducerProcessorName'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="ProductID">Product:</label>
                <select name="ProductID" id="ProductID" class="form-control">
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['ProductID'] ?>" <?= $product['ProductID'] == $row['ProductID'] ? 'selected' : '' ?>><?= $product['ProductName'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="ProducerReferenceID">Producer Reference:</label>
                <select name="ProducerReferenceID" id="ProducerReferenceID" class="form-control">
                    <?php foreach ($producers as $producer): ?>
                        <option value="<?= $producer['ProducerReferenceID'] ?>" <?= $producer['ProducerReferenceID'] == $row['ProducerReferenceID'] ? 'selected' : '' ?>><?= $producer['IdentifierReferenceSystem'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="UCID">Supply Volume Unit:</label>
                <select name="UCID" id="UCIDEdit" class="form-control">
                    <?php foreach ($units as $unit): ?>
                        <option value="<?= $unit['UCID'] ?>" <?= $unit['UCID'] == $row['UCID'] ? 'selected' : '' ?>>
                            <?= $unit['SupplyVolumeUnit'] ?> / <?= $unit['PeriodicalUnit'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="SourceVolume">Source Volume:</label>
                <input type="text" name="SourceVolume" id="SourceVolumeEdit" class="form-control" value="<?= $row['SourceVolume'] ?>"><br>

                <label for="VolumeMTY">Volume (MT/Y):</label>
                <input type="text" id="VolumeMTYEdit" class="form-control" readonly value="<?= htmlspecialchars(number_format($row['VolumeMTY'], 6)) ?>"><br>

                <label for="CropToFirstProcessedFoodStageConvertedValue">Crop to First Processed Food Stage Converted Value:</label>
                <input type="text" name="CropToFirstProcessedFoodStageConvertedValue" id="CropToFirstProcessedFoodStageConvertedValue" class="form-control" readonly value="<?= $row['CropToFirstProcessedFoodStageConvertedValue'] ?>"><br>

                <label for="YearTypeID">Year Type:</label>
                <select name="YearTypeID" id="YearTypeID" class="form-control">
                    <?php foreach ($yearTypes as $yearType): ?>
                        <option value="<?= $yearType['YearTypeID'] ?>" <?= $yearType['YearTypeID'] == $row['YearTypeID'] ? 'selected' : '' ?>><?= $yearType['YearTypeName'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <label for="StartYear">Start Year:</label>
                <input type="text" name="StartYear" id="StartYear" class="form-control" value="<?= $row['StartYear'] ?>"><br>

                <label for="EndYear">End Year:</label>
                <input type="text" name="EndYear" id="EndYear" class="form-control" value="<?= $row['EndYear'] ?>"><br>

                <label for="ReferenceID">Reference:</label>
                <select name="ReferenceID" id="ReferenceID" class="form-control" onchange="updateReferenceDetails()">
                    <?php foreach ($references as $reference): ?>
                        <option value="<?= $reference['ReferenceID'] ?>" <?= $reference['ReferenceID'] == $row['ReferenceID'] ? 'selected' : '' ?>><?= $reference['ReferenceNumber'] ?></option>
                    <?php endforeach; ?>
                </select><br>

                <div id="referenceDetails" style="display:none;"></div>

                <button type="submit" class="btn btn-primary mt-2">Update</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Build references object in JS
    var references = <?php echo json_encode($fullRefs); ?>;

    function updateReferenceDetails(refID) {
        var referenceID = refID || document.getElementById('ReferenceID').value;
        var referenceDetails = document.getElementById('referenceDetails');
        if (referenceID && referenceID in references) {
            var ref = references[referenceID];
            referenceDetails.style.display = 'block';
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
            referenceDetails.style.display = 'none';
            referenceDetails.innerHTML = '';
        }
    }

    function editRecord(record) {
        document.getElementById("SupplyID").value = record.SupplyID;
        document.getElementById("VehicleID").value = record.VehicleID;
        document.getElementById("CountryID").value = record.CountryID;
        document.getElementById("FoodTypeID").value = record.FoodTypeID;
        document.getElementById("PSID").value = record.PSID;
        document.getElementById("Origin").value = record.Origin;
        document.getElementById("EntityID").value = record.EntityID;
        document.getElementById("ProductID").value = record.ProductID;
        document.getElementById("ProducerReferenceID").value = record.ProducerReferenceID;
        document.getElementById("UCID").value = record.UCID;
        document.getElementById("SourceVolume").value = record.SourceVolume;
        document.getElementById("VolumeMTY").value = record.VolumeMTY;
        document.getElementById("CropToFirstProcessedFoodStageConvertedValue").value = record.CropToFirstProcessedFoodStageConvertedValue;
        document.getElementById("YearTypeID").value = record.YearTypeID;
        document.getElementById("StartYear").value = record.StartYear;
        document.getElementById("EndYear").value = record.EndYear;
        document.getElementById("ReferenceID").value = record.ReferenceID;
        updateReferenceDetails(record.ReferenceID);
    }

    <?php if (isset($_GET['ReferenceID'])): ?>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('ReferenceID').value = <?= intval($_GET['ReferenceID']) ?>;
            updateReferenceDetails(<?= intval($_GET['ReferenceID']) ?>);
        });
    <?php endif; ?>

    // Build a simple list of UCID => UnitValue from the $units array
    var unitValues = {
        <?php
        $countUnits = count($units);
        foreach ($units as $index => $unit) {
            // wrap key in quotes, and avoid trailing comma
            echo "\"{$unit['UCID']}\": " . (float) $unit['UnitValue'];
            if ($index < $countUnits - 1) {
                echo ",";
            }
            echo "\n";
        }
        ?>}

    function computeValues() {
        var source = parseFloat(document.getElementById('SourceVolume').value) || 0;
        var ucid = parseInt(document.getElementById('UCID').value) || 0;
        var psid = parseInt(document.getElementById('PSID').value) || 0;
        var countryID = parseInt(document.getElementById('CountryID').value) || 0;
        var unitVal = unitValues[ucid] || 0;
        var volumeMTY = source * unitVal;
        document.getElementById('VolumeMTY').value = volumeMTY ? volumeMTY.toFixed(6) : '';
        var c2fVal = 0;
        if (countryID === 2 && psid === 3) {
            c2fVal = volumeMTY * 0.175;
        } else if (countryID === 2 && psid === 6) {
            c2fVal = volumeMTY * 0.15;
        } else if (countryID === 2 && psid === 8) {
            c2fVal = volumeMTY;
        } else {
            c2fVal = volumeMTY;
        }
        document.getElementById('CropToFirstProcessedFoodStageConvertedValue').value = c2fVal ? c2fVal.toFixed(6) : '';
    }

    // Recompute values when relevant fields change
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('SourceVolume').addEventListener('input', computeValues);
        document.getElementById('UCID').addEventListener('change', computeValues);
        document.getElementById('PSID').addEventListener('change', computeValues);
        document.getElementById('CountryID').addEventListener('change', computeValues);
    });

    document.addEventListener("DOMContentLoaded", function() {
        // ...existing code...
        var sourceVolumeEditEl = document.getElementById('SourceVolumeEdit');
        var ucidEditEl = document.getElementById('UCIDEdit');
        var volumeMtyEditEl = document.getElementById('VolumeMTYEdit');

        function computeVolumeEdit() {
            var source = parseFloat(sourceVolumeEditEl.value) || 0;
            var ucid = ucidEditEl.value;
            var unitVal = unitValues[ucid] || 0;
            var volume = source * unitVal;
            volumeMtyEditEl.value = volume ? volume.toFixed(6) : '';
        }
        if (sourceVolumeEditEl && ucidEditEl && volumeMtyEditEl) {
            sourceVolumeEditEl.addEventListener('input', computeVolumeEdit);
            ucidEditEl.addEventListener('change', computeVolumeEdit);
        }
    });

    function validateYears() {
        var startYear = parseInt(document.getElementById('StartYear').value) || 0;
        var endYear = parseInt(document.getElementById('EndYear').value) || 0;
        if (startYear > endYear) {
            alert("End Year must be equal / greater than Start Year");
            return false;
        }
        return true;
    }
</script>
</body>
</html>