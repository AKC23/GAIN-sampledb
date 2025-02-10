<?php
// Include the database connection
include('db_connect.php');

// Ensure that $tableName is set and valid
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
    $tableName = $_POST['tableName'];
    $vehicleNames = $_POST['vehicleNames'] ?? [];
    $countryName = $_POST['countryName'] ?? '';
    $yearType = $_POST['yearType'] ?? '';

    if ($tableName == 'producer_processor') {
        // Fetch all records from producer_processor with joined names
        $sql = "
            SELECT pp.ProcessorID, e.ProducerProcessorName, c.CompanyGroup, fv.VehicleName, e.AdminLevel1, e.AdminLevel2, e.AdminLevel3, co.Country_Name, pp.TaskDoneByEntity, pp.Productioncapacityvolume, pp.PercentageOfCapacityUsed, pp.AnnualProductionSupplyVolume, pp.BSTIReferenceNo
            FROM producer_processor pp
            JOIN entities e ON pp.EntityID = e.EntityID
            JOIN company c ON e.CompanyID = c.CompanyID
            JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID
            JOIN country co ON e.CountryID = co.Country_ID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "co.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
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
            echo "No data found";
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
            SELECT ps.PSID, ps.Processing_Stage, ps.ExtractionRate, fv.VehicleName
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
            echo "<thead><tr><th>PSID</th><th>Processing_Stage</th><th>ExtractionRate</th><th>VehicleName</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['PSID']}</td>";
                echo "<td>{$row['Processing_Stage']}</td>";
                echo "<td>{$row['ExtractionRate']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            // Show default data if no results found
            $sql = "
                SELECT ps.PSID, ps.Processing_Stage, ps.ExtractionRate, fv.VehicleName
                FROM processing_stage ps
                JOIN FoodVehicle fv ON ps.VehicleID = fv.VehicleID
                ORDER BY ps.PSID
            ";
            $result = $conn->query($sql);
            if ($result) {
                echo "<div class='table-responsive'><table class='table table-bordered'>";
                echo "<thead><tr><th>PSID</th><th>Processing_Stage</th><th>ExtractionRate</th><th>VehicleName</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['PSID']}</td>";
                    echo "<td>{$row['Processing_Stage']}</td>";
                    echo "<td>{$row['ExtractionRate']}</td>";
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
            SELECT ec.ExtractionID, ec.ExtractionRate, fv.VehicleName, ft.FoodTypeName, r.ReferenceNumber, r.Source, r.Link, r.ProcessToObtainData, r.AccessDate
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
            echo "<thead><tr><th>ExtractionID</th><th>ExtractionRate</th><th>VehicleName</th><th>FoodTypeName</th><th>ReferenceNumber</th><th>Source</th><th>Link</th><th>ProcessToObtainData</th><th>AccessDate</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['ExtractionID']}</td>";
                echo "<td>{$row['ExtractionRate']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['FoodTypeName']}</td>";
                echo "<td>{$row['ReferenceNumber']}</td>";
                echo "<td>{$row['Source']}</td>";
                echo "<td>{$row['Link']}</td>";
                echo "<td>{$row['ProcessToObtainData']}</td>";
                echo "<td>{$row['AccessDate']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            // Show default data if no results found
            $sql = "
                SELECT ec.ExtractionID, ec.ExtractionRate, fv.VehicleName, ft.FoodTypeName, r.ReferenceNumber, r.Source, r.Link, r.ProcessToObtainData, r.AccessDate
                FROM extraction_conversion ec
                JOIN FoodVehicle fv ON ec.VehicleID = fv.VehicleID
                JOIN FoodType ft ON ec.FoodTypeID = ft.FoodTypeID
                JOIN reference r ON ec.ReferenceID = r.ReferenceID
                ORDER BY ec.ExtractionID
            ";
            $result = $conn->query($sql);
            if ($result) {
                echo "<div class='table-responsive'><table class='table table-bordered'>";
                echo "<thead><tr><th>ExtractionID</th><th>ExtractionRate</th><th>VehicleName</th><th>FoodTypeName</th><th>ReferenceNumber</th><th>Source</th><th>Link</th><th>ProcessToObtainData</th><th>AccessDate</th></tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['ExtractionID']}</td>";
                    echo "<td>{$row['ExtractionRate']}</td>";
                    echo "<td>{$row['VehicleName']}</td>";
                    echo "<td>{$row['FoodTypeName']}</td>";
                    echo "<td>{$row['ReferenceNumber']}</td>";
                    echo "<td>{$row['Source']}</td>";
                    echo "<td>{$row['Link']}</td>";
                    echo "<td>{$row['ProcessToObtainData']}</td>";
                    echo "<td>{$row['AccessDate']}</td>";
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
            SELECT e.EntityID, e.ProducerProcessorName, c.CompanyGroup, fv.VehicleName, e.AdminLevel1, e.AdminLevel2, e.AdminLevel3, co.Country_Name
            FROM entities e
            JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID
            JOIN company c ON e.CompanyID = c.CompanyID
            JOIN country co ON e.CountryID = co.Country_ID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "co.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
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
                SELECT e.EntityID, e.ProducerProcessorName, c.CompanyGroup, fv.VehicleName, e.AdminLevel1, e.AdminLevel2, e.AdminLevel3, co.Country_Name
                FROM entities e
                JOIN FoodVehicle fv ON e.VehicleID = fv.VehicleID
                JOIN company c ON e.CompanyID = c.CompanyID
                JOIN country co ON e.CountryID = co.Country_ID
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
        // Fetch all records from distribution with joined names
        $sql = "
            SELECT d.DistributionID, dc.DistributionChannelName, sdc.SubDistributionChannelName, fv.VehicleName, mu1.SupplyVolumeUnit, mu1.PeriodicalUnit, d.volumeMT, yt.YearType, d.StartYear, d.EndYear, r.Source, r.Link, r.ProcessToObtainData, r.AccessDate
            FROM distribution d
            JOIN distribution_channel dc ON d.DistributionChannelID = dc.DistributionChannelID
            JOIN sub_distribution_channel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
            JOIN FoodVehicle fv ON d.VehicleID = fv.VehicleID
            JOIN measure_unit1 mu1 ON d.UCID = mu1.UCID
            JOIN year_type yt ON d.YearTypeID = yt.YearTypeID
            JOIN reference r ON d.ReferenceID = r.ReferenceID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "c.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($yearType)) {
            $conditions[] = "yt.YearType = '" . $conn->real_escape_string($yearType) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY d.DistributionID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>DistributionID</th><th>DistributionChannelName</th><th>SubDistributionChannelName</th><th>VehicleName</th><th>SupplyVolumeUnit</th><th>PeriodicalUnit</th><th>Volume</th><th>YearType</th><th>StartYear</th><th>EndYear</th><th>Source</th><th>Link</th><th>ProcessToObtainData</th><th>AccessDate</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['DistributionID']}</td>";
                echo "<td>{$row['DistributionChannelName']}</td>";
                echo "<td>{$row['SubDistributionChannelName']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['SupplyVolumeUnit']}</td>";
                echo "<td>{$row['PeriodicalUnit']}</td>";
                echo "<td>{$row['volumeMT']}</td>";
                echo "<td>{$row['YearType']}</td>";
                echo "<td>{$row['StartYear']}</td>";
                echo "<td>{$row['EndYear']}</td>";
                echo "<td>{$row['Source']}</td>";
                echo "<td>{$row['Link']}</td>";
                echo "<td>{$row['ProcessToObtainData']}</td>";
                echo "<td>{$row['AccessDate']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'population') {
        // Fetch all records from population with joined names and year_type details
        $sql = "
            SELECT p.PopulationID, fv.VehicleName, p.AdminLevel1, p.AdminLevel3, 
                   p.PopulationGroup, p.AgeGroup, p.Value, p.AME, 
                   yt.YearType, yt.StartMonth, yt.EndMonth, p.StartYear, p.EndYear,
                   r.ReferenceNumber, r.Source, r.Link, r.ProcessToObtainData, r.AccessDate
            FROM population p
            JOIN FoodVehicle fv ON p.VehicleID = fv.VehicleID
            JOIN year_type yt ON p.YearTypeID = yt.YearTypeID
            JOIN reference r ON p.ReferenceNo = r.ReferenceID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($yearType)) {
            $conditions[] = "yt.YearType = '" . $conn->real_escape_string($yearType) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY p.PopulationID";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr>
                      <th>PopulationID</th>
                      <th>VehicleName</th>
                      <th>AdminLevel1</th>
                      <th>AdminLevel3</th>
                      <th>PopulationGroup</th>
                      <th>AgeGroup</th>
                      <th>Value</th>
                      <th>AME</th>
                      <th>YearType</th>
                      <th>StartMonth</th>
                      <th>EndMonth</th>
                      <th>StartYear</th>
                      <th>EndYear</th>
                      <th>ReferenceNumber</th>
                      <th>Source</th>
                      <th>Link</th>
                      <th>ProcessToObtainData</th>
                      <th>AccessDate</th>
                  </tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['PopulationID']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['AdminLevel1']}</td>";
                echo "<td>{$row['AdminLevel3']}</td>";
                echo "<td>{$row['PopulationGroup']}</td>";
                echo "<td>{$row['AgeGroup']}</td>";
                echo "<td>{$row['Value']}</td>";
                echo "<td>{$row['AME']}</td>";
                echo "<td>{$row['YearType']}</td>";
                echo "<td>{$row['StartMonth']}</td>";
                echo "<td>{$row['EndMonth']}</td>";
                echo "<td>{$row['StartYear']}</td>";
                echo "<td>{$row['EndYear']}</td>";
                echo "<td>{$row['ReferenceNumber']}</td>";
                echo "<td>{$row['Source']}</td>";
                echo "<td>{$row['Link']}</td>";
                echo "<td>{$row['ProcessToObtainData']}</td>";
                echo "<td>{$row['AccessDate']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'company') {
        // Fetch all records from company
        $sql = "SELECT * FROM company";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>CompanyID</th><th>CompanyGroup</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['CompanyID']}</td>";
                echo "<td>{$row['CompanyGroup']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'producer_sku') {
        // Fetch all records from producer_sku with joined names
        $sql = "
            SELECT ps.SKU_ID, b.Brand_Name, c.CompanyGroup, ps.SKU, ps.Unit, pt.Packaging_Type, ps.Price, mc.CurrencySelection, r.ReferenceNumber, r.Source, r.Link, r.ProcessToObtainData, r.AccessDate
            FROM producer_sku ps
            JOIN brand b ON ps.BrandID = b.BrandID
            JOIN company c ON ps.CompanyID = c.CompanyID
            JOIN packaging_type pt ON ps.PackagingTypeID = pt.PackagingTypeID
            JOIN measure_currency mc ON ps.CurrencyID = mc.CurrencyID
            JOIN reference r ON ps.ReferenceID = r.ReferenceID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "co.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY ps.SKU_ID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>SKU_ID</th><th>Brand_Name</th><th>CompanyGroup</th><th>SKU</th><th>Unit</th><th>Packaging_Type</th><th>Price</th><th>CurrencySelection</th><th>ReferenceNumber</th><th>Source</th><th>Link</th><th>ProcessToObtainData</th><th>AccessDate</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['SKU_ID']}</td>";
                echo "<td>{$row['Brand_Name']}</td>";
                echo "<td>{$row['CompanyGroup']}</td>";
                echo "<td>{$row['SKU']}</td>";
                echo "<td>{$row['Unit']}</td>";
                echo "<td>{$row['Packaging_Type']}</td>";
                echo "<td>{$row['Price']}</td>";
                echo "<td>{$row['CurrencySelection']}</td>";
                echo "<td>{$row['ReferenceNumber']}</td>";
                echo "<td>{$row['Source']}</td>";
                echo "<td>{$row['Link']}</td>";
                echo "<td>{$row['ProcessToObtainData']}</td>";
                echo "<td>{$row['AccessDate']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'distribution_channel') {
        // Fetch all records from distribution_channel
        $sql = "SELECT * FROM distribution_channel ORDER BY DistributionChannelID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>DistributionChannelID</th><th>DistributionChannelName</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['DistributionChannelID']}</td>";
                echo "<td>{$row['DistributionChannelName']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'sub_distribution_channel') {
        // Fetch all records from sub_distribution_channel
        $sql = "SELECT * FROM sub_distribution_channel ORDER BY SubDistributionChannelID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>SubDistributionChannelID</th><th>SubDistributionChannelName</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['SubDistributionChannelID']}</td>";
                echo "<td>{$row['SubDistributionChannelName']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'year_type') {
        // Fetch all records from year_type
        $sql = "SELECT * FROM year_type ORDER BY YearTypeID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>YearTypeID</th><th>YearType</th><th>StartMonth</th><th>EndMonth</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['YearTypeID']}</td>";
                echo "<td>{$row['YearType']}</td>";
                echo "<td>{$row['StartMonth']}</td>";
                echo "<td>{$row['EndMonth']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'measure_unit1') {
        // Fetch all records from measure_unit1
        $sql = "SELECT UCID, SupplyVolumeUnit, PeriodicalUnit, UnitValue FROM measure_unit1 ORDER BY UCID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>UCID</th><th>SupplyVolumeUnit</th><th>PeriodicalUnit</th><th>UnitValue</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['UCID']}</td>";
                echo "<td>{$row['SupplyVolumeUnit']}</td>";
                echo "<td>{$row['PeriodicalUnit']}</td>";
                echo "<td>{$row['UnitValue']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'measure_unit2') {
        // Fetch all records from measure_unit2
        $sql = "SELECT UnitID, UnitSelection, UnitValue FROM measure_unit2 ORDER BY UnitID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>UnitID</th><th>UnitSelection</th><th>UnitValue</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['UnitID']}</td>";
                echo "<td>{$row['UnitSelection']}</td>";
                echo "<td>{$row['UnitValue']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'brand') {
        $sql = "
            SELECT b.BrandID, b.Brand_Name, c.CompanyGroup, ft.FoodTypeName
            FROM brand b
            JOIN company c ON b.CompanyID = c.CompanyID
            JOIN FoodType ft ON b.FoodTypeID = ft.FoodTypeID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($countryName)) {
            $conditions[] = "co.Country_Name = '" . $conn->real_escape_string($countryName) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY b.BrandID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr>
                    <th>BrandID</th>
                    <th>Brand_Name</th>
                    <th>CompanyGroup</th>
                    <th>FoodTypeName</th>
                 </tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['BrandID']}</td>";
                echo "<td>{$row['Brand_Name']}</td>";
                echo "<td>{$row['CompanyGroup']}</td>";
                echo "<td>{$row['FoodTypeName']}</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found";
        }
    } elseif ($tableName == 'supply') {
        // Fetch all records from supply with joined FoodType, brand, and reference details
        $sql = "
            SELECT 
                s.SupplyID, 
                fv.VehicleName, 
                co.Country_Name, 
                ft.FoodTypeName, 
                ps.Processing_Stage, 
                s.Origin, 
                pp.Productioncapacityvolume, 
                pp.PercentageOfCapacityUsed, 
                b.Brand_Name, 
                s.ProductReferenceNo, 
                mu1.SupplyVolumeUnit, 
                mu1.PeriodicalUnit, 
                s.SourceVolume, 
                yt.YearType, 
                yt.StartMonth, 
                yt.EndMonth, 
                s.StartYear, 
                s.EndYear, 
                r.ReferenceNumber, 
                r.Source, 
                r.Link, 
                r.ProcessToObtainData, 
                r.AccessDate,
                s.SourceVolume * mu1.UnitValue AS Volume_MetricTon_Year
            FROM supply s
            JOIN FoodVehicle fv ON s.VehicleID = fv.VehicleID
            JOIN country co ON s.CountryID = co.Country_ID
            JOIN processing_stage ps ON s.PS_ID = ps.PSID
            JOIN producer_processor pp ON s.PSPRID = pp.ProcessorID
            JOIN FoodType ft ON s.FoodTypeID = ft.FoodTypeID
            JOIN brand b ON s.BrandID = b.BrandID
            JOIN measure_unit1 mu1 ON s.UC_ID = mu1.UCID
            JOIN year_type yt ON s.YearTypeID = yt.YearTypeID
            JOIN reference r ON s.ReferenceID = r.ReferenceID
            ORDER BY s.SupplyID
        ";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr>
                    <th>SupplyID</th>
                    <th>VehicleName</th>
                    <th>Country_Name</th>
                    <th>FoodTypeName</th>
                    <th>Processing_Stage</th>
                    <th>Origin</th>
                    <th>Productioncapacityvolume</th>
                    <th>PercentageOfCapacityUsed</th>
                    <th>Brand_Name</th>
                    <th>ProductReferenceNo</th>
                    <th>SupplyVolumeUnit</th>
                    <th>PeriodicalUnit</th>
                    <th>SourceVolume</th>
                    <th>Volume_MetricTon_Year</th>
                    <th>YearType</th>
                    <th>StartMonth</th>
                    <th>EndMonth</th>
                    <th>StartYear</th>
                    <th>EndYear</th>
                    <th>ReferenceNumber</th>
                    <th>Source</th>
                    <th>Link</th>
                    <th>ProcessToObtainData</th>
                    <th>AccessDate</th>
                  </tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['SupplyID']}</td>
                        <td>{$row['VehicleName']}</td>
                        <td>{$row['Country_Name']}</td>
                        <td>{$row['FoodTypeName']}</td>
                        <td>{$row['Processing_Stage']}</td>
                        <td>{$row['Origin']}</td>
                        <td>{$row['Productioncapacityvolume']}</td>
                        <td>{$row['PercentageOfCapacityUsed']}</td>
                        <td>{$row['Brand_Name']}</td>
                        <td>{$row['ProductReferenceNo']}</td>
                        <td>{$row['SupplyVolumeUnit']}</td>
                        <td>{$row['PeriodicalUnit']}</td>
                        <td>{$row['SourceVolume']}</td>
                        <td>{$row['Volume_MetricTon_Year']}</td>
                        <td>{$row['YearType']}</td>
                        <td>{$row['StartMonth']}</td>
                        <td>{$row['EndMonth']}</td>
                        <td>{$row['StartYear']}</td>
                        <td>{$row['EndYear']}</td>
                        <td>{$row['ReferenceNumber']}</td>
                        <td>{$row['Source']}</td>
                        <td>{$row['Link']}</td>
                        <td>{$row['ProcessToObtainData']}</td>
                        <td>{$row['AccessDate']}</td>
                      </tr>";
            }
            echo "</tbody></table></div>";
        } else {
            echo "No data found for supply table";
        }
    } elseif ($tableName == 'supply_in_chain_final') {
        // Apply filtering on SupplyYearType and DistributionYearType if a year type is selected
        $conditions = [];
        if (!empty($yearType)) {
            $conditions[] = "SupplyYearType = '" . $conn->real_escape_string($yearType) . "'";
            $conditions[] = "DistributionYearType = '" . $conn->real_escape_string($yearType) . "'";
        }
        $whereClause = !empty($conditions) ? " WHERE " . implode(" AND ", $conditions) : "";
        $sql = "SELECT * FROM supply_in_chain_final" . $whereClause . " ORDER BY SupplyID";
        $result = $conn->query($sql);
        // ...existing code to display table headers...
        echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>
                <th>SupplyID</th>
                <th>SupplyVehicle</th>
                <th>Country_Name</th>
                <th>FoodTypeName</th>
                <th>Processing_Stage</th>
                <th>Origin</th>
                <th>Productioncapacityvolume</th>
                <th>PercentageOfCapacityUsed</th>
                <th>Brand_Name</th>
                <th>ProductReferenceNo</th>
                <th>SupplyVolumeUnit</th>
                <th>PeriodicalUnit</th>
                <th>SourceVolume</th>
                <th>SupplyYearType</th>
                <th>SupplyStartYear</th>
                <th>SupplyEndYear</th>
                <th>ReferenceNumber</th>
                <th>Source</th>
                <th>Link</th>
                <th>ProcessToObtainData</th>
                <th>AccessDate</th>
                <th>DistributionID</th>
                <th>DistributionChannelName</th>
                <th>SubDistributionChannelName</th>
                <th>DistributionVehicle</th>
                <th>DistSupplyVolumeUnit</th>
                <th>DistPeriodicalUnit</th>
                <th>volumeMT</th>
                <th>DistributionYearType</th>
                <th>DistributionStartYear</th>
                <th>DistributionEndYear</th>
                <th>DistSource</th>
                <th>DistLink</th>
                <th>DistProcessToObtainData</th>
                <th>DistAccessDate</th>
              </tr></thead><tbody>";
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()){
                echo "<tr>
                        <td>{$row['SupplyID']}</td>
                        <td>{$row['SupplyVehicle']}</td>
                        <td>{$row['Country_Name']}</td>
                        <td>{$row['FoodTypeName']}</td>
                        <td>{$row['Processing_Stage']}</td>
                        <td>{$row['Origin']}</td>
                        <td>{$row['Productioncapacityvolume']}</td>
                        <td>{$row['PercentageOfCapacityUsed']}</td>
                        <td>{$row['Brand_Name']}</td>
                        <td>{$row['ProductReferenceNo']}</td>
                        <td>{$row['SupplyVolumeUnit']}</td>
                        <td>{$row['PeriodicalUnit']}</td>
                        <td>{$row['SourceVolume']}</td>
                        <td>{$row['SupplyYearType']}</td>
                        <td>{$row['SupplyStartYear']}</td>
                        <td>{$row['SupplyEndYear']}</td>
                        <td>{$row['ReferenceNumber']}</td>
                        <td>{$row['Source']}</td>
                        <td>{$row['Link']}</td>
                        <td>{$row['ProcessToObtainData']}</td>
                        <td>{$row['AccessDate']}</td>
                        <td>{$row['DistributionID']}</td>
                        <td>{$row['DistributionChannelName']}</td>
                        <td>{$row['SubDistributionChannelName']}</td>
                        <td>{$row['DistributionVehicle']}</td>
                        <td>{$row['DistSupplyVolumeUnit']}</td>
                        <td>{$row['DistPeriodicalUnit']}</td>
                        <td>{$row['volumeMT']}</td>
                        <td>{$row['DistributionYearType']}</td>
                        <td>{$row['DistributionStartYear']}</td>
                        <td>{$row['DistributionEndYear']}</td>
                        <td>{$row['DistSource']}</td>
                        <td>{$row['DistLink']}</td>
                        <td>{$row['DistProcessToObtainData']}</td>
                        <td>{$row['DistAccessDate']}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='33'>No data found</td></tr>";
        }
        echo "</tbody></table></div>";
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
            if (!empty($yearType)) {
                $conditions[] = "YearType = '" . $conn->real_escape_string($yearType) . "'";
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
                echo "<div class='alert alert-warning'>No records found in the selected table.</div>";
            }
        }
    }
}

// Note: Do not close the database connection here
// The connection will be closed by index.php

