<?php
// Include the database connection
include('db_connect.php');

// Get the ReferenceID from the GET request
$referenceID = isset($_GET['reference_id']) ? intval($_GET['reference_id']) : 0;

if ($referenceID > 0) {
    // Query to get the data from table2 based on the ReferenceID
    $sql = "SELECT * FROM table2 WHERE ReferenceID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $referenceID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h1>Details for ReferenceID $referenceID</h1>";
        echo "<table class='table table-bordered'>";
        echo "<tr><th>ReferenceID</th><th>WebsiteName</th><th>Date</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['ReferenceID']}</td>";
            echo "<td>{$row['WebsiteName']}</td>";
            echo "<td>{$row['Date']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data found for ReferenceID $referenceID.<br>";
    }

    $stmt->close();
} else {
    echo "Invalid ReferenceID.<br>";
}

?>
