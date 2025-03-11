<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $extractionRate = $_POST['extractionRate'];
        $vehicleID = $_POST['vehicleID'];
        $foodTypeID = $_POST['foodTypeID'];
        $referenceID = $_POST['referenceID'];

        // Check if the combination of ExtractionRate, VehicleID, FoodTypeID, and ReferenceID already exists
        $checkQuery = $conn->prepare("SELECT * FROM extractionconversion WHERE ExtractionRate = ? AND VehicleID = ? AND FoodTypeID = ? AND ReferenceID = ?");
        $checkQuery->bind_param("diii", $extractionRate, $vehicleID, $foodTypeID, $referenceID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of ExtractionRate, VehicleID, FoodTypeID, and ReferenceID already exists. Please use a different combination.'); window.location.href = 'input_extraction_conversion.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO extractionconversion (ExtractionRate, VehicleID, FoodTypeID, ReferenceID) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("diii", $extractionRate, $vehicleID, $foodTypeID, $referenceID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_extraction_conversion.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $extractionRate = $_POST['extractionRate'];
        $vehicleID = $_POST['vehicleID'];
        $foodTypeID = $_POST['foodTypeID'];
        $referenceID = $_POST['referenceID'];
        $stmt = $conn->prepare("UPDATE extractionconversion SET ExtractionRate = ?, VehicleID = ?, FoodTypeID = ?, ReferenceID = ? WHERE ExtractionID = ?");
        $stmt->bind_param("diiii", $extractionRate, $vehicleID, $foodTypeID, $referenceID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_extraction_conversion.php");
        exit;
    }
}

// Process delete requests via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Check for foreign key constraints
    $checkForeignKeyQuery = "
        SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_NAME = 'extractionconversion' AND REFERENCED_COLUMN_NAME = 'ExtractionID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE ExtractionID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_extraction_conversion.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM extractionconversion WHERE ExtractionID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_extraction_conversion.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Extraction Conversion Table</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table th, table td {
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
            var referenceID = document.getElementById('referenceID').value;
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
        <h1>Modify Extraction Conversion Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Extraction Conversion</h3>
        <form method="post" action="input_extraction_conversion.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="extractionRate">Extraction Rate (Example: 17.5 / 33.33):</label>
                <input type="number" step="0.01" id="extractionRate" name="extractionRate" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="vehicleID">Food Vehicle:</label>
                <select id="vehicleID" name="vehicleID" class="form-control" required>
                    <?php
                    $vehicleResult = $conn->query("SELECT VehicleID, VehicleName FROM foodvehicle ORDER BY VehicleName ASC");
                    while ($vehicleRow = $vehicleResult->fetch_assoc()) {
                        echo "<option value='{$vehicleRow['VehicleID']}'>" . htmlspecialchars($vehicleRow['VehicleName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="foodTypeID">Food Type:</label>
                <select id="foodTypeID" name="foodTypeID" class="form-control" required>
                    <?php
                    $foodTypeResult = $conn->query("SELECT FoodTypeID, FoodTypeName FROM foodtype ORDER BY FoodTypeName ASC");
                    while ($foodTypeRow = $foodTypeResult->fetch_assoc()) {
                        echo "<option value='{$foodTypeRow['FoodTypeID']}'>" . htmlspecialchars($foodTypeRow['FoodTypeName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="referenceID">Reference:</label>
                <select id="referenceID" name="referenceID" class="form-control" required onchange="updateReferenceDetails()">
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
        
        <!-- Extraction Conversion Table -->
        <h2>Table: Extraction Conversion</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Extraction ID</th>
                        <th>Extraction Rate</th>
                        <th>Food Vehicle</th>
                        <th>Food Type</th>
                        <th>Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT ec.ExtractionID, ec.ExtractionRate, fv.VehicleName, ft.FoodTypeName, r.ReferenceNumber FROM extractionconversion ec JOIN foodvehicle fv ON ec.VehicleID = fv.VehicleID JOIN foodtype ft ON ec.FoodTypeID = ft.FoodTypeID JOIN reference r ON ec.ReferenceID = r.ReferenceID ORDER BY ec.ExtractionID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ExtractionID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ExtractionRate']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FoodTypeName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ReferenceNumber']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['ExtractionID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['ExtractionID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM extractionconversion WHERE ExtractionID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Extraction Conversion</h2>
                <form method="post" action="input_extraction_conversion.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['ExtractionID']); ?>">
                    <div class="form-group">
                        <label for="extractionRate">Extraction Rate:</label>
                        <input type="number" step="0.01" id="extractionRate" name="extractionRate" class="form-control" value="<?php echo htmlspecialchars($row['ExtractionRate']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="vehicleID">Food Vehicle:</label>
                        <select id="vehicleID" name="vehicleID" class="form-control" required>
                            <?php
                            $vehicleResult = $conn->query("SELECT VehicleID, VehicleName FROM foodvehicle ORDER BY VehicleName ASC");
                            while ($vehicleRow = $vehicleResult->fetch_assoc()) {
                                $selected = ($vehicleRow['VehicleID'] == $row['VehicleID']) ? 'selected' : '';
                                echo "<option value='{$vehicleRow['VehicleID']}' $selected>" . htmlspecialchars($vehicleRow['VehicleName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="foodTypeID">Food Type:</label>
                        <select id="foodTypeID" name="foodTypeID" class="form-control" required>
                            <?php
                            $foodTypeResult = $conn->query("SELECT FoodTypeID, FoodTypeName FROM foodtype ORDER BY FoodTypeName ASC");
                            while ($foodTypeRow = $foodTypeResult->fetch_assoc()) {
                                $selected = ($foodTypeRow['FoodTypeID'] == $row['FoodTypeID']) ? 'selected' : '';
                                echo "<option value='{$foodTypeRow['FoodTypeID']}' $selected>" . htmlspecialchars($foodTypeRow['FoodTypeName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="referenceID">Reference:</label>
                        <select id="referenceID" name="referenceID" class="form-control" required onchange="updateReferenceDetails()">
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
