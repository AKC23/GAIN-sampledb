<?php
// Include the database connection
include('db_connect.php');

// Fetch all records from table1
$result = $conn->query("SELECT * FROM table1 ORDER BY ItemID");

if ($result) {
    echo "<h1>Table 1 Contents</h1>";
    echo "<table border='1'>";
    echo "<tr><th>ItemID</th><th>ItemName</th><th>ReferenceID</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['ItemID']}</td>";
        echo "<td>{$row['ItemName']}</td>";
        echo "<td><a href='view_reference.php?reference_id=" . htmlspecialchars($row['ReferenceID']) . "' target='_blank'>" . htmlspecialchars($row['ReferenceID']) . "</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error fetching table1 data: " . $conn->error;
}

?>
