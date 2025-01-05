<?php
session_start();

// if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
//     header('Location: login.php');
//     exit;
// }

if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = [
        ['Calendar', '2015', '2015', 'Company A', 'Brand X', 'Wheat', 'Import', 10000, 5000],
        ['Fiscal', '2015', '2016', 'Company B', 'Brand Y', 'Oil', 'Production', 5000, 3000],
        ['Calendar', '2016', '2016', 'Company C', 'Brand Z', 'Wheat', 'Production', 8000, 4000],
        ['Fiscal', '2016', '2017', 'Company A', 'Brand X', 'Oil', 'Import', 6000, 3500],
        ['Calendar', '2017', '2017', 'Company B', 'Brand Y', 'Wheat', 'Import', 9000, 4500],
    ];
}

// Sort the data by Start Year
usort($_SESSION['data'], function($a, $b) {
    return $a[1] <=> $b[1];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database [Template]</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
        }
        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .table-fixed-header {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }
        .checkbox-group {
            max-height: 120px;
            overflow-y: auto;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }
        .unit-conversion {
            margin-bottom: 20px;
        }
        .currency-conversion {
            margin-bottom: 20px;
        }
        .conversion-group {
            margin-top: 20px;
        }
        .conversion-container {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h1 class="h2 text-center">Database on Edible Oil and Wheat Flour [Template]</h1>
            </div>
            <div class="col-auto">
                <a href="login.php" class="btn btn-secondary">
                    Admin Login
                </a>
            </div>
        </div>
        <form id="filterForm" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label>Start Year:</label>
                    <div class="checkbox-group">
                        <?php for ($year = 2016; $year <= 2026; $year++): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="startYear[]" id="year<?php echo $year; ?>" value="<?php echo $year; ?>">
                                <label class="form-check-label" for="year<?php echo $year; ?>"><?php echo $year; ?></label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="conversion-container">
                        <div>
                            <label>Product:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="product[]" id="oil" value="Oil">
                                <label class="form-check-label" for="oil">Oil</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="product[]" id="wheat" value="Wheat">
                                <label class="form-check-label" for="wheat">Wheat</label>
                            </div>
                        </div>
                        <div>
                            <label>Convert Amount to:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="unit" id="metricTon" value="1" checked>
                                <label class="form-check-label" for="metricTon">Metric Ton (t)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="unit" id="thousandTon" value="0.001">
                                <label class="form-check-label" for="thousandTon">1000 t</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="unit" id="kilogram" value="1000">
                                <label class="form-check-label" for="kilogram">Kilogram (kg)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="unit" id="gram" value="1000000">
                                <label class="form-check-label" for="gram">Gram (g)</label>
                            </div>
                        </div>
                        <div>
                            <label>Convert Price to:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currency" id="usd" value="1" checked>
                                <label class="form-check-label" for="usd">USD</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currency" id="bdt" value="119.42">
                                <label class="form-check-label" for="bdt">BDT</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currency" id="eur" value="0.81">
                                <label class="form-check-label" for="eur">EUR</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="currency" id="gbp" value="0.72">
                                <label class="form-check-label" for="gbp">GBP</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Filter</button>
        </form>
        <div class="table-container">
            <table class="table table-striped" id="resultsTable">
                <thead class="table-fixed-header">
                    <tr>
                        <th>Year Type</th>
                        <th>Start Year</th>
                        <th>End Year</th>
                        <th>Company</th>
                        <th>Brand</th>
                        <th>Product</th>
                        <th>Type</th>
                        <th>Amount (Metric Ton)</th>
                        <th>Price (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($_SESSION['data'] as $row) {
                        echo "<tr>";
                        echo "<td>{$row[0]}</td>"; // Year Type
                        echo "<td>{$row[1]}</td>"; // Start Year
                        echo "<td>{$row[2]}</td>"; // End Year
                        echo "<td>{$row[3]}</td>"; // Company
                        echo "<td>{$row[4]}</td>"; // Brand
                        echo "<td>{$row[5]}</td>"; // Product
                        echo "<td>{$row[6]}</td>"; // Type
                        echo "<td data-original-amount='{$row[7]}'>{$row[7]}</td>"; // Amount
                        echo "<td data-original-price='{$row[8]}'>{$row[8]}</td>"; // Price
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const startYears = Array.from(document.querySelectorAll('input[name="startYear[]"]:checked')).map(cb => cb.value);
            const products = Array.from(document.querySelectorAll('input[name="product[]"]:checked')).map(cb => cb.value);

            const table = document.getElementById('resultsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                const rowStartYear = cells[1].innerText;
                const rowProduct = cells[5].innerText;

                const yearMatch = startYears.length === 0 || startYears.includes(rowStartYear);
                const productMatch = products.length === 0 || products.includes(rowProduct);

                if (yearMatch && productMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });

        document.querySelectorAll('input[name="unit"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const conversionRate = parseFloat(this.value);
                const unitLabel = this.nextElementSibling.innerText;
                const table = document.getElementById('resultsTable');
                const rows = table.getElementsByTagName('tr');

                // Update the column header
                const headerCell = table.querySelector('thead th:nth-child(8)');
                headerCell.innerText = `Amount (${unitLabel.split(' ')[0]})`;

                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const amountCell = row.getElementsByTagName('td')[7];
                    const originalAmount = parseFloat(amountCell.getAttribute('data-original-amount'));
                    const convertedAmount = originalAmount * conversionRate;
                    amountCell.innerText = convertedAmount.toFixed(2);
                }
            });
        });

        document.querySelectorAll('input[name="currency"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const conversionRate = parseFloat(this.value);
                const currencyLabel = this.nextElementSibling.innerText;
                const table = document.getElementById('resultsTable');
                const rows = table.getElementsByTagName('tr');

                // Update the column header
                const headerCell = table.querySelector('thead th:nth-child(9)');
                headerCell.innerText = `Price (${currencyLabel})`;

                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const priceCell = row.getElementsByTagName('td')[8];
                    const originalPrice = parseFloat(priceCell.getAttribute('data-original-price'));
                    const convertedPrice = originalPrice * conversionRate;
                    priceCell.innerText = convertedPrice.toFixed(2);
                }
            });
        });

        window.addEventListener('load', function() {
            const table = document.getElementById('resultsTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const amountCell = row.getElementsByTagName('td')[7];
                const originalAmount = parseFloat(amountCell.innerText);
                amountCell.setAttribute('data-original-amount', originalAmount);

                const priceCell = row.getElementsByTagName('td')[8];
                const originalPrice = parseFloat(priceCell.innerText);
                priceCell.setAttribute('data-original-price', originalPrice);
            }
        });
    </script>
</body>
</html>
