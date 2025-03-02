<?php
// input_geography_level1.php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $adminLevel1 = $_POST['adminLevel1'];
        $countryID = $_POST['countryID'];
        // Check if the combination of AdminLevel1 and CountryID already exists
        $checkQuery = $conn->prepare("SELECT * FROM geographylevel1 WHERE AdminLevel1 = ? AND CountryID = ?");
        $checkQuery->bind_param("si", $adminLevel1, $countryID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of AdminLevel1 and CountryID already exists. Please use a different combination.'); window.location.href = 'input_geography_level1.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO geographylevel1 (AdminLevel1, CountryID) VALUES (?, ?)");
            $stmt->bind_param("si", $adminLevel1, $countryID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_geography_level1.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $adminLevel1 = $_POST['adminLevel1'];
        $countryID = $_POST['countryID'];
        $stmt = $conn->prepare("UPDATE geographylevel1 SET AdminLevel1 = ?, CountryID = ? WHERE GL1ID = ?");
        $stmt->bind_param("sii", $adminLevel1, $countryID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_geography_level1.php");
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
        WHERE REFERENCED_TABLE_NAME = 'geographylevel1' AND REFERENCED_COLUMN_NAME = 'GL1ID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE GL1ID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_geography_level1.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM geographylevel1 WHERE GL1ID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_geography_level1.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Geography Level 1 Table</title>
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
        <h1>Modify Geography Level 1 Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Informations</h3>
        <form method="post" action="input_geography_level1.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="adminLevel1">Admin Level 1:</label>
                <input type="text" id="adminLevel1" name="adminLevel1" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="countryID">Country Name:</label>
                <select id="countryID" name="countryID" class="form-control" required>
                    <?php
                    $countryResult = $conn->query("SELECT CountryID, CountryName FROM country ORDER BY CountryName ASC");
                    while ($countryRow = $countryResult->fetch_assoc()) {
                        echo "<option value='{$countryRow['CountryID']}'>" . htmlspecialchars($countryRow['CountryName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Geography Level 1 Table -->
        <h2>Table: Geography Level 1</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>GL1ID</th>
                        <th>Admin Level 1</th>
                        <th>Country Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT GL1ID, AdminLevel1, country.CountryName FROM geographylevel1 JOIN country ON geographylevel1.CountryID = country.CountryID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['GL1ID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AdminLevel1']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CountryName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['GL1ID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['GL1ID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM geographylevel1 WHERE GL1ID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Geography Level 1</h2>
                <form method="post" action="input_geography_level1.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['GL1ID']); ?>">
                    <div class="form-group">
                        <label for="adminLevel1">Admin Level 1:</label>
                        <input type="text" id="adminLevel1" name="adminLevel1" class="form-control" value="<?php echo htmlspecialchars($row['AdminLevel1']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="countryID">Country Name:</label>
                        <select id="countryID" name="countryID" class="form-control" required>
                            <?php
                            $countryResult = $conn->query("SELECT CountryID, CountryName FROM country ORDER BY CountryName ASC");
                            while ($countryRow = $countryResult->fetch_assoc()) {
                                $selected = ($countryRow['CountryID'] == $row['CountryID']) ? 'selected' : '';
                                echo "<option value='{$countryRow['CountryID']}' $selected>" . htmlspecialchars($countryRow['CountryName']) . "</option>";
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
