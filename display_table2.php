<?php
// Include the database connection
include('db_connect.php');

// Ensure that $tableName is set and valid
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
    $tableName = $_POST['tableName'];

    if ($tableName == 'producer_processor') {
        // Fetch all records from producer_processor with joined names
        $result = $conn->query("
            SELECT p.ProcessorID, v.VehicleName, p.CompanyGroup, p.ProducerProcessorName, p.ProducerProcessorAddress, c.Country_Name 
            FROM producer_processor p
            JOIN FoodVehicle v ON p.VehicleID = v.VehicleID
            JOIN country c ON p.Country_ID = c.Country_ID
            ORDER BY p.ProcessorID
        ");

        if ($result) {
            echo "<h1>Producer Processor Table Contents</h1>";
            echo "<table class='table table-bordered'>";
            echo "<tr><th>ProcessorID</th><th>VehicleName</th><th>CompanyGroup</th><th>ProducerProcessorName</th><th>ProducerProcessorAddress</th><th>Country_Name</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['ProcessorID']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['CompanyGroup']}</td>";
                echo "<td>{$row['ProducerProcessorName']}</td>";
                echo "<td>{$row['ProducerProcessorAddress']}</td>";
                echo "<td>{$row['Country_Name']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Error fetching producer_processor data: " . $conn->error;
        }
    } else {
        // Handle other tables
        if (!empty($tableName)) {
            // Check if the table exists before querying
            $table_check = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($tableName) . "'");
            if ($table_check->num_rows == 0) {
                echo "<div class='alert alert-danger'>Error: Table '" . htmlspecialchars($tableName) . "' does not exist.</div>";
                exit;
            }

            // Base SQL query to retrieve the entire table securely
            $sql = "SELECT * FROM " . $conn->real_escape_string($tableName);

            // Execute the query
            try {
                $result = $conn->query($sql);
                if (!$result) {
                    throw new Exception("Table '$tableName' does not exist.");
                }
            } catch (Exception $e) {
                echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
                exit;
            }

            // Check if there are results and display the table
            if ($result && $result->num_rows > 0) {
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead class='thead-dark'><tr>";
                
                // Fetch field names dynamically
                $fieldTypes = [];
                while ($fieldinfo = $result->fetch_field()) {
                    $fieldTypes[$fieldinfo->name] = $fieldinfo->type;
                    echo "<th>" . htmlspecialchars($fieldinfo->name) . "</th>";
                }
                
                echo "</tr></thead><tbody>";

                // Display data rows
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $field => $data) {
                        // Apply alignment based on column type
                        $alignStyle = '';
                        if ($fieldTypes[$field] == MYSQLI_TYPE_LONG) { // Integer values (assuming ID is integer)
                            $alignStyle = "text-align: center;";
                        } elseif (is_numeric($data)) { // Any numeric values (align right)
                            $alignStyle = "text-align: center;";
                        } else { // Default alignment for text (align left)
                            $alignStyle = "text-align: center;";
                        }
                        echo "<td style='background-color: #f8f9fa; $alignStyle'>" . htmlspecialchars($data) . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-warning'>No records found in the selected table.</div>";
            }
        }
    }
}

// Close the database connection
$conn->close();
?>
