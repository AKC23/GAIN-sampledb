<?php
header('Content-Type: application/json');
include('db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(isset($_GET['table'])) {
    $table = $_GET['table'];
    $columns = array();
    
    // Check if table exists
    $table_exists = $conn->query("SHOW TABLES LIKE '$table'");
    if($table_exists->num_rows > 0) {
        // Get the year column name based on table
        $year_column = ($table === 'import_amount_oilseeds') ? 'Start_Year' : 'StartYear';
        
        // Build the query based on table
        switch($table) {
            case 'total_local_production_amount_edible_oil':
            case 'import_edible_oil':
                $query = "SELECT 
                    t.DataID,
                    ft.FoodTypeName as 'Food Type',
                    fv.VehicleName as 'Vehicle',
                    t.ConvertedUnit as 'Unit',
                    t.CValue as 'Value',
                    t.$year_column as 'Start Year',
                    t.EndYear as 'End Year',
                    DATE_FORMAT(t.AccessedDate, '%d/%m/%Y') as 'Accessed Date',
                    t.Source,
                    t.Link,
                    t.DataType as 'Data Type',
                    t.ProcessToObtainData as 'Process'
                FROM $table t
                LEFT JOIN FoodType ft ON t.FoodTypeID = ft.FoodTypeID
                LEFT JOIN FoodVehicle fv ON ft.VehicleID = fv.VehicleID
                WHERE 1=1";
                break;
                
            case 'total_local_crop_production':
            case 'import_amount_oilseeds':
                $query = "SELECT 
                    t.DataID,
                    rc.RawCrops as 'Raw Crop',
                    fv.VehicleName as 'Vehicle',
                    t.ConvertedUnit as 'Unit',
                    t.Value as 'Value',
                    t.$year_column as 'Start Year',
                    t.EndYear as 'End Year',
                    DATE_FORMAT(t.AccessedDate, '%d/%m/%Y') as 'Accessed Date',
                    t.Source,
                    t.Link,
                    t.DataType as 'Data Type',
                    t.ProcessToObtainData as 'Process'
                FROM $table t
                LEFT JOIN raw_crops rc ON t.RawCropsID = rc.RawCropsID
                LEFT JOIN FoodVehicle fv ON t.VehicleID = fv.VehicleID
                WHERE 1=1";
                break;
                
            default:
                $query = "SELECT * FROM $table WHERE 1=1";
        }
        
        $result = $conn->query($query);
        
        if ($result) {
            // Get column names
            $fields = $result->fetch_fields();
            foreach ($fields as $field) {
                // Clean up the display names
                $displayName = str_replace(['RawCropName', 'VehicleName', 'FoodTypeName'], 
                                        ['Raw Crop', 'Vehicle', 'Food Type'], 
                                        $field->name);
                $columns[] = $displayName;
            }
        }
    }
    
    if ($table === 'import_amount_oilseeds') {
        $columns = array_map(function($column) {
            return ($column === 'Start_Year') ? 'Year' : $column;
        }, $columns);
    }
    
    echo json_encode($columns);
} else {
    echo json_encode([]);
}
?>