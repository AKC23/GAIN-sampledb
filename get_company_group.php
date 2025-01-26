<?php
// Include the database connection
include('db_connect.php');

$entityID = $_GET['entityID'] ?? '';

if (!empty($entityID)) {
    $entityID = $conn->real_escape_string($entityID);
    $result = $conn->query("SELECT CompanyGroup FROM entities WHERE EntityID = '$entityID'");
    if ($result) {
        $row = $result->fetch_assoc();
        echo $row['CompanyGroup'];
    } else {
        echo "Error fetching company group: " . $conn->error;
    }
} else {
    echo "Invalid Entity ID";
}
?>
