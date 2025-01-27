<?php
// Include the database connection
include('db_connect.php');

// Ensure that $tableName is set and valid
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
    $tableName = $_POST['tableName'];
    $vehicleNames = $_POST['vehicleNames'] ?? [];
    $countryName = $_POST['countryName'] ?? '';

    if ($tableName == 'producer_processor') {
        // Fetch all records from producer_processor with joined names
        $sql = "
            SELECT pp.ProcessorID, e.ProducerProcessorName, e.CompanyGroup, fv.VehicleName, e.AdminLevel1, e.AdminLevel2, e.AdminLevel3, c.Country_Name, pp.TaskDoneByEntity, pp.Productioncapacityvolume, pp.PercentageOfCapacityUsed, pp.AnnualProductionSupplyVolume, pp.BSTIReferenceNo
            FROM producer_processor pp
            JOIN entities e ON pp.EntityID = e.EntityID
            JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID
            JOIN country c ON e.CountryID = c.Country_ID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "c.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY pp.ProcessorID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>ProcessorID</th><th>ProducerProcessorName</th><th>CompanyGroup</th><th>VehicleName</th><th>AdminLevel1</th><th>AdminLevel2</th><th>AdminLevel3</th><th>Country_Name</th><th>TaskDoneByEntity</th><th>Productioncapacityvolume</th><th>PercentageOfCapacityUsed</th><th>AnnualProductionSupplyVolume</th><th>BSTIReferenceNo</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['ProcessorID']}</td>";
                echo "<td>{$row['ProducerProcessorName']}</td>";
                echo "<td>{$row['CompanyGroup']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['AdminLevel1']}</td>";
                echo "<td>{$row['AdminLevel2']}</td>";
                echo "<td>{$row['AdminLevel3']}</td>";
                echo "<td>{$row['Country_Name']}</td>";
                echo "<td>{$row['TaskDoneByEntity']}</td>";
                echo "<td>{$row['Productioncapacityvolume']}</td>";
                echo "<td>{$row['PercentageOfCapacityUsed']}</td>";
                echo "<td>{$row['AnnualProductionSupplyVolume']}</td>";
                echo "<td>{$row['BSTIReferenceNo']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            // Show default data if no results found
            $sql = "
                SELECT pp.ProcessorID, e.ProducerProcessorName, e.CompanyGroup, fv.VehicleName, e.AdminLevel1, e.AdminLevel2, e.AdminLevel3, c.Country_Name, pp.TaskDoneByEntity, pp.Productioncapacityvolume, pp.PercentageOfCapacityUsed, pp.AnnualProductionSupplyVolume, pp.BSTIReferenceNo
                FROM producer_processor pp
                JOIN entities e ON pp.EntityID = e.EntityID
                JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID
                JOIN country c ON e.CountryID = c.Country_ID
                ORDER BY pp.ProcessorID
            ";
            $result = $conn->query($sql);
            if ($result) {
                echo "<div class='table-responsive'><table class='table table-bordered'>";
                echo "<thead><tr><th>ProcessorID</th><th>ProducerProcessorName</th><th>CompanyGroup</th><th>VehicleName</th><th>AdminLevel1</th><th>AdminLevel2</th><th>AdminLevel3</th><th>Country_Name</th><th>TaskDoneByEntity</th><th>Productioncapacityvolume</th><th>PercentageOfCapacityUsed</th><th>AnnualProductionSupplyVolume</th><th>BSTIReferenceNo</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['ProcessorID']}</td>";
                    echo "<td>{$row['ProducerProcessorName']}</td>";
                    echo "<td>{$row['CompanyGroup']}</td>";
                    echo "<td>{$row['VehicleName']}</td>";
                    echo "<td>{$row['AdminLevel1']}</td>";
                    echo "<td>{$row['AdminLevel2']}</td>";
                    echo "<td>{$row['AdminLevel3']}</td>";
                    echo "<td>{$row['Country_Name']}</td>";
                    echo "<td>{$row['TaskDoneByEntity']}</td>";
                    echo "<td>{$row['Productioncapacityvolume']}</td>";
                    echo "<td>{$row['PercentageOfCapacityUsed']}</td>";
                    echo "<td>{$row['AnnualProductionSupplyVolume']}</td>";
                    echo "<td>{$row['BSTIReferenceNo']}</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            } else {
                echo "Error fetching producer_processor data: " . $conn->error;
            }
        }
    } elseif ($tableName == 'foodtype') {
        // Fetch all records from FoodType with joined VehicleName
        $sql = "
            SELECT ft.FoodTypeID, ft.FoodTypeName, fv.VehicleName
            FROM FoodType ft
            JOIN FoodVehicle fv ON ft.VehicleID = fv.VehicleID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "c.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY ft.FoodTypeID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>FoodTypeID</th><th>FoodTypeName</th><th>VehicleName</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['FoodTypeID']}</td>";
                echo "<td>{$row['FoodTypeName']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            // Show default data if no results found
            $sql = "
                SELECT ft.FoodTypeID, ft.FoodTypeName, fv.VehicleName
                FROM FoodType ft
                JOIN FoodVehicle fv ON ft.VehicleID = fv.VehicleID
                ORDER BY ft.FoodTypeID
            ";
            $result = $conn->query($sql);
            if ($result) {
                echo "<div class='table-responsive'><table class='table table-bordered'>";
                echo "<thead><tr><th>FoodTypeID</th><th>FoodTypeName</th><th>VehicleName</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['FoodTypeID']}</td>";
                    echo "<td>{$row['FoodTypeName']}</td>";
                    echo "<td>{$row['VehicleName']}</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            } else {
                echo "Error fetching FoodType data: " . $conn->error;
            }
        }
    } elseif ($tableName == 'processing_stage') {
        // Fetch all records from processing_stage with joined VehicleName
        $sql = "
            SELECT ps.PSID, ps.Processing_Stage, fv.VehicleName
            FROM processing_stage ps
            JOIN FoodVehicle fv ON ps.VehicleID = fv.VehicleID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "c.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY ps.PSID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>PSID</th><th>Processing_Stage</th><th>VehicleName</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['PSID']}</td>";
                echo "<td>{$row['Processing_Stage']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            // Show default data if no results found
            $sql = "
                SELECT ps.PSID, ps.Processing_Stage, fv.VehicleName
                FROM processing_stage ps
                JOIN FoodVehicle fv ON ps.VehicleID = fv.VehicleID
                ORDER BY ps.PSID
            ";
            $result = $conn->query($sql);
            if ($result) {
                echo "<div class='table-responsive'><table class='table table-bordered'>";
                echo "<thead><tr><th>PSID</th><th>Processing_Stage</th><th>VehicleName</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['PSID']}</td>";
                    echo "<td>{$row['Processing_Stage']}</td>";
                    echo "<td>{$row['VehicleName']}</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            } else {
                echo "Error fetching processing_stage data: " . $conn->error;
            }
        }
    } elseif ($tableName == 'geography') {
        // Fetch all records from Geography with joined Country_Name
        $sql = "
            SELECT g.GeographyID, g.AdminLevel1, g.AdminLevel2, g.AdminLevel3, c.Country_Name
            FROM geography g
            JOIN country c ON g.CountryID = c.Country_ID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "g.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "c.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY g.GeographyID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>GeographyID</th><th>AdminLevel1</th><th>AdminLevel2</th><th>AdminLevel3</th><th>Country_Name</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['GeographyID']}</td>";
                echo "<td>{$row['AdminLevel1']}</td>";
                echo "<td>{$row['AdminLevel2']}</td>";
                echo "<td>{$row['AdminLevel3']}</td>";
                echo "<td>{$row['Country_Name']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            // Show default data if no results found
            $sql = "
                SELECT g.GeographyID, g.AdminLevel1, g.AdminLevel2, g.AdminLevel3, c.Country_Name
                FROM geography g
                JOIN country c ON g.CountryID = c.Country_ID
                ORDER BY g.GeographyID
            ";
            $result = $conn->query($sql);
            if ($result) {
                echo "<div class='table-responsive'><table class='table table-bordered'>";
                echo "<thead><tr><th>GeographyID</th><th>AdminLevel1</th><th>AdminLevel2</th><th>AdminLevel3</th><th>Country_Name</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['GeographyID']}</td>";
                    echo "<td>{$row['AdminLevel1']}</td>";
                    echo "<td>{$row['AdminLevel2']}</td>";
                    echo "<td>{$row['AdminLevel3']}</td>";
                    echo "<td>{$row['Country_Name']}</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            } else {
                echo "Error fetching Geography data: " . $conn->error;
            }
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
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "c.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY ec.ExtractionID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>ExtractionID</th><th>ExtractionRate</th><th>VehicleName</th><th>FoodTypeName</th><th>Reference No.</th><th>Source</th><th>Link</th><th>Process to Obtain Data</th><th>Access Date</th></tr></thead><tbody>";
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
            echo "</tbody></table></div>";
        } else {
            // Show default data if no results found
            $sql = "
                SELECT ec.ExtractionID, ec.ExtractionRate, fv.VehicleName, ft.FoodTypeName, r.`Reference No.`, r.Source, r.Link, r.`Process to Obtain Data`, r.`Access Date`
                FROM extraction_conversion ec
                JOIN FoodVehicle fv ON ec.VehicleID = fv.VehicleID
                JOIN FoodType ft ON ec.FoodTypeID = ft.FoodTypeID
                JOIN reference r ON ec.ReferenceID = r.ReferenceID
                ORDER BY ec.ExtractionID
            ";
            $result = $conn->query($sql);
            if ($result) {
                echo "<div class='table-responsive'><table class='table table-bordered'>";
                echo "<thead><tr><th>ExtractionID</th><th>ExtractionRate</th><th>VehicleName</th><th>FoodTypeName</th><th>Reference No.</th><th>Source</th><th>Link</th><th>Process to Obtain Data</th><th>Access Date</th></tr></thead><tbody>";
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
                echo "</tbody></table></div>";
            } else {
                echo "Error fetching extraction_conversion data: " . $conn->error;
            }
        }
    } elseif ($tableName == 'entities') {
        // Fetch all records from entities with joined VehicleName and Country_Name
        $sql = "
            SELECT e.EntityID, e.ProducerProcessorName, e.CompanyGroup, fv.VehicleName, e.AdminLevel1, e.AdminLevel2, e.AdminLevel3, c.Country_Name
            FROM entities e
            JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID
            JOIN country c ON e.CountryID = c.Country_ID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "c.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY e.EntityID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>EntityID</th><th>ProducerProcessorName</th><th>CompanyGroup</th><th>VehicleName</th><th>AdminLevel1</th><th>AdminLevel2</th><th>AdminLevel3</th><th>Country_Name</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['EntityID']}</td>";
                echo "<td>{$row['ProducerProcessorName']}</td>";
                echo "<td>{$row['CompanyGroup']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['AdminLevel1']}</td>";
                echo "<td>{$row['AdminLevel2']}</td>";
                echo "<td>{$row['AdminLevel3']}</td>";
                echo "<td>{$row['Country_Name']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            // Show default data if no results found
            $sql = "
                SELECT e.EntityID, e.ProducerProcessorName, e.CompanyGroup, fv.VehicleName, e.AdminLevel1, e.AdminLevel2, e.AdminLevel3, c.Country_Name
                FROM entities e
                JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID
                JOIN country c ON e.CountryID = c.Country_ID
                ORDER BY e.EntityID
            ";
            $result = $conn->query($sql);
            if ($result) {
                echo "<div class='table-responsive'><table class='table table-bordered'>";
                echo "<thead><tr><th>EntityID</th><th>ProducerProcessorName</th><th>CompanyGroup</th><th>VehicleName</th><th>AdminLevel1</th><th>AdminLevel2</th><th>AdminLevel3</th><th>Country_Name</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['EntityID']}</td>";
                    echo "<td>{$row['ProducerProcessorName']}</td>";
                    echo "<td>{$row['CompanyGroup']}</td>";
                    echo "<td>{$row['VehicleName']}</td>";
                    echo "<td>{$row['AdminLevel1']}</td>";
                    echo "<td>{$row['AdminLevel2']}</td>";
                    echo "<td>{$row['AdminLevel3']}</td>";
                    echo "<td>{$row['Country_Name']}</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            } else {
                echo "Error fetching entities data: " . $conn->error;
            }
        }
    } elseif ($tableName == 'distribution') {
        // Fetch all records from distribution with joined VehicleName
        $sql = "
            SELECT d.DistributionID, d.DistributionChannel, d.SubDistributionChannel, fv.VehicleName, d.PeriodicalUnit, d.SourceVolumeUnit, d.Volume, d.YearType, d.StartYear, d.StartMonth, d.EndYear, d.EndMonth, d.ReferenceNo
            FROM distribution d 
            LEFT JOIN foodvehicle fv ON d.VehicleID = fv.VehicleID
        ";
        $conditions = [];
        if (!empty($countryName)) {
            $conditions[] = "Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($vehicleNames)) {
            $vehicleConditions = array_map(function($vehicle) use ($conn) {
                return "fv.VehicleName = '" . $conn->real_escape_string($vehicle) . "'";
            }, $vehicleNames);
            $conditions[] = '(' . implode(' OR ', $vehicleConditions) . ')';
        }
        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo '<thead><tr>';
            $columns = array_keys($result->fetch_assoc());
            foreach ($columns as $column) {
                echo "<th>$column</th>";
            }
            echo '</tr></thead>';
            $result->data_seek(0);
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo "<td>$cell</td>";
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table></div>';
        } else {
            echo 'No data found';
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
            $sql = "SELECT * FROM `" . $conn->real_escape_string($tableName) . "`";
            $conditions = [];
            if (!empty($vehicleNames)) {
                $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
                $conditions[] = "VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
            }
            if (!empty($countryName)) {
                $conditions[] = "Country_Name = '" . $conn->real_escape_string($countryName) . "'";
            }
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
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
                echo "<div class='table-responsive'><table class='table table-bordered table-striped'>";
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
                echo "</tbody></table></div>";
            } else {
                // Show default data if no results found
                $sql = "SELECT * FROM `" . $conn->real_escape_string($tableName) . "` ORDER BY 1";
                $result = $conn->query($sql);
                if ($result) {
                    echo "<div class='table-responsive'><table class='table table-bordered table-striped'>";
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
                    echo "</tbody></table></div>";
                } else {
                    echo "<div class='alert alert-warning'>No records found in the selected table.</div>";
                }
            }
        }
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
