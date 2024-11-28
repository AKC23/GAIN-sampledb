<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oil & Wheat Database - BD</title>
    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .center-title {
            text-align: center;
            color: #000;
            margin-top: 20px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .current-time {
            text-align: right;
            font-size: 0.9em;
            color: #6c757d;
            margin-top: 1px;
        }

        .card {
            margin-top: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #c8e5bf;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-selection {
            margin-bottom: 20px;
        }

        .card-title {
            color: #17a2b8;
        }

        /* Table styling for borders */
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #d3a79e;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="center-title">Database on Edible Oil and Wheat Flour Supply for Human Consumption in Bangladesh</h1>

        <div class="card">
            <!-- Dropdown or other content on the left side -->
            <div>
                <?php include('display_dropdown.php'); ?>
            </div>

            <!-- Last Updated Date and Time on the right side of the card -->
            <div class="current-time">
                <?php
                echo "Last Updated: October 25, 2024, 3:30 pm";
                ?>
            </div>
        </div>

        <?php
        
        
        include('insert_total_local_crop_production.php');
        
        
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['tableName'])) {
            // Get the user-friendly display name
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
            $tableName = $_POST['tableName'];
            $displayTableName = $tableNames[$tableName] ?? $tableName;

            
            // Display the friendly table name
            echo "<h2 class='text-center card-title'>Data Table: " . htmlspecialchars($displayTableName) . "</h2>";

            // Include display_table.php to show the data
            echo '<div class="table-responsive">';
            include('display_table.php');
            echo '</div>';
        } else {
            echo "<p class='text-center text-muted'>Select a table to display data.</p>";
        }
        ?>
    </div>

    <!-- Bootstrap and jQuery scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>




<!-- 


// include('insert_local_production_amount_oilseed.php');
// include('insert_import_edible_oil.php');

// include('insert_import_amount_oilseeds.php');
// include('insert_import_edible_oil.php');
//include('insert_total_local_production_amount_edible_oil.php');

//include('insert_producer_skus_price.php');
//include('insert_producers_brand_name.php');
//include('insert_producers_supply.php');

//include('insert_importer_skus_price.php');
//include('insert_importers_brand_name.php');
//include('insert_importers_supply.php');

//include('insert_distribution_channels.php');

//include('insert_rawcrops.php');
//include('insert_foodtype.php');
//include('insert_foodvehicle.php'); 



-->