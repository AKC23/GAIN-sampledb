<?php
// input_company.php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $companyName = $_POST['companyName'];
        // Check if the company name already exists (case-insensitive)
        $checkQuery = $conn->prepare("SELECT * FROM company WHERE LOWER(CompanyName) = LOWER(?)");
        $checkQuery->bind_param("s", $companyName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Company name already exists. Please use a different name.'); window.location.href = 'input_company.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO company (CompanyName) VALUES (?)");
            $stmt->bind_param("s", $companyName);
            $stmt->execute();
            $stmt->close();
            header("Location: input_company.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $newCompanyName = $_POST['newCompanyName'];
        $stmt = $conn->prepare("UPDATE company SET CompanyName = ? WHERE CompanyID = ?");
        $stmt->bind_param("si", $newCompanyName, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_company.php");
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
        WHERE REFERENCED_TABLE_NAME = 'company' AND REFERENCED_COLUMN_NAME = 'CompanyID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE CompanyID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this company because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_company.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM company WHERE CompanyID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_company.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Company Table</title>
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
        <h1>Modify Company Table</h1>
        
        
        <form method="post" action="input_company.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="companyName">New Company Name:</label>
                <input type="text" id="companyName" name="companyName" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Companies Table -->
        <h2>Table: Company</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Company ID</th>
                        <th>Company Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM company");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['CompanyID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CompanyName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['CompanyID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['CompanyID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM company WHERE CompanyID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Company</h2>
                <form method="post" action="input_company.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['CompanyID']); ?>">
                    <div class="form-group">
                        <label for="companyName">Company Name:</label>
                        <input type="text" id="companyName" name="companyName" class="form-control" value="<?php echo htmlspecialchars($row['CompanyName']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="newCompanyName">New Company Name:</label>
                        <input type="text" id="newCompanyName" name="newCompanyName" class="form-control" required>
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
