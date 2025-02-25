<?php
// input_table.php
// Include the database connection
include('db_connect.php');

// Fetch all table names
$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
} else {
    die("Error fetching tables: " . $conn->error);
}

// Fetch column names for the selected table
$columns = [];
$primaryKey = '';
$nextId = '';
$tableName = $_GET['table'] ?? '';
if (!empty($tableName)) {
    $result = $conn->query("SHOW COLUMNS FROM `" . $conn->real_escape_string($tableName) . "`");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
            if ($row['Key'] == 'PRI') {
                $primaryKey = $row['Field'];
            }
        }
    } else {
        die("Error fetching columns: " . $conn->error);
    }

    // Fetch the latest ID + 1 for the primary key
    if (!empty($primaryKey)) {
        $result = $conn->query("SELECT MAX(`" . $primaryKey . "`) AS max_id FROM `" . $conn->real_escape_string($tableName) . "`");
        if ($result) {
            $row = $result->fetch_assoc();
            $nextId = $row['max_id'] + 1;
        } else {
            die("Error fetching next ID: " . $conn->error);
        }
    }
}

// Handle form submission
$successMessage = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $tableName = $_POST['tableName'];
    $data = [];
    foreach ($columns as $column) {
        if ($column != $primaryKey && $column != 'Volume_MT_Y' && $column != 'AnnualProductionSupplyVolumeMTY' && $column != 'VolumeMTY') {
            $data[$column] = $_POST[$column] ?? '';
        }
    }

    // Ensure CountryID exists in the country table, but skip this check for the country table itself
    if ($tableName != 'country' && in_array('CountryID', $columns)) {
        $countryID = $_POST['CountryID'] ?? '';
        $countryCheckQuery = "SELECT CountryID FROM country WHERE CountryID = '" . $conn->real_escape_string($countryID) . "'";
        $countryCheckResult = $conn->query($countryCheckQuery);
        if ($countryCheckResult->num_rows == 0) {
            echo "<div class='alert alert-danger'>Error: CountryID '" . htmlspecialchars($countryID) . "' does not exist in the country table.</div>";
            exit;
        }
    }

    // Calculate VolumeMTY for consumption table
    if ($tableName == 'consumption') {
        $ucid = $_POST['UCID'] ?? '';
        $sourceVolume = (float)$_POST['SourceVolume'] ?? 0;
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $data['VolumeMTY'] = $sourceVolume * $unitValue;
        } else {
            echo "<div class='alert alert-danger'>Error: Invalid UCID $ucid.</div>";
            exit;
        }
    }

    // Calculate Volume_MT_Y for distribution table
    if ($tableName == 'distribution') {
        $ucid = $_POST['UCID'] ?? '';
        $sourceVolume = (float)$_POST['SourceVolume'] ?? 0;
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $data['Volume_MT_Y'] = $sourceVolume * $unitValue;
        } else {
            echo "<div class='alert alert-danger'>Error: Invalid UCID $ucid.</div>";
            exit;
        }
    }

    // Calculate AnnualProductionSupplyVolumeMTY for producerprocessor table
    if ($tableName == 'producerprocessor') {
        $productionCapacityVolumeMTY = (float)$_POST['ProductionCapacityVolumeMTY'] ?? 0;
        $percentageOfCapacityUsed = (float)$_POST['PercentageOfCapacityUsed'] ?? 0;
        $data['AnnualProductionSupplyVolumeMTY'] = ($productionCapacityVolumeMTY * $percentageOfCapacityUsed) / 100;
    }

    // Insert new record
    $columnsEscaped = array_map(function ($col) use ($conn) {
        return "`" . $conn->real_escape_string($col) . "`";
    }, array_keys($data));
    $valuesEscaped = array_map([$conn, 'real_escape_string'], array_values($data));
    $insertSQL = "INSERT INTO `" . $conn->real_escape_string($tableName) . "` (" . implode(", ", $columnsEscaped) . ") VALUES ('" . implode("', '", $valuesEscaped) . "')";
    if ($conn->query($insertSQL) === TRUE) {
        $successMessage = "New record created successfully.";
        // Reset tableName to show 'Select a table' option again
        $tableName = '';
        $columns = [];
    } else {
        echo "Error creating record: " . $conn->error;
    }
}

// Fetch company group if entityID is provided
$companyGroup = '';
$entityID = $_GET['entityID'] ?? '';
if (!empty($entityID)) {
    $entityID = $conn->real_escape_string($entityID);
    $result = $conn->query("SELECT CompanyGroup FROM entities WHERE EntityID = '$entityID'");
    if ($result) {
        $row = $result->fetch_assoc();
        $companyGroup = $row['CompanyGroup'];
    } else {
        $companyGroup = "Error fetching company group: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Table Data</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateForm() {
            const tableName = document.getElementById('tableName').value;
            window.location.href = 'input_table.php?table=' + tableName;
        }

        function updateCompanyGroup() {
            const entityID = document.getElementById('EntityID').value;
            window.location.href = 'input_table.php?table=producer_processor&entityID=' + entityID;
        }

        function calculateVolumeMTY() {
            const ucid = document.getElementById('UCID').value;
            const sourceVolume = parseFloat(document.getElementById('SourceVolume').value) || 0;
            if (ucid && sourceVolume) {
                fetch(`get_unit_value.php?ucid=${ucid}`)
                    .then(response => response.json())
                    .then(data => {
                        const unitValue = parseFloat(data.UnitValue) || 0;
                        const volumeMTY = sourceVolume * unitValue;
                        document.getElementById('VolumeMTY').value = volumeMTY.toFixed(2);
                    });
            }
        }

        function calculateAnnualProductionSupplyVolumeMTY() {
            const productionCapacityVolumeMTY = parseFloat(document.getElementById('ProductionCapacityVolumeMTY').value) || 0;
            const percentageOfCapacityUsed = parseFloat(document.getElementById('PercentageOfCapacityUsed').value) || 0;
            const annualProductionSupplyVolumeMTY = (productionCapacityVolumeMTY * percentageOfCapacityUsed) / 100;
            document.getElementById('AnnualProductionSupplyVolumeMTY').value = annualProductionSupplyVolumeMTY.toFixed(2);
        }

        function updateReferenceDetails() {
            const referenceID = document.getElementById('ReferenceID').value;
            if (referenceID) {
                fetch(`get_reference_details.php?referenceID=${referenceID}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('ReferenceNumber').value = data.ReferenceNumber;
                        document.getElementById('Source').value = data.Source;
                        document.getElementById('Link').value = data.Link;
                        document.getElementById('ProcessToObtainData').value = data.ProcessToObtainData;
                        document.getElementById('AccessDate').value = data.AccessDate;
                    });
            }
        }
    </script>
</head>

<body>
    <div class="container">
        <br><br><br>
        <h1 class="center-title">Input Data for Table</h1>
        <form method="post" action="">
            <div class="mb-3">
                <label for="tableName" class="form-label">Select a table</label>
                <select id="tableName" name="tableName" class="form-control" onchange="updateForm()">
                    <option value="">Select a table</option>
                    <?php
                    foreach ($tables as $table): ?>
                        <option value="<?php echo htmlspecialchars($table); ?>" <?php echo ($table == $tableName) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($table); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($columns)): ?>
                <?php foreach ($columns as $column): 
                    $label = htmlspecialchars($column);
                    $isForeignKey = false;
                    $foreignKeyTable = '';
                    $foreignKeyDisplayColumn = '';

                    if ($column == 'VehicleID') {
                        $label = 'Vehicle Name';
                        $isForeignKey = true;
                        $foreignKeyTable = 'foodvehicle';
                        $foreignKeyDisplayColumn = 'VehicleName';
                    } elseif ($column == 'FoodTypeID') {
                        $label = 'Food Type Name';
                        $isForeignKey = true;
                        $foreignKeyTable = 'foodtype';
                        $foreignKeyDisplayColumn = 'FoodTypeName';
                    } elseif ($column == 'GL1ID') {
                        $label = 'Admin Level 1';
                        $isForeignKey = true;
                        $foreignKeyTable = 'geographylevel1';
                        $foreignKeyDisplayColumn = 'AdminLevel1';
                    } elseif ($column == 'GL2ID') {
                        $label = 'Admin Level 2';
                        $isForeignKey = true;
                        $foreignKeyTable = 'geographylevel2';
                        $foreignKeyDisplayColumn = 'AdminLevel2';
                    } elseif ($column == 'GL3ID') {
                        $label = 'Admin Level 3';
                        $isForeignKey = true;
                        $foreignKeyTable = 'geographylevel3';
                        $foreignKeyDisplayColumn = 'AdminLevel3';
                    } elseif ($column == 'GenderID') {
                        $label = 'Gender Name';
                        $isForeignKey = true;
                        $foreignKeyTable = 'gender';
                        $foreignKeyDisplayColumn = 'GenderName';
                    } elseif ($column == 'AgeID') {
                        $label = 'Age Range';
                        $isForeignKey = true;
                        $foreignKeyTable = 'age';
                        $foreignKeyDisplayColumn = 'AgeRange';
                    } elseif ($column == 'ProducerReferenceID' && $tableName == 'producerprocessor') {
                        $label = 'Producer Reference';
                        $isForeignKey = true;
                        $foreignKeyTable = 'producerreference';
                        $foreignKeyDisplayColumn = 'IdentifierNumber';
                    }
                    ?>
                    <div class="mb-3">
                        <label for="<?php echo htmlspecialchars($column); ?>" class="form-label"><?php echo $label; ?></label>
                        <?php if ($column == $primaryKey): ?>
                            <input type="text" name="<?php echo htmlspecialchars($column); ?>" class="form-control" value="<?php echo htmlspecialchars($nextId); ?>" readonly>
                        <?php elseif ($column == 'VolumeMTY' && $tableName == 'consumption'): ?>
                            <input type="text" name="<?php echo htmlspecialchars($column); ?>" id="VolumeMTY" class="form-control" readonly style="color: darkgray;">
                        <?php elseif ($isForeignKey && $column == 'ProducerReferenceID' && $tableName == 'producerprocessor'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" id="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $fkResult = $conn->query("SELECT ProducerReferenceID, IdentifierNumber FROM producerreference ORDER BY IdentifierNumber ASC");
                                while ($fkRow = $fkResult->fetch_assoc()) {
                                    echo "<option value='{$fkRow['ProducerReferenceID']}'>{$fkRow['IdentifierNumber']}</option>";
                                }
                                ?>
                            </select>
                            <?php
                            // Fetch and display related details
                            $producerReferenceID = $_POST['ProducerReferenceID'] ?? '';
                            if (!empty($producerReferenceID)) {
                                $detailsQuery = "SELECT c.CompanyName, pr.IdentifierNumber, pr.IdentifierReferenceSystem, co.CountryName 
                                                 FROM producerreference pr
                                                 JOIN company c ON pr.CompanyID = c.CompanyID
                                                 JOIN country co ON pr.CountryID = co.CountryID
                                                 WHERE pr.ProducerReferenceID = '" . $conn->real_escape_string($producerReferenceID) . "'";
                                $detailsResult = $conn->query($detailsQuery);
                                if ($detailsResult && $detailsResult->num_rows > 0) {
                                    $detailsRow = $detailsResult->fetch_assoc();
                                    echo "<div class='mt-2'>";
                                    echo "<p>Company Name: <input type='text' class='form-control-plaintext' value='" . htmlspecialchars($detailsRow['CompanyName']) . "' readonly></p>";
                                    echo "<p>Identifier Number: <input type='text' class='form-control-plaintext' value='" . htmlspecialchars($detailsRow['IdentifierNumber']) . "' readonly></p>";
                                    echo "<p>Identifier Reference System: <input type='text' class='form-control-plaintext' value='" . htmlspecialchars($detailsRow['IdentifierReferenceSystem']) . "' readonly></p>";
                                    echo "<p>Country Name: <input type='text' class='form-control-plaintext' value='" . htmlspecialchars($detailsRow['CountryName']) . "' readonly></p>";
                                    echo "</div>";
                                }
                            }
                            ?>
                        <?php elseif ($isForeignKey): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" id="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $fkResult = $conn->query("SELECT {$column}, {$foreignKeyDisplayColumn} FROM {$foreignKeyTable} ORDER BY {$foreignKeyDisplayColumn} ASC");
                                while ($fkRow = $fkResult->fetch_assoc()) {
                                    echo "<option value='{$fkRow[$column]}'>{$fkRow[$foreignKeyDisplayColumn]}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'CountryID'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $result = $conn->query("SELECT CountryID, CountryName FROM country ORDER BY CountryName ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['CountryID']}'>{$row['CountryName']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'VehicleID'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $result = $conn->query("SELECT VehicleID, VehicleName FROM foodvehicle ORDER BY VehicleName ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['VehicleID']}'>{$row['VehicleName']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'DistributionChannelID'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $result = $conn->query("SELECT DistributionChannelID, DistributionChannelName FROM distributionchannel ORDER BY DistributionChannelName ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['DistributionChannelID']}'>{$row['DistributionChannelName']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'SubDistributionChannelID'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $result = $conn->query("SELECT SubDistributionChannelID, SubDistributionChannelName FROM subdistributionchannel ORDER BY SubDistributionChannelName ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['SubDistributionChannelID']}'>{$row['SubDistributionChannelName']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'UCID'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" id="UCID" class="form-control" onchange="calculateVolumeMTY()">
                                <?php
                                $result = $conn->query("SELECT UCID, SupplyVolumeUnit, PeriodicalUnit FROM measureunit1 ORDER BY SupplyVolumeUnit ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['UCID']}'>{$row['SupplyVolumeUnit']} / {$row['PeriodicalUnit']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'YearTypeID'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $result = $conn->query("SELECT YearTypeID, YearTypeName FROM yeartype ORDER BY YearTypeName ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['YearTypeID']}'>{$row['YearTypeName']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'ReferenceID'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" id="ReferenceID" class="form-control" onchange="updateReferenceDetails()">
                                <?php
                                $result = $conn->query("SELECT ReferenceID, ReferenceNumber FROM reference ORDER BY ReferenceNumber ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['ReferenceID']}'>{$row['ReferenceNumber']}</option>";
                                }
                                ?>
                            </select>
                            <input type="text" id="ReferenceNumber" class="form-control mt-2" readonly style="color: darkgray;" placeholder="Reference Number">
                            <input type="text" id="Source" class="form-control mt-2" readonly style="color: darkgray;" placeholder="Source">
                            <input type="text" id="Link" class="form-control mt-2" readonly style="color: darkgray;" placeholder="Link">
                            <input type="text" id="ProcessToObtainData" class="form-control mt-2" readonly style="color: darkgray;" placeholder="Process To Obtain Data">
                            <input type="text" id="AccessDate" class="form-control mt-2" readonly style="color: darkgray;" placeholder="Access Date">
                        <?php elseif ($column == 'EntityID' && $tableName == 'producerprocessor'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" id="EntityID" class="form-control">
                                <?php
                                $result = $conn->query("SELECT EntityID, ProducerProcessorName FROM entity ORDER BY ProducerProcessorName ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['EntityID']}'>{$row['ProducerProcessorName']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'GL1ID' && $tableName == 'geographylevel2'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $result = $conn->query("SELECT GL1ID, AdminLevel1 FROM geographylevel1 ORDER BY AdminLevel1 ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['GL1ID']}'>{$row['AdminLevel1']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'GL2ID' && $tableName == 'geographylevel3'): ?>
                            <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                <?php
                                $result = $conn->query("SELECT GL2ID, AdminLevel2 FROM geographylevel2 ORDER BY AdminLevel2 ASC");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['GL2ID']}'>{$row['AdminLevel2']}</option>";
                                }
                                ?>
                            </select>
                        <?php elseif ($column == 'SourceVolume' && $tableName == 'distribution'): ?>
                            <input type="text" name="<?php echo htmlspecialchars($column); ?>" id="SourceVolume" class="form-control" oninput="calculateVolumeMTY()">
                        <?php elseif ($column == 'Volume_MT_Y' && $tableName == 'distribution'): ?>
                            <input type="text" name="<?php echo htmlspecialchars($column); ?>" id="Volume_MT_Y" class="form-control" readonly style="color: darkgray;">
                        <?php elseif ($column == 'ProductionCapacityVolumeMTY' && $tableName == 'producerprocessor'): ?>
                            <input type="text" name="<?php echo htmlspecialchars($column); ?>" id="ProductionCapacityVolumeMTY" class="form-control" oninput="calculateAnnualProductionSupplyVolumeMTY()">
                        <?php elseif ($column == 'PercentageOfCapacityUsed' && $tableName == 'producerprocessor'): ?>
                            <input type="text" name="<?php echo htmlspecialchars($column); ?>" id="PercentageOfCapacityUsed" class="form-control" oninput="calculateAnnualProductionSupplyVolumeMTY()">
                        <?php elseif ($column == 'AnnualProductionSupplyVolumeMTY' && $tableName == 'producerprocessor'): ?>
                            <input type="text" name="<?php echo htmlspecialchars($column); ?>" id="AnnualProductionSupplyVolumeMTY" class="form-control" readonly style="color: darkgray;">
                        <?php elseif ($column == 'AccessDate'): ?>
                            <input type="date" name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                        <?php else: ?>
                            <input type="text" name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="mb-3">
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <!-- Optional JavaScript and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>