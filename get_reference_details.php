<?php
// get_reference_details.php
// Include the database connection
include('db_connect.php');

$referenceID = $_GET['referenceID'] ?? '';
if (!empty($referenceID)) {
    $result = $conn->query("SELECT ReferenceNumber, Source, Link, ProcessToObtainData, AccessDate FROM reference WHERE ReferenceID = $referenceID");
    if ($result && $row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
