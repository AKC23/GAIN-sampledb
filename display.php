<?php
// display_table.php

// Include the database connection
include('db_connect.php');

// Check if the table name is set
if (!isset($tableName)) {
    echo "Table name is not set.";
    exit;
}

// Prepare the SQL query to select all data from the specified table
$sql = "SELECT * FROM $tableName";

// Execute the query
$result = $conn->query($sql);

// Check if there are results and display the table
if ($result && $result->num_rows > 0) {
    // Start the HTML table
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>";

    // Fetch the field names dynamically
    while ($field = $result->fetch_field()) {
        echo "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    echo "</tr>";

    // Fetch and display each row of data
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }

    // Close the table
    echo "</table>";
} else {
    echo "No records found in the table '$tableName'.";
}

// Close the database connection
$conn->close();
?>
