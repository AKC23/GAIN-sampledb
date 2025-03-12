<?php
include('../db_connect.php');

// Helper to fetch dropdown options
function fetchOptions($conn, $table, $idField, $nameField) {
    $options = [];
    $result = $conn->query("SELECT $idField, $nameField FROM $table ORDER BY $nameField");
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
    return $options;
}

// Fetch dropdown data
$companies  = fetchOptions($conn, 'company', 'CompanyID', 'CompanyName');
$vehicles   = fetchOptions($conn, 'foodvehicle', 'VehicleID', 'VehicleName');
$levels1    = fetchOptions($conn, 'geographylevel1', 'GL1ID', 'AdminLevel1');
$levels2    = fetchOptions($conn, 'geographylevel2', 'GL2ID', 'AdminLevel2');
$levels3    = fetchOptions($conn, 'geographylevel3', 'GL3ID', 'AdminLevel3');
$countries  = fetchOptions($conn, 'country', 'CountryID', 'CountryName');

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $producer = $_POST['ProducerProcessorName'];
        $companyID = $_POST['CompanyID'];
        $vehicleID = $_POST['VehicleID'];
        $gl1ID = $_POST['GL1ID'];
        $gl2ID = $_POST['GL2ID'];
        $gl3ID = $_POST['GL3ID'];
        $countryID = $_POST['CountryID'];

        $sql = "INSERT INTO entity (ProducerProcessorName, CompanyID, VehicleID, GL1ID, GL2ID, GL3ID, CountryID)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiiiii", $producer, $companyID, $vehicleID, $gl1ID, $gl2ID, $gl3ID, $countryID);
        if ($stmt->execute()) {
            echo "✓ Entity record inserted successfully.";
        } else {
            echo "Error inserting entity record: " . $stmt->error;
        }
        $stmt->close();
        header("Location: input_entity.php");
        exit;
    }
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $entityID  = $_POST['EntityID'];
        $producer  = $_POST['ProducerProcessorName'];
        $companyID = $_POST['CompanyID'];
        $vehicleID = $_POST['VehicleID'];
        $gl1ID     = $_POST['GL1ID'];
        $gl2ID     = $_POST['GL2ID'];
        $gl3ID     = $_POST['GL3ID'];
        $countryID = $_POST['CountryID'];

        $sql = "UPDATE entity
                SET ProducerProcessorName = ?,
                    CompanyID = ?,
                    VehicleID = ?,
                    GL1ID = ?,
                    GL2ID = ?,
                    GL3ID = ?,
                    CountryID = ?
                WHERE EntityID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiiiiii", $producer, $companyID, $vehicleID, $gl1ID, $gl2ID, $gl3ID, $countryID, $entityID);
        if ($stmt->execute()) {
            echo "✓ Entity record updated successfully.";
        } else {
            echo "Error updating entity record: " . $stmt->error;
        }
        $stmt->close();
        header("Location: input_entity.php");
        exit;
    }
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $entityID = $_GET['id'];
    $sql = "DELETE FROM entity WHERE EntityID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $entityID);
    if ($stmt->execute()) {
        echo "✓ Entity record deleted successfully.";
    } else {
        echo "Error deleting entity record: " . $stmt->error;
    }
    $stmt->close();
    header("Location: input_entity.php");
    exit;
}

// Fetch existing entity records with LEFT JOIN for names
$entities = $conn->query("
    SELECT e.*,
           c.CompanyName,
           v.VehicleName,
           g1.AdminLevel1,
           g2.AdminLevel2,
           g3.AdminLevel3,
           co.CountryName
    FROM entity e
    LEFT JOIN company c ON e.CompanyID = c.CompanyID
    LEFT JOIN foodvehicle v ON e.VehicleID = v.VehicleID
    LEFT JOIN geographylevel1 g1 ON e.GL1ID = g1.GL1ID
    LEFT JOIN geographylevel2 g2 ON e.GL2ID = g2.GL2ID
    LEFT JOIN geographylevel3 g3 ON e.GL3ID = g3.GL3ID
    LEFT JOIN country co ON e.CountryID = co.CountryID
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Input Entity</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <style>
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
    <h1>Entity Table</h1>

    <!-- Create Form -->
    <h3>Add New Entity</h3>
    <form method="post" action="input_entity.php" class="mb-4">
        <input type="hidden" name="action" value="create">

        <label for="ProducerProcessorName">Producer/Processor Name:</label>
        <input type="text" name="ProducerProcessorName" class="form-control"><br>

        <label for="CompanyID">Company:</label>
        <select name="CompanyID" class="form-control">
            <?php foreach ($companies as $c): ?>
                <option value="<?= $c['CompanyID'] ?>"><?= $c['CompanyName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="VehicleID">Vehicle:</label>
        <select name="VehicleID" class="form-control">
            <?php foreach ($vehicles as $v): ?>
                <option value="<?= $v['VehicleID'] ?>"><?= $v['VehicleName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="GL1ID">Admin Level 1:</label>
        <select name="GL1ID" class="form-control">
            <option value="">-- None --</option>
            <?php foreach ($levels1 as $l1): ?>
                <option value="<?= $l1['GL1ID'] ?>"><?= $l1['AdminLevel1'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="GL2ID">Admin Level 2:</label>
        <select name="GL2ID" class="form-control">
            <option value="">-- None --</option>
            <?php foreach ($levels2 as $l2): ?>
                <option value="<?= $l2['GL2ID'] ?>"><?= $l2['AdminLevel2'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="GL3ID">Admin Level 3:</label>
        <select name="GL3ID" class="form-control">
            <option value="">-- None --</option>
            <?php foreach ($levels3 as $l3): ?>
                <option value="<?= $l3['GL3ID'] ?>"><?= $l3['AdminLevel3'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="CountryID">Country:</label>
        <select name="CountryID" class="form-control">
            <?php foreach ($countries as $co): ?>
                <option value="<?= $co['CountryID'] ?>"><?= $co['CountryName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>
    <div class="mb-5"></div>
    <h2>Existing Entities</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>EntityID</th>
                <th>Producer/Processor Name</th>
                <th>Company</th>
                <th>Vehicle</th>
                <th>Admin Level 1</th>
                <th>Admin Level 2</th>
                <th>Admin Level 3</th>
                <th>Country</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $entities->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['EntityID'] ?></td>
                    <td><?= $row['ProducerProcessorName'] ?></td>
                    <td><?= $row['CompanyName'] ?></td>
                    <td><?= $row['VehicleName'] ?></td>
                    <td><?= $row['AdminLevel1'] ?></td>
                    <td><?= $row['AdminLevel2'] ?></td>
                    <td><?= $row['AdminLevel3'] ?></td>
                    <td><?= $row['CountryName'] ?></td>
                    <td>
                        <a href="?action=edit&id=<?= $row['EntityID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?action=delete&id=<?= $row['EntityID'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this record?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="mb-5"></div>

    <?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])): ?>
        <?php
        $entityID = $_GET['id'];
        $result = $conn->query("SELECT * FROM entity WHERE EntityID = $entityID");
        if ($row = $result->fetch_assoc()):
        ?>
            <h2>Edit Entity</h2>
            <form method="post" action="input_entity.php" class="mb-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="EntityID" value="<?= $row['EntityID'] ?>">

                <label for="ProducerProcessorName">Producer/Processor Name:</label>
                <input type="text" name="ProducerProcessorName" class="form-control" value="<?= $row['ProducerProcessorName'] ?>"><br>

                <label for="CompanyID">Company:</label>
                <select name="CompanyID" class="form-control">
                    <?php foreach ($companies as $c): ?>
                        <option value="<?= $c['CompanyID'] ?>" <?= ($row['CompanyID'] == $c['CompanyID']) ? 'selected' : '' ?>>
                            <?= $c['CompanyName'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="VehicleID">Vehicle:</label>
                <select name="VehicleID" class="form-control">
                    <?php foreach ($vehicles as $v): ?>
                        <option value="<?= $v['VehicleID'] ?>" <?= ($row['VehicleID'] == $v['VehicleID']) ? 'selected' : '' ?>>
                            <?= $v['VehicleName'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="GL1ID">Admin Level 1:</label>
                <select name="GL1ID" class="form-control">
                    <option value="">-- None --</option>
                    <?php foreach ($levels1 as $l1): ?>
                        <option value="<?= $l1['GL1ID'] ?>" <?= ($row['GL1ID'] == $l1['GL1ID']) ? 'selected' : '' ?>>
                            <?= $l1['AdminLevel1'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="GL2ID">Admin Level 2:</label>
                <select name="GL2ID" class="form-control">
                    <option value="">-- None --</option>
                    <?php foreach ($levels2 as $l2): ?>
                        <option value="<?= $l2['GL2ID'] ?>" <?= ($row['GL2ID'] == $l2['GL2ID']) ? 'selected' : '' ?>>
                            <?= $l2['AdminLevel2'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="GL3ID">Admin Level 3:</label>
                <select name="GL3ID" class="form-control">
                    <option value="">-- None --</option>
                    <?php foreach ($levels3 as $l3): ?>
                        <option value="<?= $l3['GL3ID'] ?>" <?= ($row['GL3ID'] == $l3['GL3ID']) ? 'selected' : '' ?>>
                            <?= $l3['AdminLevel3'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="CountryID">Country:</label>
                <select name="CountryID" class="form-control">
                    <?php foreach ($countries as $co): ?>
                        <option value="<?= $co['CountryID'] ?>" <?= ($row['CountryID'] == $co['CountryID']) ? 'selected' : '' ?>>
                            <?= $co['CountryName'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <button type="submit" class="btn btn-primary mt-2">Update</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
