<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $supplyVolumeUnit = $_POST['supplyVolumeUnit'];
        $periodicalUnit = $_POST['periodicalUnit'];
        $unitValue = $_POST['unitValue'];
        // Check if the combination of SupplyVolumeUnit and PeriodicalUnit already exists
        $checkQuery = $conn->prepare("SELECT * FROM measureunit1 WHERE SupplyVolumeUnit = ? AND PeriodicalUnit = ?");
        $checkQuery->bind_param("ss", $supplyVolumeUnit, $periodicalUnit);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of SupplyVolumeUnit and PeriodicalUnit already exists. Please use a different combination.'); window.location.href = 'input_measure_unit1.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO measureunit1 (SupplyVolumeUnit, PeriodicalUnit, UnitValue) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $supplyVolumeUnit, $periodicalUnit, $unitValue);
            $stmt->execute();
            $stmt->close();
            header("Location: input_measure_unit1.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $supplyVolumeUnit = $_POST['supplyVolumeUnit'];
        $periodicalUnit = $_POST['periodicalUnit'];
        $unitValue = $_POST['unitValue'];
        $stmt = $conn->prepare("UPDATE measureunit1 SET SupplyVolumeUnit = ?, PeriodicalUnit = ?, UnitValue = ? WHERE UCID = ?");
        $stmt->bind_param("ssdi", $supplyVolumeUnit, $periodicalUnit, $unitValue, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_measure_unit1.php");
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
        WHERE REFERENCED_TABLE_NAME = 'measureunit1' AND REFERENCED_COLUMN_NAME = 'UCID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE UCID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_measure_unit1.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM measureunit1 WHERE UCID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_measure_unit1.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Measure Unit 1 Table</title>
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
</head>
<body>
    <div class="container mt-5">
        <h1>Modify Measure Unit 1 Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Measure Unit 1</h3>
        <form method="post" action="input_measure_unit1.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="supplyVolumeUnit">Supply Volume Unit:</label>
                <input type="text" id="supplyVolumeUnit" name="supplyVolumeUnit" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="periodicalUnit">Periodical Unit:</label>
                <input type="text" id="periodicalUnit" name="periodicalUnit" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="unitValue">Unit Value:</label>
                <input type="number" step="0.00000001" id="unitValue" name="unitValue" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Measure Unit 1 Table -->
        <h2>Table: Measure Unit 1</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>UCID</th>
                        <th>Supply Volume Unit</th>
                        <th>Periodical Unit</th>
                        <th>Unit Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM measureunit1 ORDER BY UCID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['UCID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['SupplyVolumeUnit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['PeriodicalUnit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['UnitValue']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['UCID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['UCID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM measureunit1 WHERE UCID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Measure Unit 1</h2>
                <form method="post" action="input_measure_unit1.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['UCID']); ?>">
                    <div class="form-group">
                        <label for="supplyVolumeUnit">Supply Volume Unit:</label>
                        <input type="text" id="supplyVolumeUnit" name="supplyVolumeUnit" class="form-control" value="<?php echo htmlspecialchars($row['SupplyVolumeUnit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="periodicalUnit">Periodical Unit:</label>
                        <input type="text" id="periodicalUnit" name="periodicalUnit" class="form-control" value="<?php echo htmlspecialchars($row['PeriodicalUnit']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="unitValue">Unit Value:</label>
                        <input type="number" step="0.00000001" id="unitValue" name="unitValue" class="form-control" value="<?php echo htmlspecialchars($row['UnitValue']); ?>" required>
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
