<?php
// input_foodvehicle.php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $vehicleName = $_POST['vehicleName'];
        // Check if the vehicle name already exists (case-insensitive)
        $checkQuery = $conn->prepare("SELECT * FROM foodvehicle WHERE LOWER(VehicleName) = LOWER(?)");
        $checkQuery->bind_param("s", $vehicleName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Vehicle name already exists. Please use a different name.'); window.location.href = 'input_foodvehicle.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO foodvehicle (VehicleName) VALUES (?)");
            $stmt->bind_param("s", $vehicleName);
            $stmt->execute();
            $stmt->close();
            header("Location: input_foodvehicle.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $newVehicleName = $_POST['newVehicleName'];
        $stmt = $conn->prepare("UPDATE foodvehicle SET VehicleName = ? WHERE VehicleID = ?");
        $stmt->bind_param("si", $newVehicleName, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_foodvehicle.php");
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
        WHERE REFERENCED_TABLE_NAME = 'foodvehicle' AND REFERENCED_COLUMN_NAME = 'VehicleID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE VehicleID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this vehicle because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_foodvehicle.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM foodvehicle WHERE VehicleID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_foodvehicle.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Modify Food Vehicle Table</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table th,
        table td {
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
        <h1>Modify Food Vehicle Table</h1>

        <!-- Create Form -->
        <h3>Add New Informations</h3>
        <form method="post" action="input_foodvehicle.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="vehicleName">New Vehicle Name:</label>
                <input type="text" id="vehicleName" name="vehicleName" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        <!-- Vehicles Table -->
        <h2>Table: Food Vehicle</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Vehicle ID</th>
                        <th>Vehicle Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM foodvehicle");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['VehicleID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['VehicleID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['VehicleID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM foodvehicle WHERE VehicleID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
        ?>
                <h2>Edit Vehicle</h2>
                <form method="post" action="input_foodvehicle.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['VehicleID']); ?>">
                    <div class="form-group">
                        <label for="vehicleName">Vehicle Name:</label>
                        <input type="text" id="vehicleName" name="vehicleName" class="form-control" value="<?php echo htmlspecialchars($row['VehicleName']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="newVehicleName">New Vehicle Name:</label>
                        <input type="text" id="newVehicleName" name="newVehicleName" class="form-control" required>
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