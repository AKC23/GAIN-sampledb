<?php
// get_unit_value.php
// Include the database connection
include('db_connect.php');

$ucid = $_GET['ucid'] ?? '';
if (!empty($ucid)) {
    $result = $conn->query("SELECT UnitValue FROM measureunit1 WHERE UCID = $ucid");
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode(['UnitValue' => $row['UnitValue']]);
    } else {
        echo json_encode(['UnitValue' => 0]);
    }
} else {
    echo json_encode(['UnitValue' => 0]);
}
?>
