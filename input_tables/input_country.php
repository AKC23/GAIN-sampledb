<?php
// input_country.php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $countryName = $_POST['countryName'];
        // Check if the country name already exists (case-insensitive)
        $checkQuery = $conn->prepare("SELECT * FROM country WHERE LOWER(CountryName) = LOWER(?)");
        $checkQuery->bind_param("s", $countryName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Country name already exists. Please use a different name.'); window.location.href = 'input_country.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO country (CountryName) VALUES (?)");
            $stmt->bind_param("s", $countryName);
            $stmt->execute();
            $stmt->close();
            header("Location: input_country.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $newCountryName = $_POST['newCountryName'];
        $stmt = $conn->prepare("UPDATE country SET CountryName = ? WHERE CountryID = ?");
        $stmt->bind_param("si", $newCountryName, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_country.php");
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
        WHERE REFERENCED_TABLE_NAME = 'country' AND REFERENCED_COLUMN_NAME = 'CountryID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE CountryID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this country because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_country.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM country WHERE CountryID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_country.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Country Table</title>
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
        <h1>Modify Country Table</h1>
        
        <!-- Create Form -->
        <h2>Add New Country</h2>
        <form method="post" action="input_country.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="countryName">New Country Name:</label>
                <input type="text" id="countryName" name="countryName" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Countries Table -->
        <h2>Table: Country</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Country ID</th>
                        <th>Country Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM country");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['CountryID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['CountryName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['CountryID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['CountryID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM country WHERE CountryID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Country</h2>
                <form method="post" action="input_country.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['CountryID']); ?>">
                    <div class="form-group">
                        <label for="countryName">Country Name:</label>
                        <input type="text" id="countryName" name="countryName" class="form-control" value="<?php echo htmlspecialchars($row['CountryName']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="newCountryName">New Country Name:</label>
                        <input type="text" id="newCountryName" name="newCountryName" class="form-control" required>
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
