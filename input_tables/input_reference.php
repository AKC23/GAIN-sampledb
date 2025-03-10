<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $referenceNumber = $_POST['referenceNumber'];
        $source = $_POST['source'];
        $link = $_POST['link'];
        $processToObtainData = $_POST['processToObtainData'];
        $accessDate = $_POST['accessDate'];
        // Check if the ReferenceNumber already exists
        $checkQuery = $conn->prepare("SELECT * FROM reference WHERE ReferenceNumber = ?");
        $checkQuery->bind_param("i", $referenceNumber);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This ReferenceNumber already exists. Please use a different number.'); window.location.href = 'input_reference.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO reference (ReferenceNumber, Source, Link, ProcessToObtainData, AccessDate) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $referenceNumber, $source, $link, $processToObtainData, $accessDate);
            $stmt->execute();
            $stmt->close();
            header("Location: input_reference.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $referenceNumber = $_POST['referenceNumber'];
        $source = $_POST['source'];
        $link = $_POST['link'];
        $processToObtainData = $_POST['processToObtainData'];
        $accessDate = $_POST['accessDate'];
        $stmt = $conn->prepare("UPDATE reference SET ReferenceNumber = ?, Source = ?, Link = ?, ProcessToObtainData = ?, AccessDate = ? WHERE ReferenceID = ?");
        $stmt->bind_param("issssi", $referenceNumber, $source, $link, $processToObtainData, $accessDate, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_reference.php");
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
        WHERE REFERENCED_TABLE_NAME = 'reference' AND REFERENCED_COLUMN_NAME = 'ReferenceID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE ReferenceID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_reference.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM reference WHERE ReferenceID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_reference.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Reference Table</title>
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
        <h1>Modify Reference Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Reference</h3>
        <form method="post" action="input_reference.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="referenceNumber">Reference Number:</label>
                <input type="number" id="referenceNumber" name="referenceNumber" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="source">Source:</label>
                <input type="text" id="source" name="source" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="link">Link:</label>
                <input type="text" id="link" name="link" class="form-control">
            </div>
            <div class="form-group">
                <label for="processToObtainData">Process To Obtain Data:</label>
                <input type="text" id="processToObtainData" name="processToObtainData" class="form-control">
            </div>
            <div class="form-group">
                <label for="accessDate">Access Date:</label>
                <input type="date" id="accessDate" name="accessDate" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Reference Table -->
        <h2>Table: Reference</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Reference ID</th>
                        <th>Reference Number</th>
                        <th>Source</th>
                        <th>Link</th>
                        <th>Process To Obtain Data</th>
                        <th>Access Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM reference ORDER BY ReferenceID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['ReferenceID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ReferenceNumber']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Source']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Link']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['ProcessToObtainData']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['AccessDate']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['ReferenceID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['ReferenceID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM reference WHERE ReferenceID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Reference</h2>
                <form method="post" action="input_reference.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['ReferenceID']); ?>">
                    <div class="form-group">
                        <label for="referenceNumber">Reference Number:</label>
                        <input type="number" id="referenceNumber" name="referenceNumber" class="form-control" value="<?php echo htmlspecialchars($row['ReferenceNumber']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="source">Source:</label>
                        <input type="text" id="source" name="source" class="form-control" value="<?php echo htmlspecialchars($row['Source']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="link">Link:</label>
                        <input type="text" id="link" name="link" class="form-control" value="<?php echo htmlspecialchars($row['Link']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="processToObtainData">Process To Obtain Data:</label>
                        <input type="text" id="processToObtainData" name="processToObtainData" class="form-control" value="<?php echo htmlspecialchars($row['ProcessToObtainData']); ?>">
                    </div>
                    <div class="form-group">
                        <label for="accessDate">Access Date:</label>
                        <input type="date" id="accessDate" name="accessDate" class="form-control" value="<?php echo htmlspecialchars($row['AccessDate']); ?>">
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
