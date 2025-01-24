<?php
// Include the database connection
include('db_connect.php');

// Ensure that $tableName is set and valid
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
    $tableName = $_POST['tableName'];
    $vehicleName = $_POST['vehicleName'] ?? '';

    if ($tableName == 'producer_processor') {
        // Fetch all records from producer_processor with joined names
        $sql = "
            SELECT p.ProcessorID, v.VehicleName, p.CompanyGroup, p.ProducerProcessorName, p.ProducerProcessorAddress, c.Country_Name 
            FROM producer_processor p
            JOIN FoodVehicle v ON p.VehicleID = v.VehicleID
            JOIN country c ON p.Country_ID = c.Country_ID
        ";
        if (!empty($vehicleName)) {
            $sql .= " WHERE v.VehicleName = '" . $conn->real_escape_string($vehicleName) . "'";
        }
        $sql .= " ORDER BY p.ProcessorID";
        $result = $conn->query($sql);

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
    } elseif ($tableName == 'foodtype') {
        // Fetch all records from FoodType with joined VehicleName
        $sql = "
            SELECT ft.FoodTypeID, ft.FoodTypeName, fv.VehicleName
            FROM FoodType ft
            JOIN FoodVehicle fv ON ft.VehicleID = fv.VehicleID
        ";
        if (!empty($vehicleName)) {
            $sql .= " WHERE fv.VehicleName = '" . $conn->real_escape_string($vehicleName) . "'";
        }
        $sql .= " ORDER BY ft.FoodTypeID";
        $result = $conn->query($sql);

        if ($result) {
            echo "<h1>FoodType Table Contents</h1>";
            echo "<table class='table table-bordered'>";
            echo "<tr><th>FoodTypeID</th><th>FoodTypeName</th><th>VehicleName</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['FoodTypeID']}</td>";
                echo "<td>{$row['FoodTypeName']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Error fetching FoodType data: " . $conn->error;
        }
    } elseif ($tableName == 'processing_stage') {
        // Fetch all records from processing_stage with joined VehicleName
        $sql = "
            SELECT ps.PSID, ps.Processing_Stage, fv.VehicleName
            FROM processing_stage ps
            JOIN FoodVehicle fv ON ps.VehicleID = fv.VehicleID
        ";
        if (!empty($vehicleName)) {
            $sql .= " WHERE fv.VehicleName = '" . $conn->real_escape_string($vehicleName) . "'";
        }
        $sql .= " ORDER BY ps.PSID";
        $result = $conn->query($sql);

        if ($result) {
            echo "<h1>Processing Stage Table Contents</h1>";
            echo "<table class='table table-bordered'>";
            echo "<tr><th>PSID</th><th>Processing_Stage</th><th>VehicleName</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['PSID']}</td>";
                echo "<td>{$row['Processing_Stage']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Error fetching processing_stage data: " . $conn->error;
        }
    } elseif ($tableName == 'geography') {
        // Fetch all records from Geography with joined Country_Name
        $sql = "
            SELECT g.GeographyID, g.`Admin Level 1 (City Corporation)`, g.`Admin Level 2 (District)`, g.`Admin Level 3 (Division)`, c.Country_Name
            FROM geography g
            JOIN country c ON g.CountryID = c.Country_ID
        ";
        if (!empty($vehicleName)) {
            $sql .= " WHERE g.VehicleName = '" . $conn->real_escape_string($vehicleName) . "'";
        }
        $sql .= " ORDER BY g.GeographyID";
        $result = $conn->query($sql);

        if ($result) {
            echo "<h1>Geography Table Contents</h1>";
            echo "<table class='table table-bordered'>";
            echo "<tr><th>GeographyID</th><th>Admin Level 1 (City Corporation)</th><th>Admin Level 2 (District)</th><th>Admin Level 3 (Division)</th><th>Country_Name</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['GeographyID']}</td>";
                echo "<td>{$row['Admin Level 1 (City Corporation)']}</td>";
                echo "<td>{$row['Admin Level 2 (District)']}</td>";
                echo "<td>{$row['Admin Level 3 (Division)']}</td>";
                echo "<td>{$row['Country_Name']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Error fetching Geography data: " . $conn->error;
        }
    } elseif ($tableName == 'extraction_conversion') {
        // Fetch all records from extraction_conversion with joined VehicleName, FoodTypeName, and reference details
        $sql = "
            SELECT ec.ExtractionID, ec.ExtractionRate, fv.VehicleName, ft.FoodTypeName, r.`Reference No.`, r.Source, r.Link, r.`Process to Obtain Data`, r.`Access Date`
            FROM extraction_conversion ec
            JOIN FoodVehicle fv ON ec.VehicleID = fv.VehicleID
            JOIN FoodType ft ON ec.FoodTypeID = ft.FoodTypeID
            JOIN reference r ON ec.ReferenceID = r.ReferenceID
        ";
        if (!empty($vehicleName)) {
            $sql .= " WHERE fv.VehicleName = '" . $conn->real_escape_string($vehicleName) . "'";
        }
        $sql .= " ORDER BY ec.ExtractionID";
        $result = $conn->query($sql);

        if ($result) {
            echo "<h1>Extraction Conversion Table Contents</h1>";
            echo "<table class='table table-bordered'>";
            echo "<tr><th>ExtractionID</th><th>ExtractionRate</th><th>VehicleName</th><th>FoodTypeName</th><th>Reference No.</th><th>Source</th><th>Link</th><th>Process to Obtain Data</th><th>Access Date</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['ExtractionID']}</td>";
                echo "<td>{$row['ExtractionRate']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['FoodTypeName']}</td>";
                echo "<td>{$row['Reference No.']}</td>";
                echo "<td>{$row['Source']}</td>";
                echo "<td>{$row['Link']}</td>";
                echo "<td>{$row['Process to Obtain Data']}</td>";
                echo "<td>{$row['Access Date']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Error fetching extraction_conversion data: " . $conn->error;
        }
    } elseif ($tableName == 'entities') {
        // Fetch all records from entities with joined VehicleName and Country_Name
        $sql = "
            SELECT e.EntityID, e.`Producer / Processor name`, e.`Company group`, fv.VehicleName, e.`Admin 1`, e.`Admin 2`, e.`Admin 3`, e.UDC, e.Thana, e.Upazila, c.Country_Name
            FROM entities e
            JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID
            JOIN country c ON e.CountryID = c.Country_ID
        ";
        if (!empty($vehicleName)) {
            $sql .= " WHERE fv.VehicleName = '" . $conn->real_escape_string($vehicleName) . "'";
        }
        $sql .= " ORDER BY e.EntityID";
        $result = $conn->query($sql);

        if ($result) {
            echo "<h1>Entities Table Contents</h1>";
            echo "<table class='table table-bordered'>";
            echo "<tr><th>EntityID</th><th>Producer / Processor name</th><th>Company group</th><th>VehicleName</th><th>Admin 1</th><th>Admin 2</th><th>Admin 3</th><th>UDC</th><th>Thana</th><th>Upazila</th><th>Country_Name</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['EntityID']}</td>";
                echo "<td>{$row['Producer / Processor name']}</td>";
                echo "<td>{$row['Company group']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['Admin 1']}</td>";
                echo "<td>{$row['Admin 2']}</td>";
                echo "<td>{$row['Admin 3']}</td>";
                echo "<td>{$row['UDC']}</td>";
                echo "<td>{$row['Thana']}</td>";
                echo "<td>{$row['Upazila']}</td>";
                echo "<td>{$row['Country_Name']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "Error fetching entities data: " . $conn->error;
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
            if (!empty($vehicleName)) {
                $sql .= " WHERE VehicleName = '" . $conn->real_escape_string($vehicleName) . "'";
            }

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

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
