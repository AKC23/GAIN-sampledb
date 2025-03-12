<?php
include('../db_connect.php');

function fetchOptions($conn, $table, $idField, $nameField) {
    $options = [];
    $result = $conn->query("SELECT $idField, $nameField FROM $table ORDER BY $nameField");
    while ($row = $result->fetch_assoc()) {
        $options[] = $row;
    }
    return $options;
}

$genders = fetchOptions($conn, 'gender', 'GenderID', 'GenderName');
$ages = fetchOptions($conn, 'age', 'AgeID', 'AgeRange');

// Process form submissions (create, update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $ame = (float)$_POST['AME'];
        $genderID = $_POST['GenderID'];
        $ageID = $_POST['AgeID'];
        $sql = "INSERT INTO adultmaleequivalent (AME, GenderID, AgeID) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("dii", $ame, $genderID, $ageID);
        if ($stmt->execute()) {
            echo "✓ Inserted successfully.<br>";
        }
        $stmt->close();
        header("Location: input_adult_male_equivalent.php");
        exit;

    } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $ameid = $_POST['AMEID'];
        $ame = (float)$_POST['AME'];
        $genderID = $_POST['GenderID'];
        $ageID = $_POST['AgeID'];
        $sql = "UPDATE adultmaleequivalent SET AME = ?, GenderID = ?, AgeID = ? WHERE AMEID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("diii", $ame, $genderID, $ageID, $ameid);
        if ($stmt->execute()) {
            echo "✓ Updated successfully.<br>";
        }
        $stmt->close();
        header("Location: input_adult_male_equivalent.php");
        exit;
    }
}

// Process delete requests
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $ameid = $_GET['id'];
    $sql = "DELETE FROM adultmaleequivalent WHERE AMEID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ameid);
    if ($stmt->execute()) {
        echo "✓ Deleted successfully.<br>";
    }
    $stmt->close();
    header("Location: input_adult_male_equivalent.php");
    exit;
}

// Fetch existing records
$ames = $conn->query("
    SELECT ame.*,
           g.GenderName,
           a.AgeRange
    FROM adultmaleequivalent ame
    LEFT JOIN gender g ON ame.GenderID = g.GenderID
    LEFT JOIN age a ON ame.AgeID = a.AgeID
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Adult Male Equivalent Input</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Adult Male Equivalent</h1>

    <!-- Create Form -->
    <h3>Add New Record</h3>
    <form method="post" action="input_adult_male_equivalent.php" class="mb-4">
        <input type="hidden" name="action" value="create">
        <label for="AME">AME (Example: 0.95 or 0.55): </label>
        <input type="text" name="AME" id="AME" class="form-control"><br>

        <label for="GenderID">Gender:</label>
        <select name="GenderID" id="GenderID" class="form-control">
            <?php foreach ($genders as $g): ?>
                <option value="<?= $g['GenderID'] ?>"><?= $g['GenderName'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="AgeID">Age Range:</label>
        <select name="AgeID" id="AgeID" class="form-control">
            <?php foreach ($ages as $a): ?>
                <option value="<?= $a['AgeID'] ?>"><?= $a['AgeRange'] ?></option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>
    <div class="mb-5"></div>

    <!-- Existing Records Table -->
    <h2>Existing Records</h2>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>AMEID</th>
            <th>AME</th>
            <th>Gender</th>
            <th>Age Range</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $ames->fetch_assoc()): ?>
            <tr>
                <td><?= $row['AMEID'] ?></td>
                <td><?= $row['AME'] ?></td>
                <td><?= $row['GenderName'] ?></td>
                <td><?= $row['AgeRange'] ?></td>
                <td>
                    <a href="?action=edit&id=<?= $row['AMEID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?action=delete&id=<?= $row['AMEID'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete this record?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <div class="mb-5"></div>

    <!-- Edit Form -->
    <?php if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])): ?>
        <?php
        $ameid = $_GET['id'];
        $result = $conn->query("SELECT * FROM adultmaleequivalent WHERE AMEID = $ameid");
        if ($row = $result->fetch_assoc()):
        ?>
            <h3>Edit Record</h3>
            <form method="post" action="input_adult_male_equivalent.php" class="mb-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="AMEID" value="<?= $row['AMEID'] ?>">

                <label for="AME">AME:</label>
                <input type="text" name="AME" id="AME" class="form-control" value="<?= $row['AME'] ?>"><br>

                <label for="GenderID">Gender:</label>
                <select name="GenderID" id="GenderID" class="form-control">
                    <?php foreach ($genders as $g): ?>
                        <option value="<?= $g['GenderID'] ?>" <?= $row['GenderID'] == $g['GenderID'] ? 'selected' : '' ?>>
                            <?= $g['GenderName'] ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>

                <label for="AgeID">Age Range:</label>
                <select name="AgeID" id="AgeID" class="form-control">
                    <?php foreach ($ages as $a): ?>
                        <option value="<?= $a['AgeID'] ?>" <?= $row['AgeID'] == $a['AgeID'] ? 'selected' : '' ?>>
                            <?= $a['AgeRange'] ?>
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