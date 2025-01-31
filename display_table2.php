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
            SELECT d.DistributionID, dc.DistributionChannelName, sdc.SubDistributionChannelName, fv.VehicleName, d.PeriodicalUnit, d.SourceVolumeUnit, d.Volume, yt.YearType, d.StartYear, d.EndYear, r.ReferenceNumber, r.Source, r.Link, r.ProcessToObtainData, r.AccessDate
            FROM distribution d
            JOIN distribution_channel dc ON d.DistributionChannelID = dc.DistributionChannelID
            JOIN sub_distribution_channel sdc ON d.SubDistributionChannelID = sdc.SubDistributionChannelID
            JOIN FoodVehicle fv ON d.VehicleID = fv.VehicleID
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
            echo "<thead><tr><th>DistributionID</th><th>DistributionChannelName</th><th>SubDistributionChannelName</th><th>VehicleName</th><th>PeriodicalUnit</th><th>SourceVolumeUnit</th><th>Volume</th><th>YearType</th><th>StartYear</th><th>EndYear</th><th>ReferenceNumber</th><th>Source</th><th>Link</th><th>ProcessToObtainData</th><th>AccessDate</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['DistributionID']}</td>";
                echo "<td>{$row['DistributionChannelName']}</td>";
                echo "<td>{$row['SubDistributionChannelName']}</td>";
                echo "<td>{$row['VehicleName']}</td>";
                echo "<td>{$row['PeriodicalUnit']}</td>";
                echo "<td>{$row['SourceVolumeUnit']}</td>";
                echo "<td>{$row['Volume']}</td>";
                echo "<td>{$row['YearType']}</td>";
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
    } elseif ($tableName == 'population') {
        // Fetch all records from population with joined names
        $sql = "
            SELECT p.PopulationID, fv.VehicleName, p.AdminLevel1, p.AdminLevel3, p.PopulationGroup, p.AgeGroup, p.Value, p.AME, p.Year, r.ReferenceNumber, r.Source, r.Link, r.ProcessToObtainData, r.AccessDate
            FROM population p
            JOIN FoodVehicle fv ON p.VehicleID = fv.VehicleID
            JOIN reference r ON p.ReferenceNo = r.ReferenceID
        ";
        $conditions = [];
        if (!empty($vehicleNames)) {
            $vehicleNamesEscaped = array_map([$conn, 'real_escape_string'], $vehicleNames);
            $conditions[] = "fv.VehicleName IN ('" . implode("', '", $vehicleNamesEscaped) . "')";
        }
        if (!empty($yearType)) {
            $conditions[] = "p.Year = '" . $conn->real_escape_string($yearType) . "'";
        }
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY p.PopulationID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>PopulationID</th><th>VehicleName</th><th>AdminLevel1</th><th>AdminLevel3</th><th>PopulationGroup</th><th>AgeGroup</th><th>Value</th><th>AME</th><th>Year</th><th>ReferenceNumber</th><th>Source</th><th>Link</th><th>ProcessToObtainData</th><th>AccessDate</th></tr></thead><tbody>";
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
                echo "<td>{$row['Year']}</td>";
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
            SELECT ps.BrandID, ps.BrandName, c.CompanyGroup, ps.SKU, ps.Unit, pt.Packaging_Type, ps.Price, mc.CurrencySelection, r.ReferenceNumber, r.Source, r.Link, r.ProcessToObtainData, r.AccessDate
            FROM producer_sku ps
            JOIN company c ON ps.CompanyID = c.CompanyID
            JOIN packaging_type pt ON ps.Packaging_Type_ID = pt.Packaging_Type_ID
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
        $sql .= " ORDER BY ps.BrandID";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            echo "<div class='table-responsive'><table class='table table-bordered'>";
            echo "<thead><tr><th>BrandID</th><th>BrandName</th><th>CompanyGroup</th><th>SKU</th><th>Unit</th><th>Packaging_Type</th><th>Price</th><th>CurrencySelection</th><th>ReferenceNumber</th><th>Source</th><th>Link</th><th>ProcessToObtainData</th><th>AccessDate</th></tr></thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['BrandID']}</td>";
                echo "<td>{$row['BrandName']}</td>";
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
?>
