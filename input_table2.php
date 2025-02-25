<?php

// input_table2.php
// This script handles the input form and redirects to specific files based on the selected table

// Include the database connection
include('db_connect.php');

// Initialize variables
$successMessage = '';
$tableName = '';
$genderID = '';
$ageID = '';
$adultMaleEquivalentValue = '';
$nextAMEID = '';

// Fetch the latest AMEID + 1
$ameidResult = $conn->query("SELECT MAX(AMEID) AS max_id FROM adultmaleequivalent");
if ($ameidResult) {
    $ameidRow = $ameidResult->fetch_assoc();
    $nextAMEID = $ameidRow['max_id'] + 1;
} else {
    $successMessage = "Error fetching next AMEID: " . $conn->error;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $tableName = $_POST['tableName'];
    $genderID = $_POST['GenderID'];
    $ageID = $_POST['AgeID'];
    $adultMaleEquivalentValue = $_POST['AdultMaleEquivalentValue'];

    // Validate data
    if ($tableName == 'adultmaleequivalent' && !empty($genderID) && !empty($ageID) && is_numeric($adultMaleEquivalentValue)) {
        // Prepare and execute SQL query
        $sql = "INSERT INTO adultmaleequivalent (GenderID, AgeID, AdultMaleEquivalentValue) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iid", $genderID, $ageID, $adultMaleEquivalentValue);

        if ($stmt->execute()) {
            $successMessage = "New record created successfully.";
            // Reset variables
            $genderID = '';
            $ageID = '';
            $adultMaleEquivalentValue = '';
            $nextAMEID++; // Increment for next input
        } else {
            $successMessage = "Error creating record: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $successMessage = "Error: Invalid input data.";
    }
}

// Display the form to select a table
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Table</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#genderName').change(function() {
                $('#GenderID').val($(this).find(':selected').data('genderid'));
            });
            $('#ageRange').change(function() {
                $('#AgeID').val($(this).find(':selected').data('ageid'));
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Select a Table</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="tableName">Table Name:</label>
                <select name="tableName" id="tableName" class="form-control">
                    <option value="adultmaleequivalent">Adult Male Equivalent</option>
                    <!-- Add more options for other tables as needed -->
                </select>
            </div>
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['tableName'] == 'adultmaleequivalent') {
                // Include the HTML form from input_adult_male_equivalent.php
                ?>
                <div class="form-group row">
                    <label for="AMEID" class="col-sm-2 col-form-label">New AMEID:</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="AMEID" name="AMEID" value="<?php echo htmlspecialchars($nextAMEID); ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="genderName" class="col-sm-2 col-form-label">Gender Name:</label>
                    <div class="col-sm-4">
                        <select name="genderName" id="genderName" class="form-control">
                            <?php
                            $genderResult = $conn->query("SELECT GenderID, GenderName FROM gender ORDER BY GenderName ASC");
                            while ($genderRow = $genderResult->fetch_assoc()) {
                                echo "<option value='{$genderRow['GenderID']}' data-genderid='{$genderRow['GenderID']}'>{$genderRow['GenderName']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <label for="GenderID" class="col-form-label">Gender ID:</label>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="GenderID" name="GenderID" value="<?php echo htmlspecialchars($genderID); ?>" readonly style="color: black;">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="ageRange" class="col-sm-2 col-form-label">Age Range:</label>
                    <div class="col-sm-4">
                        <select name="ageRange" id="ageRange" class="form-control">
                            <?php
                            $ageResult = $conn->query("SELECT AgeID, AgeRange FROM age ORDER BY AgeRange ASC");
                            while ($ageRow = $ageResult->fetch_assoc()) {
                                echo "<option value='{$ageRow['AgeID']}' data-ageid='{$ageRow['AgeID']}'>{$ageRow['AgeRange']}</option>";
                            }
                        ?>
                    </select>
                    </div>
                     <div class="col-sm-2">
                        <label for="AgeID" class="col-form-label">Age ID:</label>
                    </div>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" id="AgeID" name="AgeID" value="<?php echo htmlspecialchars($ageID); ?>" readonly style="color: black;">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="AdultMaleEquivalentValue" class="col-sm-2 col-form-label">Adult Male Equivalent Value:</label>
                    <div class="col-sm-10">
                        <input type="text" name="AdultMaleEquivalentValue" id="AdultMaleEquivalentValue" class="form-control" value="<?php echo htmlspecialchars($adultMaleEquivalentValue); ?>" required>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
                <?php
            }
            ?>
            <?php if (!empty($successMessage)): ?>
                <div class="alert <?php echo (strpos($successMessage, 'Error') !== false) ? 'alert-danger' : 'alert-success'; ?>" role="alert">
                    <?php echo $successMessage; ?>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Bootstrap JavaScript and dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#genderName').change(function() {
                $('#GenderID').val($(this).find(':selected').data('genderid'));
            });
            $('#ageRange').change(function() {
                $('#AgeID').val($(this).find(':selected').data('ageid'));
            });
        });
    </script>
</body>
</html>
