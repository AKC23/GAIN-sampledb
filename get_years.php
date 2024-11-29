<?php
include('db_connect.php');
header('Content-Type: application/json');

if(isset($_GET['table'])) {
    $table = $conn->real_escape_string($_GET['table']);
    
    // Check if table exists
    $table_exists = $conn->query("SHOW TABLES LIKE '$table'");
    if($table_exists->num_rows > 0) {
        // Get the year column name based on table
        $year_column = ($table === 'import_amount_oilseeds') ? 'Start_Year' : 'StartYear';
        
        // Check if year column exists
        $column_exists = $conn->query("SHOW COLUMNS FROM $table LIKE '$year_column'");
        if($column_exists->num_rows > 0) {
            $years_query = "SELECT DISTINCT $year_column FROM $table WHERE $year_column IS NOT NULL ORDER BY $year_column";
            $years_result = $conn->query($years_query);
            
            $years = array();
            if($years_result) {
                while($year = $years_result->fetch_array()) {
                    if($year[0]) {
                        $years[] = $year[0];
                    }
                }
                echo json_encode($years);
            } else {
                echo json_encode(['error' => 'Failed to fetch years: ' . $conn->error]);
            }
        } else {
            echo json_encode(['error' => 'Year column does not exist in table']);
        }
    } else {
        echo json_encode(['error' => 'Table does not exist']);
    }
} else {
    echo json_encode(['error' => 'No table specified']);
}
?>
