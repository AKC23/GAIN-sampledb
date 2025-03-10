<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $currencyName = $_POST['currencyName'];
        $currencyValue = $_POST['currencyValue'];
        // Check if the CurrencyName already exists
        $checkQuery = $conn->prepare("SELECT * FROM measurecurrency WHERE CurrencyName = ?");
        $checkQuery->bind_param("s", $currencyName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This CurrencyName already exists. Please use a different name.'); window.location.href = 'input_measure_currency.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO measurecurrency (CurrencyName, CurrencyValue) VALUES (?, ?)");
            $stmt->bind_param("sd", $currencyName, $currencyValue);
            $stmt->execute();
            $stmt->close();
            header("Location: input_measure_currency.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $currencyName = $_POST['currencyName'];
        $currencyValue = $_POST['currencyValue'];
        $stmt = $conn->prepare("UPDATE measurecurrency SET CurrencyName = ?, CurrencyValue = ? WHERE MCID = ?");
        $stmt->bind_param("sdi", $currencyName, $currencyValue, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_measure_currency.php");
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
        WHERE REFERENCED_TABLE_NAME = 'measurecurrency' AND REFERENCED_COLUMN_NAME = 'MCID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE MCID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_measure_currency.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM measurecurrency WHERE MCID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_measure_currency.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Measure Currency Table</title>
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
        <h1>Modify Measure Currency Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Measure Currency</h3>
        <form method="post" action="input_measure_currency.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="currencyName">Currency Name:</label>
                <input type="text" id="currencyName" name="currencyName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="currencyValue">Currency Value:</label>
                <input type="number" step="0.000000000001" id="currencyValue" name="currencyValue" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Measure Currency Table -->
        <h2>Table: Measure Currency</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>MCID</th>
                        <th>Currency Name</th>
                        <th>Currency Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM measurecurrency ORDER BY MCID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['MCID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CurrencyName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CurrencyValue']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['MCID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['MCID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM measurecurrency WHERE MCID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Measure Currency</h2>
                <form method="post" action="input_measure_currency.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['MCID']); ?>">
                    <div class="form-group">
                        <label for="currencyName">Currency Name:</label>
                        <input type="text" id="currencyName" name="currencyName" class="form-control" value="<?php echo htmlspecialchars($row['CurrencyName']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="currencyValue">Currency Value:</label>
                        <input type="number" step="0.000000000001" id="currencyValue" name="currencyValue" class="form-control" value="<?php echo htmlspecialchars($row['CurrencyValue']); ?>" required>
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
