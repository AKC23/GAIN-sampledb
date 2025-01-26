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
$successMessage = '';
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
        $successMessage = "New record created successfully.";
        // Reset tableName to show 'Select a table' option again
        $tableName = '';
        $columns = [];
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
    <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
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
            <div class="mb-3">
                <label for="tableName" class="form-label">Select a table</label>
                <select id="tableName" name="tableName" class="form-control" onchange="updateForm()">
                    <option value="">Select a table</option>
                    <?php foreach ($tables as $table): ?>
                        <option value="<?php echo htmlspecialchars($table); ?>" <?php echo ($table == $tableName) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($table); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success" role="alert" style="color: red;">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($columns)): ?>
                <?php if ($tableName == 'country'): ?>
                    <div class="mb-3">
                        <label for="Country_ID" class="form-label">Country ID</label>
                        <input type="text" name="Country_ID" class="form-control" value="<?php echo htmlspecialchars($nextId); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="Country_Name" class="form-label">Country Name</label>
                        <input type="text" name="Country_Name" class="form-control">
                    </div>
                <?php elseif ($tableName == 'entities'): ?>
                    <div class="mb-3">
                        <label for="EntityID" class="form-label">Entity ID</label>
                        <input type="text" name="EntityID" class="form-control" value="<?php echo htmlspecialchars($nextId); ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="ProducerProcessorName" class="form-label">Producer / Processor Name</label>
                        <input type="text" name="ProducerProcessorName" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="CompanyGroup" class="form-label">Company Group</label>
                        <input type="text" name="CompanyGroup" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="VehicleID" class="form-label">Vehicle Name</label>
                        <select name="VehicleID" class="form-control">
                            <?php
                            $result = $conn->query("SELECT VehicleID, VehicleName FROM FoodVehicle ORDER BY VehicleName ASC");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['VehicleID']}'>{$row['VehicleName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="AdminLevel1" class="form-label">Admin Level 1 (Zone / District)</label>
                        <input type="text" name="AdminLevel1" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="AdminLevel2" class="form-label">Admin Level 2 (Region / State)</label>
                        <input type="text" name="AdminLevel2" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="AdminLevel3" class="form-label">Admin Level 3 (City / City Corporation)</label>
                        <input type="text" name="AdminLevel3" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="UDC" class="form-label">UDC</label>
                        <input type="text" name="UDC" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="Thana" class="form-label">Thana</label>
                        <input type="text" name="Thana" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="Upazila" class="form-label">Upazila</label>
                        <input type="text" name="Upazila" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="CountryID" class="form-label">Country Name</label>
                        <select name="CountryID" class="form-control">
                            <?php
                            $result = $conn->query("SELECT Country_ID, Country_Name FROM country ORDER BY Country_Name ASC");
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='{$row['Country_ID']}'>{$row['Country_Name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                <?php else: ?>
                    <?php foreach ($columns as $column): ?>
                        <div class="mb-3">
                            <label for="<?php echo htmlspecialchars($column); ?>" class="form-label"><?php echo htmlspecialchars($column); ?></label>
                            <?php if ($column == $primaryKey): ?>
                                <input type="text" name="<?php echo htmlspecialchars($column); ?>" class="form-control" value="<?php echo htmlspecialchars($nextId); ?>" readonly>
                            <?php elseif ($column == 'CountryID'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT Country_ID, Country_Name FROM country ORDER BY Country_Name ASC");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['Country_ID']}'>{$row['Country_Name']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'VehicleID'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT VehicleID, VehicleName FROM FoodVehicle ORDER BY VehicleName ASC");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['VehicleID']}'>{$row['VehicleName']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'AdminLevel1'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT AdminLevel1_ID, AdminLevel1_Name FROM AdminLevel1 ORDER BY AdminLevel1_Name ASC");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['AdminLevel1_ID']}'>{$row['AdminLevel1_Name']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'AdminLevel2'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT AdminLevel2_ID, AdminLevel2_Name FROM AdminLevel2 ORDER BY AdminLevel2_Name ASC");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['AdminLevel2_ID']}'>{$row['AdminLevel2_Name']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'AdminLevel3'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT AdminLevel3_ID, AdminLevel3_Name FROM AdminLevel3 ORDER BY AdminLevel3_Name ASC");
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='{$row['AdminLevel3_ID']}'>{$row['AdminLevel3_Name']}</option>";
                                    }
                                    ?>
                                </select>
                            <?php elseif ($column == 'TaskDoneByEntity'): ?>
                                <select name="<?php echo htmlspecialchars($column); ?>" class="form-control">
                                    <?php
                                    $result = $conn->query("SELECT EntityID, EntityName FROM EntityTable ORDER BY EntityName ASC");
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
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="mb-3">
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
            <?php endif; ?>
        </form>
    </div>
    
     <!-- Optional JavaScript and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap JS -->
<!--     <script src="js/bootstrap.bundle.min.js"></script> -->
</body>
</html>
