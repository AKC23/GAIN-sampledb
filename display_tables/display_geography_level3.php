<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
while ($fieldInfo = $result->fetch_field()) {
    if ($fieldInfo->name == 'GL2ID') {
        echo "<th>AdminLevel2</th>";
    } else {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
}
echo "</tr></thead><tbody>";
// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $key => $cell) {
        if ($key == 'GL2ID') {
            // Fetch the AdminLevel2 based on GL2ID
            $gl2Result = $conn->query("SELECT AdminLevel2 FROM geographylevel2 WHERE GL2ID = $cell");
            if ($gl2Row = $gl2Result->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($gl2Row['AdminLevel2']) . "</td>";
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
