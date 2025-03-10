<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $foodTypeName = $_POST['foodTypeName'];
        $vehicleID = $_POST['vehicleID'];
        // Check if the FoodTypeName already exists
        $checkQuery = $conn->prepare("SELECT * FROM foodtype WHERE FoodTypeName = ?");
        $checkQuery->bind_param("s", $foodTypeName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This FoodTypeName already exists. Please use a different name.'); window.location.href = 'input_foodtype.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO foodtype (FoodTypeName, VehicleID) VALUES (?, ?)");
            $stmt->bind_param("si", $foodTypeName, $vehicleID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_foodtype.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $foodTypeName = $_POST['foodTypeName'];
        $vehicleID = $_POST['vehicleID'];
        $stmt = $conn->prepare("UPDATE foodtype SET FoodTypeName = ?, VehicleID = ? WHERE FoodTypeID = ?");
        $stmt->bind_param("sii", $foodTypeName, $vehicleID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_foodtype.php");
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
        WHERE REFERENCED_TABLE_NAME = 'foodtype' AND REFERENCED_COLUMN_NAME = 'FoodTypeID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE FoodTypeID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_foodtype.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM foodtype WHERE FoodTypeID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_foodtype.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Food Type Table</title>
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
        <h1>Modify Food Type Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Food Type</h3>
        <form method="post" action="input_foodtype.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="foodTypeName">Food Type Name:</label>
                <input type="text" id="foodTypeName" name="foodTypeName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="vehicleID">Food Vehicle:</label>
                <select id="vehicleID" name="vehicleID" class="form-control" required>
                    <?php
                    $foodVehicleResult = $conn->query("SELECT VehicleID, VehicleName FROM foodvehicle ORDER BY VehicleName ASC");
                    while ($foodVehicleRow = $foodVehicleResult->fetch_assoc()) {
                        echo "<option value='{$foodVehicleRow['VehicleID']}'>" . htmlspecialchars($foodVehicleRow['VehicleName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Food Type Table -->
        <h2>Table: Food Type</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Food Type ID</th>
                        <th>Food Type Name</th>
                        <th>Food Vehicle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT ft.FoodTypeID, ft.FoodTypeName, fv.VehicleName FROM foodtype ft JOIN foodvehicle fv ON ft.VehicleID = fv.VehicleID ORDER BY ft.FoodTypeID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['FoodTypeID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['FoodTypeName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['FoodTypeID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['FoodTypeID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM foodtype WHERE FoodTypeID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Food Type</h2>
                <form method="post" action="input_foodtype.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['FoodTypeID']); ?>">
                    <div class="form-group">
                        <label for="foodTypeName">Food Type Name:</label>
                        <input type="text" id="foodTypeName" name="foodTypeName" class="form-control" value="<?php echo htmlspecialchars($row['FoodTypeName']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="vehicleID">Food Vehicle:</label>
                        <select id="vehicleID" name="vehicleID" class="form-control" required>
                            <?php
                            $foodVehicleResult = $conn->query("SELECT VehicleID, VehicleName FROM foodvehicle ORDER BY VehicleName ASC");
                            while ($foodVehicleRow = $foodVehicleResult->fetch_assoc()) {
                                $selected = ($foodVehicleRow['VehicleID'] == $row['VehicleID']) ? 'selected' : '';
                                echo "<option value='{$foodVehicleRow['VehicleID']}' $selected>" . htmlspecialchars($foodVehicleRow['VehicleName']) . "</option>";
                            }
                            ?>
                        </select>
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
