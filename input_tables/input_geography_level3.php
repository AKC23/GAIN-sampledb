<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $adminLevel3 = $_POST['adminLevel3'];
        $gl2ID = $_POST['gl2ID'];
        // Check if the combination of AdminLevel3 and GL2ID already exists
        $checkQuery = $conn->prepare("SELECT * FROM geographylevel3 WHERE AdminLevel3 = ? AND GL2ID = ?");
        $checkQuery->bind_param("si", $adminLevel3, $gl2ID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of AdminLevel3 and GL2ID already exists. Please use a different combination.'); window.location.href = 'input_geography_level3.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO geographylevel3 (AdminLevel3, GL2ID) VALUES (?, ?)");
            $stmt->bind_param("si", $adminLevel3, $gl2ID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_geography_level3.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $adminLevel3 = $_POST['adminLevel3'];
        $gl2ID = $_POST['gl2ID'];
        $stmt = $conn->prepare("UPDATE geographylevel3 SET AdminLevel3 = ?, GL2ID = ? WHERE GL3ID = ?");
        $stmt->bind_param("sii", $adminLevel3, $gl2ID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_geography_level3.php");
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
        WHERE REFERENCED_TABLE_NAME = 'geographylevel3' AND REFERENCED_COLUMN_NAME = 'GL3ID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE GL3ID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_geography_level3.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM geographylevel3 WHERE GL3ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_geography_level3.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Geography Level 3 Table</title>
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
        <h1>Modify Geography Level 3 Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Informations</h3>
        <form method="post" action="input_geography_level3.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="adminLevel3">Admin Level 3:</label>
                <input type="text" id="adminLevel3" name="adminLevel3" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="gl2ID">Admin Level 2:</label>
                <select id="gl2ID" name="gl2ID" class="form-control" required>
                    <?php
                    $gl2Result = $conn->query("SELECT GL2ID, AdminLevel2 FROM geographylevel2 ORDER BY AdminLevel2 ASC");
                    while ($gl2Row = $gl2Result->fetch_assoc()) {
                        echo "<option value='{$gl2Row['GL2ID']}'>" . htmlspecialchars($gl2Row['AdminLevel2']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Geography Level 3 Table -->
        <h2>Table: Geography Level 3</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>GL3ID</th>
                        <th>Admin Level 3</th>
                        <th>Admin Level 2</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT GL3ID, AdminLevel3, geographylevel2.AdminLevel2 FROM geographylevel3 JOIN geographylevel2 ON geographylevel3.GL2ID = geographylevel2.GL2ID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['GL3ID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AdminLevel3']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AdminLevel2']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['GL3ID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['GL3ID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM geographylevel3 WHERE GL3ID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Geography Level 3</h2>
                <form method="post" action="input_geography_level3.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['GL3ID']); ?>">
                    <div class="form-group">
                        <label for="adminLevel3">Admin Level 3:</label>
                        <input type="text" id="adminLevel3" name="adminLevel3" class="form-control" value="<?php echo htmlspecialchars($row['AdminLevel3']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gl2ID">Admin Level 2:</label>
                        <select id="gl2ID" name="gl2ID" class="form-control" required>
                            <?php
                            $gl2Result = $conn->query("SELECT GL2ID, AdminLevel2 FROM geographylevel2 ORDER BY AdminLevel2 ASC");
                            while ($gl2Row = $gl2Result->fetch_assoc()) {
                                $selected = ($gl2Row['GL2ID'] == $row['GL2ID']) ? 'selected' : '';
                                echo "<option value='{$gl2Row['GL2ID']}' $selected>" . htmlspecialchars($gl2Row['AdminLevel2']) . "</option>";
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
