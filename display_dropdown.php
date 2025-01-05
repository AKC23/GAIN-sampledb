<?php
// Array mapping option values to display names
$tableNames = [
    "foodvehicle" => "Food Vehicle",
    "foodtype" => "Food Type",
    "country" => "Country",
    "measure_unit" => "Measure Unit",
    "measure_period" => "Measure Period",
    "measure_currency" => "Measure Currency",
    "geography" => "Geography",
    "raw_crops" => "Raw Crops",
    "producer_name" => "Producers Name",
    "producers_brand_name" => "Producer Brands",
    "producer_skus" => "Producer SKUs",
    "local_production_amount_oilseed" => "Local Production Amount (Oilseed)",
    "importer_name" => "Importer Name",
    "importers_brand_name" => "Importer Brands",
    "import_edible_oil" => "Import Edible Oil",
    "total_local_production_amount_edible_oil" => "Total Local Production Amount (Edible Oil)",
    "distribution_channels" => "Distribution Channels",
    "total_local_crop_production" => "Total Local Crop Production",
    "total_local_food_production" => "Total Local Food Production",
    "total_food_import" => "Total Food Import",
    "total_crop_import" => "Total Crop Import",
    "crude_oil" => "Crude Oil",
    "packaging_type" => "Packaging Type",
    "repacker_name" => "Repacker Name",
    "distributer_list" => "Distributer List",
    "distributer_brand" => "Distributer Brand",
    "distributer_name" => "Distributer Name"
];

// Determine if a table name has been selected
$selectedTable = isset($_POST['tableName']) ? $_POST['tableName'] : null;
$displayTableName = $selectedTable ? $tableNames[$selectedTable] : null;
?>

<form method="POST" action="index.php">
    <div class="form-group">
        <label for="tableSelect">Choose a table:</label>
        <select name="tableName" id="tableSelect" class="form-control" required>
            <option value="" selected disabled>Select a Table</option>
            <?php foreach ($tableNames as $value => $name): ?>
                <option value="<?= $value ?>" <?= $selectedTable === $value ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Show Table</button>
</form>


<!-- CSS for freezing the table header -->
<style>
    .table-responsive {
        max-height: 500px; /* Set max height as needed */
        overflow-y: auto;
    }

    .table thead th {
        position: sticky;
        top: 0;
        background-color: #f8f9fa;
        z-index: 1;
    }
</style>
