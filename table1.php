<?php
// Include the database connection
include('db_connect.php');

// Dummy data for Table 1
$data = [
    ['id' => 1, 'name' => 'Item 1', 'reference_no' => 101],
    ['id' => 2, 'name' => 'Item 2', 'reference_no' => 102],
    ['id' => 3, 'name' => 'Item 3', 'reference_no' => 103],
    ['id' => 4, 'name' => 'Item 4', 'reference_no' => 104],
    ['id' => 5, 'name' => 'Item 5', 'reference_no' => 105],
    ['id' => 6, 'name' => 'Item 6', 'reference_no' => 106],
    ['id' => 7, 'name' => 'Item 7', 'reference_no' => 107],
    ['id' => 8, 'name' => 'Item 8', 'reference_no' => 108],
    ['id' => 9, 'name' => 'Item 9', 'reference_no' => 109],
    ['id' => 10, 'name' => 'Item 10', 'reference_no' => 110],
];

echo "<div class='container mt-5'>";
echo "<h2>Table 1</h2>";
echo "<table class='table table-bordered table-striped'>";
echo "<thead class='thead-dark'><tr><th>ID</th><th>Name</th><th>Reference No</th></tr></thead>";
echo "<tbody>";
foreach ($data as $row) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td><a href='table2.php?reference_id=" . htmlspecialchars($row['reference_no']) . "'>" . htmlspecialchars($row['reference_no']) . "</a></td>";
    echo "</tr>";
}
echo "</tbody></table>";
echo "</div>";
?>
