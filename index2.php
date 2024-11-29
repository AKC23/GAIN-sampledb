<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body { 
            padding-top: 20px; 
            background-color: #f8f9fa;
        }
        .form-group { 
            margin-bottom: 15px; 
        }
        .select2-container { 
            width: 100% !important; 
        }
        .table-container {
            max-height: 600px;
            overflow-y: auto;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table-fixed-header {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }
        .card {
            margin-top: 20px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #c8e5bf;
            border-radius: 8px;
            background-color: white;
        }
        .center-title {
            text-align: center;
            color: #000;
            margin-top: 20px;
            margin-bottom: 20px;
            font-weight: 700;
        }
        .filter-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .debug-info {
            margin-top: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="center-title">Database Management System</h1>

        <div class="filter-section">
            <form id="filterForm" method="post">
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Select Table:</label>
                            <select class="form-select" name="selected_table" id="tableSelect">
                                <?php
                                include('db_connect.php');
                                error_reporting(E_ALL);
                                ini_set('display_errors', 1);
                                
                                $tables = array(
                                    'total_local_crop_production' => 'Total Local Crop Production',
                                    'import_amount_oilseeds' => 'Import Amount Oilseeds',
                                    'total_local_production_amount_edible_oil' => 'Total Local Production Amount Edible Oil',
                                    'import_edible_oil' => 'Import Edible Oil'
                                );
                                
                                foreach($tables as $table_name => $display_name) {
                                    // Check if table exists
                                    $table_exists = $conn->query("SHOW TABLES LIKE '$table_name'");
                                    if($table_exists->num_rows > 0) {
                                        $selected = (isset($_POST['selected_table']) && $_POST['selected_table'] == $table_name) ? 'selected' : '';
                                        echo "<option value='$table_name' $selected>$display_name</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Select Start Year:</label>
                            <select class="form-select select2-multi" name="startyears[]" multiple="multiple" id="yearSelect">
                                <?php
                                if(isset($_POST['selected_table'])) {
                                    $table = $_POST['selected_table'];
                                } else {
                                    $table = 'total_local_crop_production';
                                }
                                
                                // Check if table exists
                                $table_exists = $conn->query("SHOW TABLES LIKE '$table'");
                                if($table_exists->num_rows > 0) {
                                    $years_query = "SELECT DISTINCT StartYear FROM $table ORDER BY StartYear";
                                    $years_result = $conn->query($years_query);
                                    
                                    if($years_result) {
                                        while($year = $years_result->fetch_array()) {
                                            if($year[0]) {
                                                $selected = (isset($_POST['startyears']) && in_array($year[0], $_POST['startyears'])) ? 'selected' : '';
                                                echo "<option value='" . htmlspecialchars($year[0]) . "' $selected>" . htmlspecialchars($year[0]) . "</option>";
                                            }
                                        }
                                    } else {
                                        echo "<option value=''>Error loading years</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Submit</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-container">
            <table class="table table-striped table-bordered" id="resultsTable">
                <thead class="table-fixed-header">
                    <?php
                    if(isset($_POST['selected_table'])) {
                        $table = $_POST['selected_table'];
                        
                        // Debug information
                        echo "<!-- Debug: POST data = " . htmlspecialchars(print_r($_POST, true)) . " -->";
                        
                        // Check if table exists
                        $table_exists = $conn->query("SHOW TABLES LIKE '$table'");
                        if($table_exists->num_rows > 0) {
                            // Get column names
                            $columns_query = "SHOW COLUMNS FROM $table";
                            $columns_result = $conn->query($columns_query);
                            
                            if($columns_result) {
                                echo "<tr>";
                                while($column = $columns_result->fetch_assoc()) {
                                    $header_name = $column['Field'];
                                    switch($column['Field']) {
                                        case 'VehicleID':
                                            $header_name = 'Vehicle';
                                            break;
                                        case 'FoodTypeID':
                                            $header_name = 'Food Type';
                                            break;
                                        case 'RawCropsID':
                                            $header_name = 'Raw Crops';
                                            break;
                                        case 'CountryID':
                                            $header_name = 'Country';
                                            break;
                                        default:
                                            $header_name = ucfirst(str_replace('_', ' ', $column['Field']));
                                    }
                                    echo "<th>" . htmlspecialchars($header_name) . "</th>";
                                }
                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </thead>
                <tbody>
                    <?php
                    if(isset($_POST['selected_table'])) {
                        $table = $_POST['selected_table'];
                        
                        // Check if table exists
                        $table_exists = $conn->query("SHOW TABLES LIKE '$table'");
                        if($table_exists->num_rows > 0) {
                            // Get the year column name based on table
                            $year_column = ($table === 'import_amount_oilseeds') ? 'Start_Year' : 'StartYear';
                            
                            // Build the query
                            $query = "SELECT * FROM $table WHERE 1=1";
                            
                            // Add year filter if years are selected
                            if (!empty($_POST['startyears'])) {
                                $years = array_map(function($year) use ($conn) {
                                    return "'" . $conn->real_escape_string($year) . "'";
                                }, $_POST['startyears']);
                                $years_str = implode(",", $years);
                                $query .= " AND $year_column IN ($years_str)";
                            }

                            $query .= " ORDER BY $year_column LIMIT 1000";
                            
                            // Debug information
                            echo "<!-- Debug: Query = " . htmlspecialchars($query) . " -->";
                            
                            $result = $conn->query($query);
                            
                            if ($result) {
                                // Get column names
                                echo "<tr>";
                                $fields = $result->fetch_fields();
                                foreach ($fields as $field) {
                                    echo "<th>" . htmlspecialchars($field->name) . "</th>";
                                }
                                echo "</tr>";
                                
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        foreach($row as $value) {
                                            echo "<td>" . htmlspecialchars($value) . "</td>";
                                        }
                                        echo "</tr>";
                                    }
                                    echo "<!-- Debug: Found " . $result->num_rows . " rows -->";
                                } else {
                                    echo "<tr><td colspan='100%'>No records found for query: " . htmlspecialchars($query) . "</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='100%'>Error executing query: " . $conn->error . "<br>Query was: " . htmlspecialchars($query) . "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='100%'>Selected table does not exist</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2-multi').select2({
                placeholder: "Select years",
                allowClear: true,
                width: '100%'
            });

            // Function to load years
            function loadYears(table) {
                $.ajax({
                    url: 'get_years.php',
                    data: { table: table },
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var yearSelect = $('#yearSelect');
                        yearSelect.empty();
                        
                        if(response && response.length > 0) {
                            response.forEach(function(year) {
                                yearSelect.append(new Option(year, year));
                            });
                        } else {
                            yearSelect.append(new Option('No years available', ''));
                        }
                        
                        yearSelect.trigger('change');
                    },
                    error: function(xhr, status, error) {
                        console.error("Error loading years:", error);
                        var yearSelect = $('#yearSelect');
                        yearSelect.empty();
                        yearSelect.append(new Option('Error loading years', ''));
                        yearSelect.trigger('change');
                    }
                });
            }

            // Load years when table selection changes
            $('#tableSelect').change(function() {
                var selectedTable = $(this).val();
                if(selectedTable) {
                    loadYears(selectedTable);
                }
            });

            // Initial load of years if table is selected
            var initialTable = $('#tableSelect').val();
            if(initialTable) {
                loadYears(initialTable);
            }
        });
    </script>
</body>
</html>
