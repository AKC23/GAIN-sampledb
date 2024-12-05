<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producer Distribution Table</title>
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

        .table-responsive {
            margin-top: 20px;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #d3a79e;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="center-title">Producer Distribution Table</h1>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Food Vehicle</th>
                        <th>Food Type</th>
                        <th>Processing Stage</th>
                        <th>Brand</th>
                        <th>Producer</th>
                        <th>District</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Country</th>
                        <th>Origin Country</th>
                        <th>Producer Type</th>
                        <th>Importer</th>
                        <th>Refinery</th>
                        <th>Distributer</th>
                        <th>Repacker</th>
                        <th>SKU</th>
                        <th>Unit</th>
                        <th>Packaging</th>
                        <th>Price</th>
                        <th>Unit</th>
                        <th>SourceURL</th>
                        <th>Date Accessed</th>
                        <th>Process to Obtain Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $csvFile = 'Producer_Distribution_Table.csv';
                    if (($handle = fopen($csvFile, "r")) !== FALSE) {
                        // Skip header row
                        fgetcsv($handle);
                        
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            echo "<tr>";
                            foreach ($data as $cell) {
                                echo "<td>" . htmlspecialchars($cell) . "</td>";
                            }
                            echo "</tr>";
                        }
                        fclose($handle);
                    } else {
                        echo "<tr><td colspan='23'>Error: Could not open file $csvFile</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap and jQuery scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
