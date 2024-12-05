<?php
// display_tables.php

// Include the database connection
include('db_connect.php');

// SQL query to join foodvehicle and foodtype tables
$sql = "SELECT fv.VehicleID, fv.VehicleName, ft.FoodTypeID, ft.FoodTypeName
        FROM FoodVehicle fv
        JOIN FoodType ft ON fv.VehicleID = ft.VehicleID
        ORDER BY fv.VehicleID, ft.FoodTypeID"; // You can adjust the ORDER BY clause as needed

// Execute the query
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Start the HTML table
    echo "<table border='1'>
            <tr>
                <th>VehicleID</th>
                <th>VehicleName</th>
                <th>FoodTypeID</th>
                <th>FoodTypeName</th>
            </tr>";

    // Ensure that imported data reflects correct foreign key relationships
    // No direct references found.
    // Ensure joined tables are updated if necessary.
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["VehicleID"] . "</td>
                <td>" . $row["VehicleName"] . "</td>
                <td>" . $row["FoodTypeID"] . "</td>
                <td>" . $row["FoodTypeName"] . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No results found.";
}

// Close the database connection
$conn->close();

?>
