
<?php
include('db_connect.php');

if (isset($_POST['view_table'])) {
    $table = $_POST['view_table'];
    
    if ($table === 'test_join') {
        header('Location: test_join.php');
        exit;
    }
    
    $query = "";
    switch($table) {
        // ...existing cases...
        
        case 'total_food_import':
            $query = "
                SELECT tfi.*, 
                       fv.VehicleName,
                       ft.FoodTypeName,
                       rc.RawCrops as RawCropsName,
                       c.CountryName
                FROM total_food_import tfi
                LEFT JOIN FoodVehicle fv ON tfi.VehicleID = fv.VehicleID
                LEFT JOIN FoodType ft ON tfi.FoodTypeID = ft.FoodTypeID
                LEFT JOIN raw_crops rc ON tfi.RawCropsID = rc.RawCropsID
                LEFT JOIN Country c ON tfi.Country_ID = c.Country_ID
                ORDER BY tfi.DataID";
            break;
            
        // ...existing code...
    }
    
    // ...existing code...
}
?>