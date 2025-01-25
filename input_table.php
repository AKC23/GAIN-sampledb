<?php
// Include the database connection
include('db_connect.php');

// Fetch all table names
$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
} else {
    die("Error fetching tables: " . $conn->error);
}

// Fetch column names for the selected table
$columns = [];
$primaryKey = '';
$nextId = '';
$tableName = $_GET['table'] ?? '';
if (!empty($tableName)) {
    $result = $conn->query("SHOW COLUMNS FROM `" . $conn->real_escape_string($tableName) . "`");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
            if ($row['Key'] == 'PRI') {
                $primaryKey = $row['Field'];
            }
        }
    } else {
        die("Error fetching columns: " . $conn->error);
    }

    // Fetch the latest ID + 1 for the primary key
    if (!empty($primaryKey)) {
        $result = $conn->query("SELECT MAX(`" . $primaryKey . "`) AS max_id FROM `" . $conn->real_escape_string($tableName) . "`");
        if ($result) {
            $row = $result->fetch_assoc();
            $nextId = $row['max_id'] + 1;
        } else {
            die("Error fetching next ID: " . $conn->error);
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $tableName = $_POST['tableName'];
    $data = [];
    foreach ($columns as $column) {
        if ($column != $primaryKey) {
            $data[$column] = $_POST[$column] ?? '';
        }
    }

    // Insert new record
    $columnsEscaped = array_map(function($col) use ($conn) {
        return "`" . $conn->real_escape_string($col) . "`";
    }, array_keys($data));
    $valuesEscaped = array_map([$conn, 'real_escape_string'], array_values($data));
    $insertSQL = "INSERT INTO `" . $conn->real_escape_string($tableName) . "` (" . implode(", ", $columnsEscaped) . ") VALUES ('" . implode("', '", $valuesEscaped) . "')";
    if ($conn->query($insertSQL) === TRUE) {
        echo "New record created successfully.";
    } else {
        echo "Error creating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Table Data</title>
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script>
        function updateForm() {
            const tableName = document.getElementById('tableName').value;
            window.location.href = 'input_table.php?table=' + tableName;
        }
    </script>
</head>

<body>
    <div class="container">
        <h1 class="center-title">Input Data for Table</h1>
        <form method="post">
            <div class="form-group row">
                <label for="tableName" class="col-sm-2 col-form-label">Select a table</label>
                <div class="col-sm-10">
                    <select id="tableName" name="tableName" class="form-control" onchange="updateForm()">
                        <option value="">Select a table</option>
                        <?php foreach ($tables as $table): ?>
                            <option value="<?php echo htmlspecialchars($table); ?>" <?php echo ($table == $tableName) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($table); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <?php if (!empty($columns)): ?>
                <?php foreach ($columns as $column): ?>
                    <div class="form-group row">
                        <label for="<?php echo htmlspecialchars($column); ?>" class="col-sm-2 col-form-label"><?php echo htmlspecialchars($column); ?></label>
                        <div class="col-sm-10">
                            <?php if ($column == $primaryKey): ?>
                                <input type="text" name="<?php echo htmlspecialchars($column); ?>" class="form-control" value="<?php echo htmlspecialchars($nextId); ?>" readonly>
                            <?php elseif ($column == 'CountryID'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT Country_ID, Country_Name FROM country");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['Country_ID']}'>{$row['Country_Name']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'VehicleID'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT VehicleID, VehicleName FROM FoodVehicle");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['VehicleID']}'>{$row['VehicleName']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'AdminLevel1'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT AdminLevel1_ID, AdminLevel1_Name FROM AdminLevel1");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['AdminLevel1_ID']}'>{$row['AdminLevel1_Name']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'AdminLevel2'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT AdminLevel2_ID, AdminLevel2_Name FROM AdminLevel2");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['AdminLevel2_ID']}'>{$row['AdminLevel2_Name']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'AdminLevel3'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT AdminLevel3_ID, AdminLevel3_Name FROM AdminLevel3");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['AdminLevel3_ID']}'>{$row['AdminLevel3_Name']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'TaskDoneByEntity'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT EntityID, EntityName FROM EntityTable");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['EntityID']}'>{$row['EntityName']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'Productioncapacityvolume'): ?>
                                <input type="number" name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                            <?php elseif ($column == 'PercentageOfCapacityUsed'): ?>
                                <input type="number" name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                            <?php elseif ($column == 'AnnualProductionSupplyVolume'): ?>
                                <input type="number" name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                            <?php elseif ($column == 'BSTIReferenceNo'): ?>
                                <input type="text" name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                            <?php else: ?>
                                <input type="text" name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
    <!-- Bootstrap JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>
