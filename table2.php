<?php
// Include the database connection
include('db_connect.php');

// Dummy data for Table 2
$data = [
    101 => ['reference_id' => 101, 'website_name' => 'Example Site 1', 'date' => '2023-01-01'],
    102 => ['reference_id' => 102, 'website_name' => 'Example Site 2', 'date' => '2023-02-01'],
    103 => ['reference_id' => 103, 'website_name' => 'Example Site 3', 'date' => '2023-03-01'],
    104 => ['reference_id' => 104, 'website_name' => 'Example Site 4', 'date' => '2023-04-01'],
    105 => ['reference_id' => 105, 'website_name' => 'Example Site 5', 'date' => '2023-05-01'],
    106 => ['reference_id' => 106, 'website_name' => 'Example Site 6', 'date' => '2023-06-01'],
    107 => ['reference_id' => 107, 'website_name' => 'Example Site 7', 'date' => '2023-07-01'],
    108 => ['reference_id' => 108, 'website_name' => 'Example Site 8', 'date' => '2023-08-01'],
    109 => ['reference_id' => 109, 'website_name' => 'Example Site 9', 'date' => '2023-09-01'],
    110 => ['reference_id' => 110, 'website_name' => 'Example Site 10', 'date' => '2023-10-01'],
];

$reference_id = isset($_GET['reference_id']) ? intval($_GET['reference_id']) : 0;

echo "<div class='container mt-5'>";
if (isset($data[$reference_id])) {
    $row = $data[$reference_id];
    echo "<h2>Reference Table</h2>";
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='thead-dark'><tr><th>Reference ID</th><th>Website Name</th><th>Date</th></tr></thead>";
    echo "<tbody>";
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['reference_id']) . "</td>";
    echo "<td>" . htmlspecialchars($row['website_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
    echo "</tr>";
    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-danger'>Invalid Reference ID.</div>";
}
echo "</div>";
?>
