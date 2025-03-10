<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $yearTypeName = $_POST['yearTypeName'];
        $startMonth = $_POST['startMonth'];
        $endMonth = $_POST['endMonth'];
        // Check if the YearTypeName already exists
        $checkQuery = $conn->prepare("SELECT * FROM yeartype WHERE YearTypeName = ?");
        $checkQuery->bind_param("s", $yearTypeName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This YearTypeName already exists. Please use a different name.'); window.location.href = 'input_year_type.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO yeartype (YearTypeName, StartMonth, EndMonth) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $yearTypeName, $startMonth, $endMonth);
            $stmt->execute();
            $stmt->close();
            header("Location: input_year_type.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $yearTypeName = $_POST['yearTypeName'];
        $startMonth = $_POST['startMonth'];
        $endMonth = $_POST['endMonth'];
        $stmt = $conn->prepare("UPDATE yeartype SET YearTypeName = ?, StartMonth = ?, EndMonth = ? WHERE YearTypeID = ?");
        $stmt->bind_param("sssi", $yearTypeName, $startMonth, $endMonth, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_year_type.php");
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
        WHERE REFERENCED_TABLE_NAME = 'yeartype' AND REFERENCED_COLUMN_NAME = 'YearTypeID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE YearTypeID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_year_type.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM yeartype WHERE YearTypeID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_year_type.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Year Type Table</title>
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
        <h1>Modify Year Type Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Year Type</h3>
        <form method="post" action="input_year_type.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="yearTypeName">Year Type Name (Example: Year (Jan-Dec): </label>
                <input type="text" id="yearTypeName" name="yearTypeName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="startMonth">Start Month (Example: May):</label>
                <input type="text" id="startMonth" name="startMonth" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="endMonth">End Month (Example: May):</label>
                <input type="text" id="endMonth" name="endMonth" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Year Type Table -->
        <h2>Table: Year Type</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Year Type ID</th>
                        <th>Year Type Name</th>
                        <th>Start Month</th>
                        <th>End Month</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM yeartype ORDER BY YearTypeID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['YearTypeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['YearTypeName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['StartMonth']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['EndMonth']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['YearTypeID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['YearTypeID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM yeartype WHERE YearTypeID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Year Type</h2>
                <form method="post" action="input_year_type.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['YearTypeID']); ?>">
                    <div class="form-group">
                        <label for="yearTypeName">Year Type Name:</label>
                        <input type="text" id="yearTypeName" name="yearTypeName" class="form-control" value="<?php echo htmlspecialchars($row['YearTypeName']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="startMonth">Start Month:</label>
                        <input type="text" id="startMonth" name="startMonth" class="form-control" value="<?php echo htmlspecialchars($row['StartMonth']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="endMonth">End Month:</label>
                        <input type="text" id="endMonth" name="endMonth" class="form-control" value="<?php echo htmlspecialchars($row['EndMonth']); ?>" required>
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
