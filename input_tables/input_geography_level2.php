<?php
// input_geography_level2.php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $adminLevel2 = $_POST['adminLevel2'];
        $gl1ID = $_POST['gl1ID'];
        // Check if the combination of AdminLevel2 and GL1ID already exists
        $checkQuery = $conn->prepare("SELECT * FROM geographylevel2 WHERE AdminLevel2 = ? AND GL1ID = ?");
        $checkQuery->bind_param("si", $adminLevel2, $gl1ID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of AdminLevel2 and GL1ID already exists. Please use a different combination.'); window.location.href = 'input_geography_level2.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO geographylevel2 (AdminLevel2, GL1ID) VALUES (?, ?)");
            $stmt->bind_param("si", $adminLevel2, $gl1ID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_geography_level2.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $adminLevel2 = $_POST['adminLevel2'];
        $gl1ID = $_POST['gl1ID'];
        $stmt = $conn->prepare("UPDATE geographylevel2 SET AdminLevel2 = ?, GL1ID = ? WHERE GL2ID = ?");
        $stmt->bind_param("sii", $adminLevel2, $gl1ID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_geography_level2.php");
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
        WHERE REFERENCED_TABLE_NAME = 'geographylevel2' AND REFERENCED_COLUMN_NAME = 'GL2ID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE GL2ID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_geography_level2.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM geographylevel2 WHERE GL2ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_geography_level2.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Geography Level 2 Table</title>
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
        <h1>Modify Geography Level 2 Table</h1>
        
        <!-- Create Form -->
        <form method="post" action="input_geography_level2.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="adminLevel2">Admin Level 2:</label>
                <input type="text" id="adminLevel2" name="adminLevel2" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="gl1ID">Admin Level 1:</label>
                <select id="gl1ID" name="gl1ID" class="form-control" required>
                    <?php
                    $gl1Result = $conn->query("SELECT GL1ID, AdminLevel1 FROM geographylevel1 ORDER BY AdminLevel1 ASC");
                    while ($gl1Row = $gl1Result->fetch_assoc()) {
                        echo "<option value='{$gl1Row['GL1ID']}'>" . htmlspecialchars($gl1Row['AdminLevel1']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Geography Level 2 Table -->
        <h2>Table: Geography Level 2</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>GL2ID</th>
                        <th>Admin Level 2</th>
                        <th>Admin Level 1</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT GL2ID, AdminLevel2, geographylevel1.AdminLevel1 FROM geographylevel2 JOIN geographylevel1 ON geographylevel2.GL1ID = geographylevel1.GL1ID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['GL2ID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AdminLevel2']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AdminLevel1']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['GL2ID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['GL2ID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM geographylevel2 WHERE GL2ID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Geography Level 2</h2>
                <form method="post" action="input_geography_level2.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['GL2ID']); ?>">
                    <div class="form-group">
                        <label for="adminLevel2">Admin Level 2:</label>
                        <input type="text" id="adminLevel2" name="adminLevel2" class="form-control" value="<?php echo htmlspecialchars($row['AdminLevel2']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gl1ID">Admin Level 1:</label>
                        <select id="gl1ID" name="gl1ID" class="form-control" required>
                            <?php
                            $gl1Result = $conn->query("SELECT GL1ID, AdminLevel1 FROM geographylevel1 ORDER BY AdminLevel1 ASC");
                            while ($gl1Row = $gl1Result->fetch_assoc()) {
                                $selected = ($gl1Row['GL1ID'] == $row['GL1ID']) ? 'selected' : '';
                                echo "<option value='{$gl1Row['GL1ID']}' $selected>" . htmlspecialchars($gl1Row['AdminLevel1']) . "</option>";
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
