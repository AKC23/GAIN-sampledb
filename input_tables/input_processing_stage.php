<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $processingStageName = $_POST['processingStageName'];
        $extractionRate = $_POST['extractionRate'];
        $vehicleID = $_POST['vehicleID'];

        // Check if the ProcessingStageName already exists
        $checkQuery = $conn->prepare("SELECT * FROM processingstage WHERE ProcessingStageName = ?");
        $checkQuery->bind_param("s", $processingStageName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This ProcessingStageName already exists. Please use a different name.'); window.location.href = 'input_processing_stage.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO processingstage (ProcessingStageName, ExtractionRate, VehicleID) VALUES (?, ?, ?)");
            $stmt->bind_param("sdi", $processingStageName, $extractionRate, $vehicleID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_processing_stage.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $processingStageName = $_POST['processingStageName'];
        $extractionRate = $_POST['extractionRate'];
        $vehicleID = $_POST['vehicleID'];
        $stmt = $conn->prepare("UPDATE processingstage SET ProcessingStageName = ?, ExtractionRate = ?, VehicleID = ? WHERE PSID = ?");
        $stmt->bind_param("sdii", $processingStageName, $extractionRate, $vehicleID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_processing_stage.php");
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
        WHERE REFERENCED_TABLE_NAME = 'processingstage' AND REFERENCED_COLUMN_NAME = 'PSID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE PSID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_processing_stage.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM processingstage WHERE PSID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_processing_stage.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Processing Stage Table</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
        .table-responsive {
            max-height: 500px;
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
        <h1>Modify Processing Stage Table</h1>

        <!-- Create Form -->
        <h3>Add New Processing Stage</h3>
        <form method="post" action="input_processing_stage.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="processingStageName">Processing Stage Name:</label>
                <input type="text" id="processingStageName" name="processingStageName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="extractionRate">Extraction Rate (Example: 35/70/95):</label>
                <input type="number" step="0.01" id="extractionRate" name="extractionRate" class="form-control">
            </div>
            <div class="form-group">
                <label for="vehicleID">Food Vehicle:</label>
                <select id="vehicleID" name="vehicleID" class="form-control" required>
                    <?php
                    $foodVehicleResult = $conn->query("SELECT VehicleID, VehicleName FROM foodvehicle ORDER BY VehicleName ASC");
                    while ($foodVehicleRow = $foodVehicleResult->fetch_assoc()) {
                        echo "<option value='{$foodVehicleRow['VehicleID']}'>" . htmlspecialchars($foodVehicleRow['VehicleName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Processing Stage Table -->
        <h2>Table: Processing Stage</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>PSID</th>
                        <th>Processing Stage Name</th>
                        <th>Extraction Rate</th>
                        <th>Food Vehicle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT ps.PSID, ps.ProcessingStageName, ps.ExtractionRate, fv.VehicleName FROM processingstage ps JOIN foodvehicle fv ON ps.VehicleID = fv.VehicleID ORDER BY ps.PSID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['PSID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ProcessingStageName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ExtractionRate']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['PSID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['PSID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM processingstage WHERE PSID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Processing Stage</h2>
                <form method="post" action="input_processing_stage.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['PSID']); ?>">
                    <div class="form-group">
                        <label for="processingStageName">Processing Stage Name:</label>
                        <input type="text" id="processingStageName" name="processingStageName" class="form-control" value="<?php echo htmlspecialchars($row['ProcessingStageName']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="extractionRate">Extraction Rate:</label>
                        <input type="number" step="0.01" id="extractionRate" name="extractionRate" class="form-control" value="<?php echo htmlspecialchars($row['ExtractionRate']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="vehicleID">Food Vehicle:</label>
                        <select id="vehicleID" name="vehicleID" class="form-control" required>
                            <?php
                            $foodVehicleResult = $conn->query("SELECT VehicleID, VehicleName FROM foodvehicle ORDER BY VehicleName ASC");
                            while ($foodVehicleRow = $foodVehicleResult->fetch_assoc()) {
                                $selected = ($foodVehicleRow['VehicleID'] == $row['VehicleID']) ? 'selected' : '';
                                echo "<option value='{$foodVehicleRow['VehicleID']}' $selected>" . htmlspecialchars($foodVehicleRow['VehicleName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
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
