<?php
include('db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$query = "
SELECT 
    -- Reference Table Names
    fv.VehicleName as 'Food Vehicle',
    tlcp.VehicleID,
    ft.FoodTypeName as 'Food Type',
    tlcp.FoodTypeID,
    tlcp.Process as 'Processing Stage',
    rc.RawCrops as 'Type of Crop',
    tlcp.RawCropsID,
    tlcp.CountryID as 'Country ID',
    
    -- Local Production Values
    tlcp.SourceVolumeUnit as 'Source Volume Unit',
    FORMAT(tlcp.SourceVolume, 2) as 'Source Volume',
    FORMAT(tlcp.ConvertedValue, 2) as 'Converted Value (in Metric Tons/year)',
    tlcp.ConvertedUnit as 'Volume Unit',
    tlcp.PeriodicalUnit as 'Periodical Unit',
    FORMAT(tlcp.CropToFoodConvertedValue, 2) as 'Crop to Food Converted Value',
    
    -- Crude Oil Values
    FORMAT(co.SourceVolume, 2) as 'Crude Oil Source Volume',
    FORMAT(co.ConvertedValue, 2) as 'Crude Oil Converted Value',
    co.VolumeUnit as 'Crude Oil Volume Unit',
    co.PeriodicalUnit as 'Crude Oil Periodical Unit',
    
    -- Time Details
    tlcp.StartYear as 'Start Year',
    tlcp.EndYear as 'End Year',
    tlcp.AccessedDate as 'Accessed Date',
    
    -- Source Information
    tlcp.Source as 'Source',
    tlcp.Link as 'Link',
    tlcp.Process as 'Process to Obtain Data',
    
    -- Crude Oil Source
    co.Source as 'Crude Oil Source',
    co.Link as 'Crude Oil Link',
    co.Process as 'Crude Oil Process'

FROM total_local_crop_production tlcp
LEFT JOIN crude_oil co ON 
    tlcp.VehicleID = co.VehicleID AND
    tlcp.FoodTypeID = co.FoodTypeID AND
    tlcp.RawCropsID = co.RawCropsID AND
    tlcp.CountryID = co.Country_ID AND
    tlcp.StartYear = co.StartYear AND
    tlcp.EndYear = co.EndYear
LEFT JOIN FoodType ft ON tlcp.FoodTypeID = ft.FoodTypeID
LEFT JOIN FoodVehicle fv ON tlcp.VehicleID = fv.VehicleID
LEFT JOIN raw_crops rc ON tlcp.RawCropsID = rc.RawCropsID
WHERE 1=1
ORDER BY 
    tlcp.StartYear DESC,
    ft.FoodTypeName,
    tlcp.Process
";

$result = $conn->query($query);

if ($result) {
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
    
    // Show the SQL query for debugging
    echo "<pre style='margin-top: 20px; background-color: #f8f9fa; padding: 15px;'>";
    echo htmlspecialchars($query);
    echo "</pre>";
} else {
    echo "Error: " . $conn->error;
}
?>
