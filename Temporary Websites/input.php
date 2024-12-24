<?php
session_start();

if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = [];
}

// Add year type options
$yearTypes = ['Calendar', 'Fiscal'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $yearType = $_POST['yearType'];
    $startYear = $_POST['startYear'];
    $endYear = ($yearType === 'Fiscal') ? $startYear + 1 : $startYear;
    
    $newData = [
        $yearType,
        $startYear,
        $endYear,
        $_POST['company'],
        $_POST['brand'],
        $_POST['product'],
        $_POST['type'],
        $_POST['amount'],
        $_POST['price']
    ];
    $_SESSION['data'][] = $newData;

    // Sort the data by Start Year
    usort($_SESSION['data'], function($a, $b) {
        return $a[1] <=> $b[1];
    });

    header('Location: index3.php');
    exit;
}

// Sample dropdown options
$years = range(2015, 2026);
$companies = ['Company A', 'Company B', 'Company C'];
$brands = ['Brand X', 'Brand Y', 'Brand Z'];
$products = ['Wheat', 'Oil'];
$types = ['Import', 'Production'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Input</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function updateEndYear() {
            const yearType = document.getElementById('yearType').value;
            const startYear = parseInt(document.getElementById('startYear').value);
            const endYear = (yearType === 'Fiscal') ? startYear + 1 : startYear;
            document.getElementById('endYear').value = endYear;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Add Data</h2>
        <form method="POST">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="yearType">Year Type:</label>
                        <select class="form-control" id="yearType" name="yearType" onchange="updateEndYear()" required>
                            <?php foreach ($yearTypes as $type): ?>
                                <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="startYear">Start Year:</label>
                        <select class="form-control" id="startYear" name="startYear" onchange="updateEndYear()" required>
                            <?php foreach ($years as $year): ?>
                                <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="endYear">End Year (Auto Selected)</label>
                        <input type="text" class="form-control" id="endYear" name="endYear" readonly>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="company">Company:</label>
                <select class="form-control" id="company" name="company" required>
                    <?php foreach ($companies as $company): ?>
                        <option value="<?php echo $company; ?>"><?php echo $company; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="brand">Brand:</label>
                <select class="form-control" id="brand" name="brand" required>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?php echo $brand; ?>"><?php echo $brand; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <select class="form-control" id="type" name="type" required>
                    <?php foreach ($types as $type): ?>
                        <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="product">Product:</label>
                <select class="form-control" id="product" name="product" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product; ?>"><?php echo $product; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount (Metric Ton):</label>
                <input type="number" class="form-control" id="amount" name="amount" required>
            </div>
            <div class="form-group">
                <label for="price">Price (USD):</label>
                <input type="number" class="form-control" id="price" name="price" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form>
    </div>
</body>
</html>
