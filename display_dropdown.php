<?php
// Array mapping option values to display names
$tableNames = [
    "foodvehicle" => "Food Vehicle",
    "foodtype" => "Food Type",
    "raw_crops" => "Raw Crops",
    "producers_supply" => "Producers Supply Amount",
    "producers_brand_name" => "Producer Brands",
    "producer_skus" => "Producer SKUs",
    "local_production_amount_oilseed" => "Local Production Amount (Oilseed)",
    "importers_supply" => "Importers Supply Amount",
    "importers_brand_name" => "Importer Brands",
    "importer_skus" => "Importer SKUs",
    "import_amount_oilseeds" => "Import Amount Oilseeds",
    "import_edible_oil" => "Import Edible Oil",
    "total_local_production_amount_edible_oil" => "Total Local Production Amount (Edible Oil)",
    "distribution_channels" => "Distribution Channels",
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
