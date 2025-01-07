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
            max-height: 500px; /* Set max height as needed */
            overflow-y: auto;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #d3a79e;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="center-title">Supply Chain</h1>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <?php
                // Path to your CSV file
                $csvFile = 'data/supply_chain_final.csv';  // Update with the exact path of your CSV file

                if (!file_exists($csvFile)) {
                    die("Error: CSV file '$csvFile' not found.<br>");
                }


                // Check for BOM and remove if present
                $content = file_get_contents($csvFile);
                if ($content === false) {
                    die("Error: Could not read CSV file.<br>");
                }

                // Check for UTF-8 BOM and remove it
                $bom = pack('H*','EFBBBF');
                if (strncmp($content, $bom, 3) === 0) {
                    echo "Found and removing UTF-8 BOM from CSV file.<br>";
                    $content = substr($content, 3);
                }

                // Normalize line endings
                $content = str_replace("\r\n", "\n", $content);
                $content = str_replace("\r", "\n", $content);
                $lines = explode("\n", $content);

                // Remove any empty lines
                $lines = array_filter($lines, function($line) {
                    return trim($line) !== '';
                });

                // Save normalized content to temp file
                $tempFile = $csvFile . '.tmp';
                file_put_contents($tempFile, implode("\n", $lines));
                $csvFile = $tempFile;

                if (($handle = fopen($csvFile, "r")) !== FALSE) {
                    // Read and display the header row
                    $header = fgetcsv($handle, 1000, ",");
                    if ($header !== FALSE) {
                        echo "<thead class='thead-dark'><tr>";
                        foreach ($header as $col) {
                            echo "<th>" . htmlspecialchars($col) . "</th>";
                        }
                        echo "</tr></thead><tbody>";
                    }

                    // Read and display the data rows
                    $rowNumber = 1;
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        echo "<tr>";
                        foreach ($data as $col) {
                            echo "<td>" . htmlspecialchars($col) . "</td>";
                        }
                        echo "</tr>";
                        $rowNumber++;
                    }
                    echo "</tbody></table>";
                    echo "</div>";

                    fclose($handle);
                } else {
                    echo "Error: Could not open CSV file.<br>";
                }
                ?>
        </div>
    </div>

    <!-- Bootstrap and jQuery scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
