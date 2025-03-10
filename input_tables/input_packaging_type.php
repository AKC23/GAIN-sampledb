<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $packagingTypeName = $_POST['packagingTypeName'];
        // Check if the PackagingTypeName already exists
        $checkQuery = $conn->prepare("SELECT * FROM packagingtype WHERE PackagingTypeName = ?");
        $checkQuery->bind_param("s", $packagingTypeName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This PackagingTypeName already exists. Please use a different name.'); window.location.href = 'input_packaging_type.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO packagingtype (PackagingTypeName) VALUES (?)");
            $stmt->bind_param("s", $packagingTypeName);
            $stmt->execute();
            $stmt->close();
            header("Location: input_packaging_type.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $packagingTypeName = $_POST['packagingTypeName'];
        $stmt = $conn->prepare("UPDATE packagingtype SET PackagingTypeName = ? WHERE PackagingTypeID = ?");
        $stmt->bind_param("si", $packagingTypeName, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_packaging_type.php");
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
        WHERE REFERENCED_TABLE_NAME = 'packagingtype' AND REFERENCED_COLUMN_NAME = 'PackagingTypeID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE PackagingTypeID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_packaging_type.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM packagingtype WHERE PackagingTypeID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_packaging_type.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Packaging Type Table</title>
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
        <h1>Modify Packaging Type Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Packaging Type</h3>
        <form method="post" action="input_packaging_type.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="packagingTypeName">Packaging Type Name:</label>
                <input type="text" id="packagingTypeName" name="packagingTypeName" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Packaging Type Table -->
        <h2>Table: Packaging Type</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Packaging Type ID</th>
                        <th>Packaging Type Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM packagingtype ORDER BY PackagingTypeID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['PackagingTypeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['PackagingTypeName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['PackagingTypeID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['PackagingTypeID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM packagingtype WHERE PackagingTypeID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Packaging Type</h2>
                <form method="post" action="input_packaging_type.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['PackagingTypeID']); ?>">
                    <div class="form-group">
                        <label for="packagingTypeName">Packaging Type Name:</label>
                        <input type="text" id="packagingTypeName" name="packagingTypeName" class="form-control" value="<?php echo htmlspecialchars($row['PackagingTypeName']); ?>" required>
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
