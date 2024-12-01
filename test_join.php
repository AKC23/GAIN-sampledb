<?php
include('db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add debugging query first
echo "<h3>Debug Information:</h3>";
$debug_query = "
SELECT 
    ft.FoodTypeID,
    ft.FoodTypeName,
    COUNT(*) as count
FROM FoodType ft
WHERE ft.FoodTypeName = 'Soya Bean'
GROUP BY ft.FoodTypeID, ft.FoodTypeName";

$debug_result = $conn->query($debug_query);
if ($debug_result) {
    echo "<p>FoodType entries for 'Soya Bean':</p>";
    while ($row = $debug_result->fetch_assoc()) {
        echo "FoodTypeID: {$row['FoodTypeID']}, Name: {$row['FoodTypeName']}, Count: {$row['count']}<br>";
    }
}

// Modified main query
$query = "
SELECT DISTINCT 
    tlcp.DataID,
    fv.VehicleName,
    ft.FoodTypeName,
    rc.RawCrops as RawCropsName,
    tlcp.SourceVolumeUnit,
    (tlcp.SourceVolume / 12) AS SourceVolume,
    (tlcp.ConvertedValue / 12) AS ConvertedValue,
    tlcp.ConvertedUnit,
    'Monthly' AS PeriodicalUnit,
    tlcp.CropToFoodConvertedValue,
    tlcp.StartYear,
    tlcp.EndYear,
    tlcp.AccessedDate,
    tlcp.Source,
    tlcp.Link,
    tlcp.Process,
    co.SourceVolume AS CrudeOilSourceVolume,
    co.ConvertedValue AS CrudeOilConvertedValue,
    co.VolumeUnit AS CrudeOilVolumeUnit,
    'Monthly' AS CrudeOilPeriodicalUnit,
    ((co.ConvertedValue * 0.15) + tlcp.CropToFoodConvertedValue) AS 'Total Oil Available For Consumption',
    co.StartYear AS CrudeOilStartYear,
    co.EndYear AS CrudeOilEndYear,
    co.AccessedDate AS CrudeOilAccessedDate,
    co.Source AS CrudeOilSource,
    co.Link AS CrudeOilLink,
    co.Process AS CrudeOilProcess
FROM 
    total_local_crop_production tlcp
INNER JOIN 
    FoodType ft ON tlcp.FoodTypeID = ft.FoodTypeID
INNER JOIN 
    FoodVehicle fv ON tlcp.VehicleID = fv.VehicleID AND fv.VehicleID = ft.VehicleID
LEFT JOIN 
    raw_crops rc ON tlcp.RawCropsID = rc.RawCropsID
LEFT JOIN 
    crude_oil co ON tlcp.VehicleID = co.VehicleID 
                AND tlcp.FoodTypeID = co.FoodTypeID 
                AND tlcp.RawCropsID = co.RawCropsID
WHERE 
    tlcp.StartYear IN ('July 2020', 'July 2021')
    AND ft.FoodTypeName = 'Soya Bean'
";

$result = $conn->query($query);

if ($result) {
    $numRows = $result->num_rows;
    echo "<p>Number of rows returned: $numRows</p>";

    if ($numRows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        
        // Headers
        $fields = $result->fetch_fields();
        echo "<tr style='background-color: #f2f2f2;'>";
        foreach ($fields as $field) {
            echo "<th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>" . htmlspecialchars($field->name) . "</th>";
        }
        echo "</tr>";
        
        // Data
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach($row as $key => $value) {
                echo "<td style='padding: 8px; border: 1px solid #ddd;'>";
                if (strpos($key, 'Link') !== false && !empty($value)) {
                    echo "<a href='" . htmlspecialchars($value) . "' target='_blank'>View</a>";
                } else {
                    echo htmlspecialchars($value ?? '');
                }
                echo "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data found.</p>";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
