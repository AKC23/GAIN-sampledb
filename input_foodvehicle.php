<?php
// Include the database connection
include('db_connect.php');

// Process form submissions (create, update, update specific column)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $vehicleName = $_POST['vehicleName'];
        $stmt = $conn->prepare("INSERT INTO foodvehicle (VehicleName) VALUES (?)");
        $stmt->bind_param("s", $vehicleName);
        $stmt->execute();
        $stmt->close();
        header("Location: input_foodvehicle.php");
        exit;
    }
    // UPDATE an entire row
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $vehicleName = $_POST['vehicleName'];
        $stmt = $conn->prepare("UPDATE foodvehicle SET VehicleName = ? WHERE VehicleID = ?");
        $stmt->bind_param("si", $vehicleName, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_foodvehicle.php");
        exit;
    }
    // UPDATE a specific column (for example, update only the vehicle name)
    elseif (isset($_POST['action']) && $_POST['action'] === 'update_column') {
        $id = $_POST['id'];
        $vehicleName = $_POST['vehicleName'];
        $stmt = $conn->prepare("UPDATE foodvehicle SET VehicleName = ? WHERE VehicleID = ?");
        $stmt->bind_param("si", $vehicleName, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_foodvehicle.php");
        exit;
    }
}

// Process delete requests via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM foodvehicle WHERE VehicleID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: input_foodvehicle.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Food Vehicle Table</title>
    <style>
        table { border-collapse: collapse; width: 80%; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Modify Food Vehicle Table</h1>
    
    <!-- Create Form -->
    <h2>Create New Vehicle</h2>
    <form method="post" action="input_foodvehicle.php">
        <input type="hidden" name="action" value="create">
        <label>Vehicle Name: <input type="text" name="vehicleName" required></label><br><br>
        <input type="submit" value="Create">
    </form>
    
    <!-- Vehicles Table -->
    <h2>Table: Food Vehicle</h2>
    <table>
        <tr>
            <th>Vehicle ID</th>
            <th>Vehicle Name</th>
            <th>Actions</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM foodvehicle");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['VehicleID']) . "</td>";
            echo "<td>" . htmlspecialchars($row['VehicleName']) . "</td>";
            echo "<td>";
            echo "<a href='?action=edit&id=" . $row['VehicleID'] . "'>Edit</a> | ";
            echo "<a href='?action=delete&id=" . $row['VehicleID'] . "' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    
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
            <form method="post" action="input_foodvehicle.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['VehicleID']); ?>">
                <label>Vehicle Name: <input type="text" name="vehicleName" value="<?php echo htmlspecialchars($row['VehicleName']); ?>" required></label><br><br>
                <input type="submit" value="Update">
            </form>
            <h3>Update Specific Column (Vehicle Name Only)</h3>
            <form method="post" action="input_foodvehicle.php">
                <input type="hidden" name="action" value="update_column">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['VehicleID']); ?>">
                <label>New Vehicle Name: <input type="text" name="vehicleName" value="<?php echo htmlspecialchars($row['VehicleName']); ?>" required></label><br><br>
                <input type="submit" value="Update Name">
            </form>
            <?php
        }
        $stmt->close();
    }
    
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
