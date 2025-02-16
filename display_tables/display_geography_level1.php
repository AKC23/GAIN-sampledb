<?php
// Include the database connection
include('db_connect.php');

$query = "SELECT * FROM geographylevel1";
$conditions = [];
if (!empty($_POST['countryName'])) {
    // Get CountryID based on CountryName
    $countryName = $_POST['countryName'];
    $countryResult = $conn->query("SELECT CountryID FROM country WHERE CountryName = '$countryName'");
    if ($countryRow = $countryResult->fetch_assoc()) {
        $countryID = $countryRow['CountryID'];
        $conditions[] = "CountryID = '$countryID'";
    }
}
if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$result = $conn->query($query);

echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
while ($fieldInfo = $result->fetch_field()) {
    if ($fieldInfo->name == 'CountryID') {
        echo "<th>CountryName</th>";
    } else {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
}
echo "</tr></thead><tbody>";
// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $key => $cell) {
        if ($key == 'CountryID') {
            // Fetch the CountryName based on CountryID
            $countryResult = $conn->query("SELECT CountryName FROM country WHERE CountryID = $cell");
            if ($countryRow = $countryResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($countryRow['CountryName']) . "</td>";
            } else {
                echo "<td>N/A</td>";
            }
        } else {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
    }
    echo "</tr>";
}
echo "</tbody></table></div>";
?>
