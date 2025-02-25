<?php

// display_table.php
// This script displays the supply_in_chain_final table

// Include the database connection
include('db_connect.php');

// Ensure that $tableName is set and valid
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
    $tableName = $_POST['tableName'];

    // Handle the supply_in_chain_final table
    if ($tableName == 'supply_in_chain_final') {
        include('display_tables/display_supply_in_chain_final.php');
    } else {
        echo "Table Name: $tableName<br>No records found.";
    }
}
// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
