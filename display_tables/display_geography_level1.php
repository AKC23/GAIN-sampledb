<?php
// Include the database connection
include('db_connect.php');

$sql = "
SELECT 
    gl1.GL1ID,
    gl1.AdminLevel1,
    c.CountryName
FROM 
    geographylevel1 gl1
JOIN 
    country c ON gl1.CountryID = c.CountryID
ORDER BY 
    gl1.GL1ID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    // Fetch and display table headers
    while ($fieldInfo = $result->fetch_field()) {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
    echo "</tr></thead><tbody>";
    // Fetch and display table rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo 'No records found';
}

$conn->close();
?>
