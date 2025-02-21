<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
echo "<th>AMEID</th>";
echo "<th>AME</th>";
echo "<th>GenderName</th>";
echo "<th>AgeRange</th>";
echo "</tr></thead><tbody>";

// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['AMEID']) . "</td>";
    echo "<td>" . htmlspecialchars($row['AME']) . "</td>";

    // Fetch GenderName from gender table
    $genderID = htmlspecialchars($row['GenderID']);
    $genderQuery = $conn->query("SELECT GenderName FROM gender WHERE GenderID = $genderID");
    if ($genderRow = $genderQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($genderRow['GenderName']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    // Fetch AgeRange from age table
    $ageID = htmlspecialchars($row['AgeID']);
    $ageQuery = $conn->query("SELECT AgeRange FROM age WHERE AgeID = $ageID");
    if ($ageRow = $ageQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($ageRow['AgeRange']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    echo "</tr>";
}
echo "</tbody></table></div>";
?>
