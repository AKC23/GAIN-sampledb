<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
while ($fieldInfo = $result->fetch_field()) {
    if ($fieldInfo->name == 'GL1ID') {
        echo "<th>AdminLevel1</th>";
    } else {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
}
echo "</tr></thead><tbody>";
// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $key => $cell) {
        if ($key == 'GL1ID') {
            // Fetch the AdminLevel1 based on GL1ID
            $gl1Result = $conn->query("SELECT AdminLevel1 FROM geographylevel1 WHERE GL1ID = $cell");
            if ($gl1Row = $gl1Result->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($gl1Row['AdminLevel1']) . "</td>";
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
