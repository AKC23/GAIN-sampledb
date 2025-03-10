<?php
// input_producer_processor.php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $entityID = $_POST['entityID'];
        $taskDoneByEntity = $_POST['taskDoneByEntity'];
        $productionCapacityVolumeMTY = $_POST['productionCapacityVolumeMTY'];
        $percentageOfCapacityUsed = $_POST['percentageOfCapacityUsed'];
        $annualProductionSupplyVolumeMTY = $_POST['annualProductionSupplyVolumeMTY'];
        $producerReferenceID = $_POST['producerReferenceID'];
        
        // Check if the combination of EntityID and ProducerReferenceID already exists
        $checkQuery = $conn->prepare("SELECT * FROM producerprocessor WHERE EntityID = ? AND ProducerReferenceID = ?");
        $checkQuery->bind_param("ii", $entityID, $producerReferenceID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of EntityID and ProducerReferenceID already exists. Please use a different combination.'); window.location.href = 'input_producer_processor.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO producerprocessor (EntityID, TaskDoneByEntity, ProductionCapacityVolumeMTY, PercentageOfCapacityUsed, AnnualProductionSupplyVolumeMTY, ProducerReferenceID) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isdddi", $entityID, $taskDoneByEntity, $productionCapacityVolumeMTY, $percentageOfCapacityUsed, $annualProductionSupplyVolumeMTY, $producerReferenceID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_producer_processor.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $entityID = $_POST['entityID'];
        $taskDoneByEntity = $_POST['taskDoneByEntity'];
        $productionCapacityVolumeMTY = $_POST['productionCapacityVolumeMTY'];
        $percentageOfCapacityUsed = $_POST['percentageOfCapacityUsed'];
        $annualProductionSupplyVolumeMTY = $_POST['annualProductionSupplyVolumeMTY'];
        $producerReferenceID = $_POST['producerReferenceID'];
        $stmt = $conn->prepare("UPDATE producerprocessor SET EntityID = ?, TaskDoneByEntity = ?, ProductionCapacityVolumeMTY = ?, PercentageOfCapacityUsed = ?, AnnualProductionSupplyVolumeMTY = ?, ProducerReferenceID = ? WHERE ProducerProcessorID = ?");
        $stmt->bind_param("isdddii", $entityID, $taskDoneByEntity, $productionCapacityVolumeMTY, $percentageOfCapacityUsed, $annualProductionSupplyVolumeMTY, $producerReferenceID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_producer_processor.php");
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
        WHERE REFERENCED_TABLE_NAME = 'producerprocessor' AND REFERENCED_COLUMN_NAME = 'ProducerProcessorID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE ProducerProcessorID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_producer_processor.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM producerprocessor WHERE ProducerProcessorID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_producer_processor.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Producer Processor Table</title>
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Modify Producer Processor Table</h1>
        
        <!-- Create Form -->
        <form method="post" action="input_producer_processor.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="entityID">Entity Name:</label>
                <select id="entityID" name="entityID" class="form-control" required>
                    <?php
                    $entityResult = $conn->query("SELECT EntityID, ProducerProcessorName FROM entity ORDER BY ProducerProcessorName ASC");
                    while ($entityRow = $entityResult->fetch_assoc()) {
                        echo "<option value='{$entityRow['EntityID']}'>" . htmlspecialchars($entityRow['ProducerProcessorName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="taskDoneByEntity">Task Done By Entity:</label>
                <input type="text" id="taskDoneByEntity" name="taskDoneByEntity" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="productionCapacityVolumeMTY">Production Capacity Volume (MT/Y):</label>
                <input type="number" step="0.01" id="productionCapacityVolumeMTY" name="productionCapacityVolumeMTY" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="percentageOfCapacityUsed">Percentage of Capacity Used:</label>
                <input type="number" step="0.01" id="percentageOfCapacityUsed" name="percentageOfCapacityUsed" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="annualProductionSupplyVolumeMTY">Annual Production Supply Volume (MT/Y):</label>
                <input type="number" step="0.01" id="annualProductionSupplyVolumeMTY" name="annualProductionSupplyVolumeMTY" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="producerReferenceID">Producer Reference:</label>
                <select id="producerReferenceID" name="producerReferenceID" class="form-control" required>
                    <?php
                    $producerReferenceResult = $conn->query("SELECT ProducerReferenceID, IdentifierNumber FROM producerreference ORDER BY IdentifierNumber ASC");
                    while ($producerReferenceRow = $producerReferenceResult->fetch_assoc()) {
                        echo "<option value='{$producerReferenceRow['ProducerReferenceID']}'>" . htmlspecialchars($producerReferenceRow['IdentifierNumber']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Producer Processor Table -->
        <h2>Table: Producer Processor</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Producer Processor ID</th>
                        <th>Entity Name</th>
                        <th>Task Done By Entity</th>
                        <th>Production Capacity Volume (MT/Y)</th>
                        <th>Percentage of Capacity Used</th>
                        <th>Annual Production Supply Volume (MT/Y)</th>
                        <th>Producer Reference</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT pp.ProducerProcessorID, e.ProducerProcessorName, pp.TaskDoneByEntity, pp.ProductionCapacityVolumeMTY, pp.PercentageOfCapacityUsed, pp.AnnualProductionSupplyVolumeMTY, pr.IdentifierNumber FROM producerprocessor pp JOIN entity e ON pp.EntityID = e.EntityID JOIN producerreference pr ON pp.ProducerReferenceID = pr.ProducerReferenceID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ProducerProcessorID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ProducerProcessorName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['TaskDoneByEntity']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ProductionCapacityVolumeMTY']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['PercentageOfCapacityUsed']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AnnualProductionSupplyVolumeMTY']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['IdentifierNumber']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['ProducerProcessorID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['ProducerProcessorID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
        <?php
        // Edit Form - show only when "edit" action is triggered
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $stmt = $conn->prepare("SELECT * FROM producerprocessor WHERE ProducerProcessorID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Producer Processor</h2>
                <form method="post" action="input_producer_processor.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['ProducerProcessorID']); ?>">
                    <div class="form-group">
                        <label for="entityID">Entity Name:</label>
                        <select id="entityID" name="entityID" class="form-control" required>
                            <?php
                            $entityResult = $conn->query("SELECT EntityID, ProducerProcessorName FROM entity ORDER BY ProducerProcessorName ASC");
                            while ($entityRow = $entityResult->fetch_assoc()) {
                                $selected = ($entityRow['EntityID'] == $row['EntityID']) ? 'selected' : '';
                                echo "<option value='{$entityRow['EntityID']}' $selected>" . htmlspecialchars($entityRow['ProducerProcessorName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="taskDoneByEntity">Task Done By Entity:</label>
                        <input type="text" id="taskDoneByEntity" name="taskDoneByEntity" class="form-control" value="<?php echo htmlspecialchars($row['TaskDoneByEntity']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="productionCapacityVolumeMTY">Production Capacity Volume (MT/Y):</label>
                        <input type="number" step="0.01" id="productionCapacityVolumeMTY" name="productionCapacityVolumeMTY" class="form-control" value="<?php echo htmlspecialchars($row['ProductionCapacityVolumeMTY']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="percentageOfCapacityUsed">Percentage of Capacity Used:</label>
                        <input type="number" step="0.01" id="percentageOfCapacityUsed" name="percentageOfCapacityUsed" class="form-control" value="<?php echo htmlspecialchars($row['PercentageOfCapacityUsed']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="annualProductionSupplyVolumeMTY">Annual Production Supply Volume (MT/Y):</label>
                        <input type="number" step="0.01" id="annualProductionSupplyVolumeMTY" name="annualProductionSupplyVolumeMTY" class="form-control" value="<?php echo htmlspecialchars($row['AnnualProductionSupplyVolumeMTY']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="producerReferenceID">Producer Reference:</label>
                        <select id="producerReferenceID" name="producerReferenceID" class="form-control" required>
                            <?php
                            $producerReferenceResult = $conn->query("SELECT ProducerReferenceID, IdentifierNumber FROM producerreference ORDER BY IdentifierNumber ASC");
                            while ($producerReferenceRow = $producerReferenceResult->fetch_assoc()) {
                                $selected = ($producerReferenceRow['ProducerReferenceID'] == $row['ProducerReferenceID']) ? 'selected' : '';
                                echo "<option value='{$producerReferenceRow['ProducerReferenceID']}' $selected>" . htmlspecialchars($producerReferenceRow['IdentifierNumber']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                </form>
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
