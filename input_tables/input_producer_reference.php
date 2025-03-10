<?php
// input_tables/input_producer_reference.php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $companyID = $_POST['companyID'];
        $identifierNumber = $_POST['identifierNumber'];
        $identifierReferenceSystem = $_POST['identifierReferenceSystem'];
        $countryID = $_POST['countryID'];
        // Check if the combination of CompanyID, IdentifierNumber, and CountryID already exists
        $checkQuery = $conn->prepare("SELECT * FROM producerreference WHERE CompanyID = ? AND IdentifierNumber = ? AND CountryID = ?");
        $checkQuery->bind_param("isi", $companyID, $identifierNumber, $countryID);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This combination of CompanyID, IdentifierNumber, and CountryID already exists. Please use a different combination.'); window.location.href = 'input_producer_reference.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO producerreference (CompanyID, IdentifierNumber, IdentifierReferenceSystem, CountryID) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $companyID, $identifierNumber, $identifierReferenceSystem, $countryID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_producer_reference.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $companyID = $_POST['companyID'];
        $identifierNumber = $_POST['identifierNumber'];
        $identifierReferenceSystem = $_POST['identifierReferenceSystem'];
        $countryID = $_POST['countryID'];
        $stmt = $conn->prepare("UPDATE producerreference SET CompanyID = ?, IdentifierNumber = ?, IdentifierReferenceSystem = ?, CountryID = ? WHERE ProducerReferenceID = ?");
        $stmt->bind_param("issii", $companyID, $identifierNumber, $identifierReferenceSystem, $countryID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_producer_reference.php");
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
        WHERE REFERENCED_TABLE_NAME = 'producerreference' AND REFERENCED_COLUMN_NAME = 'ProducerReferenceID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE ProducerReferenceID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_producer_reference.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM producerreference WHERE ProducerReferenceID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_producer_reference.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Producer Reference Table</title>
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
        <h1>Modify Producer Reference Table</h1>
        
        <!-- Create Form -->
        <form method="post" action="input_producer_reference.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="companyID">Company Name:</label>
                <select id="companyID" name="companyID" class="form-control" required>
                    <?php
                    $companyResult = $conn->query("SELECT CompanyID, CompanyName FROM company ORDER BY CompanyName ASC");
                    while ($companyRow = $companyResult->fetch_assoc()) {
                        echo "<option value='{$companyRow['CompanyID']}'>" . htmlspecialchars($companyRow['CompanyName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="identifierNumber">Identifier Number:</label>
                <input type="text" id="identifierNumber" name="identifierNumber" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="identifierReferenceSystem">Identifier Reference System:</label>
                <input type="text" id="identifierReferenceSystem" name="identifierReferenceSystem" class="form-control" required>
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
        
        <!-- Producer Reference Table -->
        <h2>Table: Producer Reference</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Producer Reference ID</th>
                        <th>Company Name</th>
                        <th>Identifier Number</th>
                        <th>Identifier Reference System</th>
                        <th>Country Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT pr.ProducerReferenceID, c.CompanyName, pr.IdentifierNumber, pr.IdentifierReferenceSystem, co.CountryName FROM producerreference pr JOIN company c ON pr.CompanyID = c.CompanyID JOIN country co ON pr.CountryID = co.CountryID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ProducerReferenceID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CompanyName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['IdentifierNumber']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['IdentifierReferenceSystem']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CountryName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['ProducerReferenceID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['ProducerReferenceID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM producerreference WHERE ProducerReferenceID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Producer Reference</h2>
                <form method="post" action="input_producer_reference.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['ProducerReferenceID']); ?>">
                    <div class="form-group">
                        <label for="companyID">Company Name:</label>
                        <select id="companyID" name="companyID" class="form-control" required>
                            <?php
                            $companyResult = $conn->query("SELECT CompanyID, CompanyName FROM company ORDER BY CompanyName ASC");
                            while ($companyRow = $companyResult->fetch_assoc()) {
                                $selected = ($companyRow['CompanyID'] == $row['CompanyID']) ? 'selected' : '';
                                echo "<option value='{$companyRow['CompanyID']}' $selected>" . htmlspecialchars($companyRow['CompanyName']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="identifierNumber">Identifier Number:</label>
                        <input type="text" id="identifierNumber" name="identifierNumber" class="form-control" value="<?php echo htmlspecialchars($row['IdentifierNumber']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="identifierReferenceSystem">Identifier Reference System:</label>
                        <input type="text" id="identifierReferenceSystem" name="identifierReferenceSystem" class="form-control" value="<?php echo htmlspecialchars($row['IdentifierReferenceSystem']); ?>" required>
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
