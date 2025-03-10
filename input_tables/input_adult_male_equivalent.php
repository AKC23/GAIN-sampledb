<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $ame = $_POST['ame'];
        $genderID = $_POST['genderID'];
        $ageID = $_POST['ageID'];

        // Check if the combination of AME, GenderID, and AgeID already exists
        $checkQuery = $conn->prepare("SELECT * FROM adultmaleequivalent WHERE AME = ? AND GenderID = ? AND AgeID = ?");
        $checkQuery->bind_param("dii", $ame, $genderID, $ageID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of AME, GenderID, and AgeID already exists. Please use a different combination.'); window.location.href = 'input_adult_male_equivalent.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO adultmaleequivalent (AME, GenderID, AgeID) VALUES (?, ?, ?)");
            $stmt->bind_param("dii", $ame, $genderID, $ageID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_adult_male_equivalent.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $ame = $_POST['ame'];
        $genderID = $_POST['genderID'];
        $ageID = $_POST['ageID'];
        $stmt = $conn->prepare("UPDATE adultmaleequivalent SET AME = ?, GenderID = ?, AgeID = ? WHERE AMEID = ?");
        $stmt->bind_param("diii", $ame, $genderID, $ageID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_adult_male_equivalent.php");
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
        WHERE REFERENCED_TABLE_NAME = 'adultmaleequivalent' AND REFERENCED_COLUMN_NAME = 'AMEID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE AMEID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_adult_male_equivalent.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM adultmaleequivalent WHERE AMEID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_adult_male_equivalent.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Adult Male Equivalent Table</title>
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
        <h1>Modify Adult Male Equivalent Table</h1>
        
        <!-- Create Form -->
        <form method="post" action="input_adult_male_equivalent.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="ame">AME:</label>
                <input type="number" step="0.01" id="ame" name="ame" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="genderID">Gender:</label>
                <select id="genderID" name="genderID" class="form-control" required>
                    <?php
                    $genderResult = $conn->query("SELECT GenderID, GenderName FROM gender ORDER BY GenderName ASC");
                    while ($genderRow = $genderResult->fetch_assoc()) {
                        echo "<option value='{$genderRow['GenderID']}'>" . htmlspecialchars($genderRow['GenderName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="ageID">Age:</label>
                <select id="ageID" name="ageID" class="form-control" required>
                    <?php
                    $ageResult = $conn->query("SELECT AgeID, AgeName FROM age ORDER BY AgeName ASC");
                    while ($ageRow = $ageResult->fetch_assoc()) {
                        echo "<option value='{$ageRow['AgeID']}'>" . htmlspecialchars($ageRow['AgeName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Adult Male Equivalent Table -->
        <h2>Table: Adult Male Equivalent</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>AME ID</th>
                        <th>AME</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT ame.AMEID, ame.AME, g.GenderName, a.AgeName FROM adultmaleequivalent ame JOIN gender g ON ame.GenderID = g.GenderID JOIN age a ON ame.AgeID = a.AgeID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['AMEID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AME']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['GenderName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AgeName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['AMEID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['AMEID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM adultmaleequivalent WHERE AMEID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Adult Male Equivalent</h2>
                <form method="post" action="input_adult_male_equivalent.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['AMEID']); ?>">
                    <div class="form-group">
                        <label for="ame">AME:</label>
                        <input type="number" step="0.01" id="ame" name="ame" class="form-control" value="<?php echo htmlspecialchars($row['AME']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="genderID">Gender:</label>
                        <select id="genderID" name="genderID" class="form-control" required>
                            <?php
                            $genderResult = $conn->query("SELECT GenderID, GenderName FROM gender ORDER BY GenderName ASC");
                            while ($genderRow = $genderResult->fetch_assoc()) {
                                $selected = ($genderRow['GenderID'] == $row['GenderID']) ? 'selected' : '';
                                echo "<option value='{$genderRow['GenderID']}' $selected>" . htmlspecialchars($genderRow['GenderName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="ageID">Age:</label>
                        <select id="ageID" name="ageID" class="form-control" required>
                            <?php
                            $ageResult = $conn->query("SELECT AgeID, AgeName FROM age ORDER BY AgeName ASC");
                            while ($ageRow = $ageResult->fetch_assoc()) {
                                $selected = ($ageRow['AgeID'] == $row['AgeID']) ? 'selected' : '';
                                echo "<option value='{$ageRow['AgeID']}' $selected>" . htmlspecialchars($ageRow['AgeName']) . "</option>";
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
