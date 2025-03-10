<?php
// Include the database connection
include('../db_connect.php');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE a new record
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $subDistributionChannelName = $_POST['subDistributionChannelName'];
        $distributionChannelID = $_POST['distributionChannelID'];
        // Check if the SubDistributionChannelName already exists
        $checkQuery = $conn->prepare("SELECT * FROM subdistributionchannel WHERE SubDistributionChannelName = ?");
        $checkQuery->bind_param("s", $subDistributionChannelName);
        $checkQuery->execute();
        $checkResult = $checkQuery->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('This SubDistributionChannelName already exists. Please use a different name.'); window.location.href = 'input_sub_distribution_channel.php';</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO subdistributionchannel (SubDistributionChannelName, DistributionChannelID) VALUES (?, ?)");
            $stmt->bind_param("si", $subDistributionChannelName, $distributionChannelID);
            $stmt->execute();
            $stmt->close();
            header("Location: input_sub_distribution_channel.php");
            exit;
        }
        $checkQuery->close();
    }
    // UPDATE a record
    elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'];
        $subDistributionChannelName = $_POST['subDistributionChannelName'];
        $distributionChannelID = $_POST['distributionChannelID'];
        $stmt = $conn->prepare("UPDATE subdistributionchannel SET SubDistributionChannelName = ?, DistributionChannelID = ? WHERE SubDistributionChannelID = ?");
        $stmt->bind_param("sii", $subDistributionChannelName, $distributionChannelID, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_sub_distribution_channel.php");
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
        WHERE REFERENCED_TABLE_NAME = 'subdistributionchannel' AND REFERENCED_COLUMN_NAME = 'SubDistributionChannelID' AND TABLE_SCHEMA = DATABASE()
    ";
    $foreignKeyResult = $conn->query($checkForeignKeyQuery);
    $isForeignKeyConstraint = false;
    $connectedTables = [];

    while ($row = $foreignKeyResult->fetch_assoc()) {
        $connectedTable = $row['TABLE_NAME'];
        $connectedTables[] = $connectedTable;
        $checkConnectedTableQuery = "SELECT * FROM $connectedTable WHERE SubDistributionChannelID = $id";
        $connectedTableResult = $conn->query($checkConnectedTableQuery);
        if ($connectedTableResult->num_rows > 0) {
            $isForeignKeyConstraint = true;
        }
    }

    if ($isForeignKeyConstraint) {
        $connectedTablesList = implode(', ', $connectedTables);
        echo "<script>alert('Cannot delete this record because it is connected to the following tables: $connectedTablesList.'); window.location.href = 'input_sub_distribution_channel.php';</script>";
    } else {
        $stmt = $conn->prepare("DELETE FROM subdistributionchannel WHERE SubDistributionChannelID = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        header("Location: input_sub_distribution_channel.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modify Sub Distribution Channel Table</title>
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
        <h1>Modify Sub Distribution Channel Table</h1>
        
        <!-- Create Form -->
        <h3>Add New Sub Distribution Channel</h3>
        <form method="post" action="input_sub_distribution_channel.php" class="mb-4">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="subDistributionChannelName">Sub Distribution Channel Name:</label>
                <input type="text" id="subDistributionChannelName" name="subDistributionChannelName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="distributionChannelID">Distribution Channel:</label>
                <select id="distributionChannelID" name="distributionChannelID" class="form-control" required>
                    <?php
                    $distributionChannelResult = $conn->query("SELECT DistributionChannelID, DistributionChannelName FROM distributionchannel ORDER BY DistributionChannelName ASC");
                    while ($distributionChannelRow = $distributionChannelResult->fetch_assoc()) {
                        echo "<option value='{$distributionChannelRow['DistributionChannelID']}'>" . htmlspecialchars($distributionChannelRow['DistributionChannelName']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Add</button>
        </form>
        
        <!-- Add space after the edit form -->
        <div class="mb-5"></div>
        
        <!-- Sub Distribution Channel Table -->
        <h2>Table: Sub Distribution Channel</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sub Distribution Channel ID</th>
                        <th>Sub Distribution Channel Name</th>
                        <th>Distribution Channel</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT sdc.SubDistributionChannelID, sdc.SubDistributionChannelName, dc.DistributionChannelName FROM subdistributionchannel sdc JOIN distributionchannel dc ON sdc.DistributionChannelID = dc.DistributionChannelID ORDER BY sdc.SubDistributionChannelID");
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['SubDistributionChannelID']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['SubDistributionChannelName']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['DistributionChannelName']) . "</td>";
                        echo "<td>";
                        echo "<a href='?action=edit&id=" . $row['SubDistributionChannelID'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                        echo "<a href='?action=delete&id=" . $row['SubDistributionChannelID'] . "' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete this record?');\">Delete</a>";
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
            $stmt = $conn->prepare("SELECT * FROM subdistributionchannel WHERE SubDistributionChannelID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                ?>
                <h2>Edit Sub Distribution Channel</h2>
                <form method="post" action="input_sub_distribution_channel.php" class="mb-4">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['SubDistributionChannelID']); ?>">
                    <div class="form-group">
                        <label for="subDistributionChannelName">Sub Distribution Channel Name:</label>
                        <input type="text" id="subDistributionChannelName" name="subDistributionChannelName" class="form-control" value="<?php echo htmlspecialchars($row['SubDistributionChannelName']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="distributionChannelID">Distribution Channel:</label>
                        <select id="distributionChannelID" name="distributionChannelID" class="form-control" required>
                            <?php
                            $distributionChannelResult = $conn->query("SELECT DistributionChannelID, DistributionChannelName FROM distributionchannel ORDER BY DistributionChannelName ASC");
                            while ($distributionChannelRow = $distributionChannelResult->fetch_assoc()) {
                                $selected = ($distributionChannelRow['DistributionChannelID'] == $row['DistributionChannelID']) ? 'selected' : '';
                                echo "<option value='{$distributionChannelRow['DistributionChannelID']}' $selected>" . htmlspecialchars($distributionChannelRow['DistributionChannelName']) . "</option>";
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
