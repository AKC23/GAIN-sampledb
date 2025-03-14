<?php
include('../db_connect.php');

function fetchOptions($conn, $table, $idField, $nameField)
{
    $options = [];
    $result = $conn->query("SELECT $idField, $nameField FROM $table ORDER BY $nameField");
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
    return $options;
}

// Fetch foreign key dropdown data
$vehicles = fetchOptions($conn, 'foodvehicle', 'VehicleID', 'VehicleName');
$levels1 = fetchOptions($conn, 'geographylevel1', 'GL1ID', 'AdminLevel1');
$levels2 = fetchOptions($conn, 'geographylevel2', 'GL2ID', 'AdminLevel2');
$levels3 = fetchOptions($conn, 'geographylevel3', 'GL3ID', 'AdminLevel3');
$genders = fetchOptions($conn, 'gender', 'GenderID', 'GenderName');
$ages = fetchOptions($conn, 'age', 'AgeID', 'AgeRange');
$units = [];
$resultUnits = $conn->query("SELECT UCID, SupplyVolumeUnit, PeriodicalUnit, UnitValue FROM measureunit1 ORDER BY SupplyVolumeUnit");
while ($rowUnits = $resultUnits->fetch_assoc()) {
    $units[] = $rowUnits;
}
$yearTypes = fetchOptions($conn, 'yeartype', 'YearTypeID', 'YearTypeName');
$references = fetchOptions($conn, 'reference', 'ReferenceID', 'ReferenceNumber');

// Also build reference details array if needed
$refDataResult = $conn->query("SELECT ReferenceID, ReferenceNumber, Source, Link, ProcessToObtainData, AccessDate FROM reference");
$fullRefs = [];
while ($refRow = $refDataResult->fetch_assoc()) {
    $fullRefs[$refRow['ReferenceID']] = $refRow;
}

// Handle create/update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $vehicleID = $_POST['VehicleID'];
        $gl1ID = (!empty($_POST['GL1ID'])) ? (int)$_POST['GL1ID'] : null;
        $gl2ID = (!empty($_POST['GL2ID'])) ? (int)$_POST['GL2ID'] : null;
        $gl3ID = (!empty($_POST['GL3ID'])) ? (int)$_POST['GL3ID'] : null;
        $genderID = $_POST['GenderID'];
        $ageID = $_POST['AgeID'];
        $numberOfPeople = (int)$_POST['NumberOfPeople'];
        $sourceVolume = (float)$_POST['SourceVolume'];
        $ucid = $_POST['UCID'];

        // Calculate VolumeMTY based on UCID and SourceVolume
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error calculating volume: " . $conn->error . "<br>";
            exit;
        }

        $yearTypeID = $_POST['YearTypeID'];
        $startYear = $_POST['StartYear'];
        $endYear = $_POST['EndYear'];
        $referenceID = $_POST['ReferenceID'];

        $sql = "INSERT INTO consumption (VehicleID, GL1ID, GL2ID, GL3ID, GenderID, AgeID, NumberOfPeople, SourceVolume, VolumeMTY, UCID, YearTypeID, StartYear, EndYear, ReferenceID)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiiiidiiisii", $vehicleID, $gl1ID, $gl2ID, $gl3ID, $genderID, $ageID, $numberOfPeople, $sourceVolume, $volumeMTY, $ucid, $yearTypeID, $startYear, $endYear, $referenceID);

        if ($stmt->execute()) {
            echo "✓ Consumption record inserted successfully.<br>";
        } else {
            echo "Error inserting consumption record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        header("Location: input_consumption.php");
        exit;
    }
    // UPDATE
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $consumptionID = $_POST['ConsumptionID'];
        $vehicleID = $_POST['VehicleID'];
        $gl1ID = (!empty($_POST['GL1ID'])) ? (int)$_POST['GL1ID'] : null;
        $gl2ID = (!empty($_POST['GL2ID'])) ? (int)$_POST['GL2ID'] : null;
        $gl3ID = (!empty($_POST['GL3ID'])) ? (int)$_POST['GL3ID'] : null;
        $genderID = $_POST['GenderID'];
        $ageID = $_POST['AgeID'];
        $numberOfPeople = (int)$_POST['NumberOfPeople'];
        $sourceVolume = (float)$_POST['SourceVolume'];
        $ucid = $_POST['UCID'];

        // Calculate VolumeMTY based on UCID and SourceVolume
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error calculating volume: " . $conn->error . "<br>";
            exit;
        }

        $yearTypeID = $_POST['YearTypeID'];
        $startYear = $_POST['StartYear'];
        $endYear = $_POST['EndYear'];
        $referenceID = $_POST['ReferenceID'];

        $sql = "UPDATE consumption
                SET VehicleID = ?, GL1ID = ?, GL2ID = ?, GL3ID = ?, GenderID = ?, AgeID = ?, NumberOfPeople = ?, SourceVolume = ?, VolumeMTY = ?, UCID = ?, YearTypeID = ?, StartYear = ?, EndYear = ?, ReferenceID = ?
                WHERE ConsumptionID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiiiidiiisiii", $vehicleID, $gl1ID, $gl2ID, $gl3ID, $genderID, $ageID, $numberOfPeople, $sourceVolume, $volumeMTY, $ucid, $yearTypeID, $startYear, $endYear, $referenceID, $consumptionID);

        if ($stmt->execute()) {
            echo "✓ Consumption record updated successfully.<br>";
        } else {
            echo "Error updating consumption record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        header("Location: input_consumption.php");
        exit;
    }
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $consumptionID = $_GET['id'];
    $sql = "DELETE FROM consumption WHERE ConsumptionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $consumptionID);
    if ($stmt->execute()) {
        echo "✓ Consumption record deleted successfully.<br>";
    } else {
        echo "Error deleting consumption record: " . $stmt->error . "<br>";
    }
    $stmt->close();
    header("Location: input_consumption.php");
    exit;
}

// Fetch with JOIN
$consumptions = $conn->query("
    SELECT c.*,
           v.VehicleName,
           gl1.AdminLevel1,
           gl2.AdminLevel2,
           gl3.AdminLevel3,
           g.GenderName,
           a.AgeRange,
           m.SupplyVolumeUnit,
           m.PeriodicalUnit,
           y.YearTypeName,
           r.ReferenceNumber
    FROM consumption c
    LEFT JOIN foodvehicle v ON c.VehicleID = v.VehicleID
    LEFT JOIN geographylevel1 gl1 ON c.GL1ID = gl1.GL1ID
    LEFT JOIN geographylevel2 gl2 ON c.GL2ID = gl2.GL2ID
    LEFT JOIN geographylevel3 gl3 ON c.GL3ID = gl3.GL3ID
    LEFT JOIN gender g ON c.GenderID = g.GenderID
    LEFT JOIN age a ON c.AgeID = a.AgeID
    LEFT JOIN measureunit1 m ON c.UCID = m.UCID
    LEFT JOIN yeartype y ON c.YearTypeID = y.YearTypeID
    LEFT JOIN reference r ON c.ReferenceID = r.ReferenceID
");
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Input Consumption</title>
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
        <h1>Input Consumption</h1>

        <!-- Create Form -->
        <h3>Add New Consumption</h3>
        <form method="post" action="input_consumption.php" class="mb-4" onsubmit="return validateYears()">
            <input type="hidden" name="action" value="create">
            <label for="VehicleID">Vehicle:</label>
            <select name="VehicleID" id="VehicleID" class="form-control">
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?= $vehicle['VehicleID'] ?>"><?= $vehicle['VehicleName'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="GL1ID">Admin Level 1:</label>
            <select name="GL1ID" id="GL1ID" class="form-control">
                <option value="">-- None --</option>
                <?php foreach ($levels1 as $level1): ?>
                    <option value="<?= $level1['GL1ID'] ?>"><?= $level1['AdminLevel1'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="GL2ID">Admin Level 2:</label>
            <select name="GL2ID" id="GL2ID" class="form-control">
                <option value="">-- None --</option>
                <?php foreach ($levels2 as $level2): ?>
                    <option value="<?= $level2['GL2ID'] ?>"><?= $level2['AdminLevel2'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="GL3ID">Admin Level 3:</label>
            <select name="GL3ID" id="GL3ID" class="form-control">
                <option value="">-- None --</option>
                <?php foreach ($levels3 as $level3): ?>
                    <option value="<?= $level3['GL3ID'] ?>"><?= $level3['AdminLevel3'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="GenderID">Gender:</label>
            <select name="GenderID" id="GenderID" class="form-control">
                <?php foreach ($genders as $gender): ?>
                    <option value="<?= $gender['GenderID'] ?>"><?= $gender['GenderName'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="AgeID">Age Range:</label>
            <select name="AgeID" id="AgeID" class="form-control">
                <?php foreach ($ages as $age): ?>
                    <option value="<?= $age['AgeID'] ?>"><?= $age['AgeRange'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="NumberOfPeople">Number of People:</label>
            <input type="text" name="NumberOfPeople" id="NumberOfPeople" class="form-control"><br>

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
        <h2>Consumption Records</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Vehicle</th>
                        <th>Admin Level 1</th>
                        <th>Admin Level 2</th>
                        <th>Admin Level 3</th>
                        <th>Gender</th>
                        <th>Age Range</th>
                        <th>Number of People</th>
                        <th>Supply Volume Unit</th>
                        <th>Periodical Unit</th>
                        <th>Source Volume</th>
                        <th>Volume (MT/Y)</th>
                        <th>Year Type</th>
                        <th>Start Year</th>
                        <th>End Year</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $consumptions->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['ConsumptionID'] ?></td>
                            <td><?= $row['VehicleName'] ?></td>
                            <td><?= $row['AdminLevel1'] ?></td>
                            <td><?= $row['AdminLevel2'] ?></td>
                            <td><?= $row['AdminLevel3'] ?></td>
                            <td><?= $row['GenderName'] ?></td>
                            <td><?= $row['AgeRange'] ?></td>
                            <td><?= $row['NumberOfPeople'] ?></td>
                            <td><?= $row['SupplyVolumeUnit'] ?></td>
                            <td><?= $row['PeriodicalUnit'] ?></td>
                            <td><?= $row['SourceVolume'] ?></td>
                            <td><?= $row['VolumeMTY'] ?></td>
                            <td><?= $row['YearTypeName'] ?></td>
                            <td><?= $row['StartYear'] ?></td>
                            <td><?= $row['EndYear'] ?></td>
                            <td><?= $row['ReferenceNumber'] ?></td>
                            <td>
                                <a href="?action=edit&id=<?= $row['ConsumptionID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?action=delete&id=<?= $row['ConsumptionID'] ?>" class="btn btn-danger btn-sm"
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
            $consumptionID = $_GET['id'];
            $result = $conn->query("SELECT * FROM consumption WHERE ConsumptionID = $consumptionID");
            if ($row = $result->fetch_assoc()):
            ?>
                <h3>Edit Consumption</h3>
                <form method="post" action="input_consumption.php" class="mb-4" onsubmit="return validateYears()">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="ConsumptionID" value="<?= $row['ConsumptionID'] ?>">
                    <label for="VehicleID">Vehicle:</label>
                    <select name="VehicleID" id="VehicleID" class="form-control">
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?= $vehicle['VehicleID'] ?>" <?= $vehicle['VehicleID'] == $row['VehicleID'] ? 'selected' : '' ?>><?= $vehicle['VehicleName'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="GL1ID">Admin Level 1:</label>
                    <select name="GL1ID" id="GL1ID" class="form-control">
                        <option value="">-- None --</option>
                        <?php foreach ($levels1 as $level1): ?>
                            <option value="<?= $level1['GL1ID'] ?>" <?= $level1['GL1ID'] == $row['GL1ID'] ? 'selected' : '' ?>><?= $level1['AdminLevel1'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="GL2ID">Admin Level 2:</label>
                    <select name="GL2ID" id="GL2ID" class="form-control">
                        <option value="">-- None --</option>
                        <?php foreach ($levels2 as $level2): ?>
                            <option value="<?= $level2['GL2ID'] ?>" <?= $level2['GL2ID'] == $row['GL2ID'] ? 'selected' : '' ?>><?= $level2['AdminLevel2'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="GL3ID">Admin Level 3:</label>
                    <select name="GL3ID" id="GL3ID" class="form-control">
                        <option value="">-- None --</option>
                        <?php foreach ($levels3 as $level3): ?>
                            <option value="<?= $level3['GL3ID'] ?>" <?= $level3['GL3ID'] == $row['GL3ID'] ? 'selected' : '' ?>><?= $level3['AdminLevel3'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="GenderID">Gender:</label>
                    <select name="GenderID" id="GenderID" class="form-control">
                        <?php foreach ($genders as $gender): ?>
                            <option value="<?= $gender['GenderID'] ?>" <?= $gender['GenderID'] == $row['GenderID'] ? 'selected' : '' ?>><?= $gender['GenderName'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="AgeID">Age Range:</label>
                    <select name="AgeID" id="AgeID" class="form-control">
                        <?php foreach ($ages as $age): ?>
                            <option value="<?= $age['AgeID'] ?>" <?= $age['AgeID'] == $row['AgeID'] ? 'selected' : '' ?>><?= $age['AgeRange'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="NumberOfPeople">Number of People:</label>
                    <input type="text" name="NumberOfPeople" id="NumberOfPeople" class="form-control" value="<?= $row['NumberOfPeople'] ?>"><br>

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
            document.getElementById("ConsumptionID").value = record.ConsumptionID;
            document.getElementById("VehicleID").value = record.VehicleID;
            document.getElementById("GL1ID").value = record.GL1ID;
            document.getElementById("GL2ID").value = record.GL2ID;
            document.getElementById("GL3ID").value = record.GL3ID;
            document.getElementById("GenderID").value = record.GenderID;
            document.getElementById("AgeID").value = record.AgeID;
            document.getElementById("NumberOfPeople").value = record.NumberOfPeople;
            document.getElementById("UCID").value = record.UCID;
            document.getElementById("SourceVolume").value = record.SourceVolume;
            document.getElementById("VolumeMTY").value = record.VolumeMTY;
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

        function computeVolume() {
            var source = parseFloat(document.getElementById('SourceVolume').value) || 0;
            var ucid = document.getElementById('UCID').value;
            var unitVal = unitValues[ucid] || 0;
            var volume = source * unitVal;
            document.getElementById('VolumeMTY').value = volume ? volume.toFixed(6) : '';
        }

        // Trigger volume calculation when either field changes
        document.getElementById('SourceVolume').addEventListener('input', computeVolume);
        document.getElementById('UCID').addEventListener('change', computeVolume);

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