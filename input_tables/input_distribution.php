<?php
// input_distribution.php
include('../db_connect.php');

// Fetch options for dropdowns
function fetchOptions($conn, $table, $idField, $nameField)
{
    $options = [];
    $result = $conn->query("SELECT $idField, $nameField FROM $table ORDER BY $nameField");
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
    return $options;
}

$distributionChannels = fetchOptions($conn, 'distributionchannel', 'DistributionChannelID', 'DistributionChannelName');
$subDistributionChannels = fetchOptions($conn, 'subdistributionchannel', 'SubDistributionChannelID', 'SubDistributionChannelName');
$vehicles = fetchOptions($conn, 'foodvehicle', 'VehicleID', 'VehicleName');
$countries = fetchOptions($conn, 'country', 'CountryID', 'CountryName');
$yearTypes = fetchOptions($conn, 'yeartype', 'YearTypeID', 'YearTypeName');
$references = fetchOptions($conn, 'reference', 'ReferenceID', 'ReferenceNumber');

// Replace the existing $units = fetchOptions(...) line with a custom query to fetch both SupplyVolumeUnit and PeriodicalUnit
$units = [];
$resultUnits = $conn->query("SELECT UCID, SupplyVolumeUnit, PeriodicalUnit, UnitValue FROM measureunit1 ORDER BY SupplyVolumeUnit");
while ($rowUnits = $resultUnits->fetch_assoc()) {
    $units[] = $rowUnits;
}

// Also fetch full reference data for Source, Link, etc.
$refDataResult = $conn->query("SELECT ReferenceID, ReferenceNumber, Source, Link, ProcessToObtainData, AccessDate FROM reference");
$fullRefs = [];
while ($refRow = $refDataResult->fetch_assoc()) {
    $fullRefs[$refRow['ReferenceID']] = $refRow;
}

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $distributionChannelID = $_POST['DistributionChannelID'];
        $subDistributionChannelID = $_POST['SubDistributionChannelID'];
        $vehicleID = $_POST['VehicleID'];
        $ucid = $_POST['UCID'];
        $sourceVolume = (float)$_POST['SourceVolume']; // Convert input to float

        // Calculate Volume_MT_Y based on UCID and SourceVolume
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error calculating volume: " . $conn->error . "<br>";
            exit;
        }

        $countryID = $_POST['CountryID'];
        $yearTypeID = $_POST['YearTypeID'];
        $startYear = $_POST['StartYear'];
        $endYear = $_POST['EndYear'];
        $referenceID = $_POST['ReferenceID'];

        $sql = "INSERT INTO distribution (DistributionChannelID, SubDistributionChannelID, VehicleID, UCID, SourceVolume, Volume_MT_Y, CountryID, YearTypeID, StartYear, EndYear, ReferenceID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiidiiiii", $distributionChannelID, $subDistributionChannelID, $vehicleID, $ucid, $sourceVolume, $volumeMTY, $countryID, $yearTypeID, $startYear, $endYear, $referenceID);

        if ($stmt->execute()) {
            echo "✓ Distribution record inserted successfully.<br>";
        } else {
            echo "Error inserting distribution record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        header("Location: input_distribution.php");
        exit;
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $distributionID = $_POST['DistributionID'];
        $distributionChannelID = $_POST['DistributionChannelID'];
        $subDistributionChannelID = $_POST['SubDistributionChannelID'];
        $vehicleID = $_POST['VehicleID'];
        $ucid = $_POST['UCID'];
        $sourceVolume = (float)$_POST['SourceVolume']; // Convert input to float

        // Calculate Volume_MT_Y based on UCID and SourceVolume
        $unitValueResult = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
        if ($unitValueResult && $unitValueRow = $unitValueResult->fetch_assoc()) {
            $unitValue = (float)$unitValueRow['UnitValue'];
            $volumeMTY = $sourceVolume * $unitValue;
        } else {
            echo "Error calculating volume: " . $conn->error . "<br>";
            exit;
        }

        $countryID = $_POST['CountryID'];
        $yearTypeID = $_POST['YearTypeID'];
        $startYear = $_POST['StartYear'];
        $endYear = $_POST['EndYear'];
        $referenceID = $_POST['ReferenceID'];

        $sql = "UPDATE distribution SET DistributionChannelID = ?, SubDistributionChannelID = ?, VehicleID = ?, UCID = ?, SourceVolume = ?, Volume_MT_Y = ?, CountryID = ?, YearTypeID = ?, StartYear = ?, EndYear = ?, ReferenceID = ? WHERE DistributionID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiidiiiiii", $distributionChannelID, $subDistributionChannelID, $vehicleID, $ucid, $sourceVolume, $volumeMTY, $countryID, $yearTypeID, $startYear, $endYear, $referenceID, $distributionID);

        if ($stmt->execute()) {
            echo "✓ Distribution record updated successfully.<br>";
        } else {
            echo "Error updating distribution record: " . $stmt->error . "<br>";
        }
        $stmt->close();
        header("Location: input_distribution.php");
        exit;
    }
}

// Process delete requests via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $distributionID = $_GET['id'];
    $sql = "DELETE FROM distribution WHERE DistributionID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $distributionID);
    if ($stmt->execute()) {
        echo "✓ Distribution record deleted successfully.<br>";
    } else {
        echo "Error deleting distribution record: " . $stmt->error . "<br>";
    }
    $stmt->close();
    header("Location: input_distribution.php");
    exit;
}

// Fetch existing records (with JOINs on related tables)
$distributions = $conn->query("
    SELECT d.*,
           dc.DistributionChannelName,
           sd.SubDistributionChannelName,
           fv.VehicleName,
           m.SupplyVolumeUnit,
           c.CountryName,
           y.YearTypeName,
           r.ReferenceNumber
    FROM distribution d
    LEFT JOIN distributionchannel dc ON d.DistributionChannelID = dc.DistributionChannelID
    LEFT JOIN subdistributionchannel sd ON d.SubDistributionChannelID = sd.SubDistributionChannelID
    LEFT JOIN foodvehicle fv ON d.VehicleID = fv.VehicleID
    LEFT JOIN measureunit1 m ON d.UCID = m.UCID
    LEFT JOIN country c ON d.CountryID = c.CountryID
    LEFT JOIN yeartype y ON d.YearTypeID = y.YearTypeID
    LEFT JOIN reference r ON d.ReferenceID = r.ReferenceID
");

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Input Distribution Data</title>
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
</head>

<body>
    <div class="container mt-5">
        <h1>Input Distribution Data</h1>

        <!-- Create Form -->
        <h3>Add New Distribution</h3>
        <form method="post" action="input_distribution.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <label for="DistributionChannelID">Distribution Channel:</label>
            <select name="DistributionChannelID" id="DistributionChannelID" class="form-control">
                <?php foreach ($distributionChannels as $channel): ?>
                    <option value="<?= $channel['DistributionChannelID'] ?>"><?= $channel['DistributionChannelName'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="SubDistributionChannelID">Sub Distribution Channel:</label>
            <select name="SubDistributionChannelID" id="SubDistributionChannelID" class="form-control">
                <?php foreach ($subDistributionChannels as $subChannel): ?>
                    <option value="<?= $subChannel['SubDistributionChannelID'] ?>"><?= $subChannel['SubDistributionChannelName'] ?></option>
                <?php endforeach; ?>
            </select><br>

            <label for="VehicleID">Vehicle:</label>
            <select name="VehicleID" id="VehicleID" class="form-control">
                <?php foreach ($vehicles as $vehicle): ?>
                    <option value="<?= $vehicle['VehicleID'] ?>"><?= $vehicle['VehicleName'] ?></option>
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

            <label for="Volume_MT_Y">Volume (MT/Y):</label>
            <input type="text" id="Volume_MT_Y" class="form-control" readonly value=""><br>

            <label for="CountryID">Country:</label>
            <select name="CountryID" id="CountryID" class="form-control">
                <?php foreach ($countries as $country): ?>
                    <option value="<?= $country['CountryID'] ?>"><?= $country['CountryName'] ?></option>
                <?php endforeach; ?>
            </select><br>

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

        <!-- Existing Records Table -->
        <h2>Existing Distribution Records</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Distribution Channel</th>
                        <th>Sub Distribution Channel</th>
                        <th>Vehicle</th>
                        <th>Supply Volume Unit</th>
                        <th>Source Volume</th>
                        <th>Volume (MT/Y)</th>
                        <th>Country</th>
                        <th>Year Type</th>
                        <th>Start Year</th>
                        <th>End Year</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $distributions->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['DistributionID'] ?></td>
                            <td><?= $row['DistributionChannelName'] ?></td>
                            <td><?= $row['SubDistributionChannelName'] ?></td>
                            <td><?= $row['VehicleName'] ?></td>
                            <td><?= $row['SupplyVolumeUnit'] ?></td>
                            <td><?= $row['SourceVolume'] ?></td>
                            <td><?= $row['Volume_MT_Y'] ?></td>
                            <td><?= $row['CountryName'] ?></td>
                            <td><?= $row['YearTypeName'] ?></td>
                            <td><?= $row['StartYear'] ?></td>
                            <td><?= $row['EndYear'] ?></td>
                            <td><?= $row['ReferenceNumber'] ?></td>
                            <td>
                                <a href="?action=edit&id=<?= $row['DistributionID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="?action=delete&id=<?= $row['DistributionID'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Form - show only when "edit" action is triggered -->
        <?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])): ?>
            <?php
            $distributionID = $_GET['id'];
            $result = $conn->query("SELECT * FROM distribution WHERE DistributionID = $distributionID");
            if ($row = $result->fetch_assoc()):
            ?>
                <h2>Edit Distribution</h2>
                <form method="post" action="input_distribution.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="DistributionID" value="<?= $row['DistributionID'] ?>">
                    <label for="DistributionChannelID">Distribution Channel:</label>
                    <select name="DistributionChannelID" id="DistributionChannelID" class="form-control">
                        <?php foreach ($distributionChannels as $channel): ?>
                            <option value="<?= $channel['DistributionChannelID'] ?>" <?= $channel['DistributionChannelID'] == $row['DistributionChannelID'] ? 'selected' : '' ?>><?= $channel['DistributionChannelName'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="SubDistributionChannelID">Sub Distribution Channel:</label>
                    <select name="SubDistributionChannelID" id="SubDistributionChannelID" class="form-control">
                        <?php foreach ($subDistributionChannels as $subChannel): ?>
                            <option value="<?= $subChannel['SubDistributionChannelID'] ?>" <?= $subChannel['SubDistributionChannelID'] == $row['SubDistributionChannelID'] ? 'selected' : '' ?>><?= $subChannel['SubDistributionChannelName'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="VehicleID">Vehicle:</label>
                    <select name="VehicleID" id="VehicleID" class="form-control">
                        <?php foreach ($vehicles as $vehicle): ?>
                            <option value="<?= $vehicle['VehicleID'] ?>" <?= $vehicle['VehicleID'] == $row['VehicleID'] ? 'selected' : '' ?>><?= $vehicle['VehicleName'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="UCID">Supply Volume Unit:</label>
                    <select name="UCID" id="UCID" class="form-control">
                        <?php foreach ($units as $unit): ?>
                            <option value="<?= $unit['UCID'] ?>" <?= $unit['UCID'] == $row['UCID'] ? 'selected' : '' ?>>
                                <?= $unit['SupplyVolumeUnit'] ?> / <?= $unit['PeriodicalUnit'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select><br>

                    <label for="SourceVolume">Source Volume:</label>
                    <input type="text" name="SourceVolume" id="SourceVolume" class="form-control" value="<?= $row['SourceVolume'] ?>"><br>

                    <label for="Volume_MT_Y">Volume (MT/Y):</label>
                    <input type="text" id="Volume_MT_Y" class="form-control" readonly value="<?= htmlspecialchars(number_format($row['Volume_MT_Y'], 4)) ?>"><br>

                    <label for="CountryID">Country:</label>
                    <select name="CountryID" id="CountryID" class="form-control">
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['CountryID'] ?>" <?= $country['CountryID'] == $row['CountryID'] ? 'selected' : '' ?>><?= $country['CountryName'] ?></option>
                        <?php endforeach; ?>
                    </select><br>

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
            <?php
            endif;
            ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Build references object in JS
        var references = <?php echo json_encode($fullRefs); ?>;

        function updateReferenceDetails() {
            var referenceID = document.getElementById('ReferenceID').value;
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
            document.getElementById("DistributionID").value = record.DistributionID;
            document.getElementById("DistributionChannelID").value = record.DistributionChannelID;
            document.getElementById("SubDistributionChannelID").value = record.SubDistributionChannelID;
            document.getElementById("VehicleID").value = record.VehicleID;
            document.getElementById("UCID").value = record.UCID;
            document.getElementById("SourceVolume").value = record.SourceVolume;
            document.getElementById("Volume_MT_Y").value = record.Volume_MT_Y;
            document.getElementById("CountryID").value = record.CountryID;
            document.getElementById("YearTypeID").value = record.YearTypeID;
            document.getElementById("StartYear").value = record.StartYear;
            document.getElementById("EndYear").value = record.EndYear;
            document.getElementById("ReferenceID").value = record.ReferenceID;
            updateReferenceDetails(record.ReferenceID);
        }

        <?php if (isset($_GET['ReferenceID'])): ?>
            document.addEventListener("DOMContentLoaded", function() {
                updateReferenceDetails(<?= intval($_GET['ReferenceID']) ?>);
            });
        <?php endif; ?>

        // Build a simple list of UCID => UnitValue from the $units array


        var unitValues = {
            <?php foreach ($units as $unit) {
                echo "{$unit['UCID']}: " . (float)$unit['UnitValue'] . ",\n";
            } ?>
        };


        function computeVolume() {
            var source = parseFloat(document.getElementById('SourceVolume').value) || 0;
            var ucid = document.getElementById('UCID').value;
            var unitVal = unitValues[ucid] || 0;
            var volume = source * unitVal;
            document.getElementById('Volume_MT_Y').value = volume ? volume.toFixed(4) : '';
        }

        // Trigger volume calculation when either field changes
        document.getElementById('SourceVolume').addEventListener('input', computeVolume);
        document.getElementById('UCID').addEventListener('change', computeVolume);
    </script>
</body>

</html>