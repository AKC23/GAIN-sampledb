<?php
// index3.php
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

        .form-group {
            margin-bottom: 15px;
        }

        .checkbox-group {
            height: 120px;
            overflow-y: auto;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
        }

        .checkbox-group .form-check {
            padding-left: 1.5rem;
        }

        .checkbox-group .form-check-input {
            margin-left: -1.5rem;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col">
            <h1 class="h2" style="text-align: center;">Database on Edible Oil and Wheat Flour [Template]</h1>

            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#loginModal">
                    Admin Login
                </button>
            </div>
        </div>

        <form id="dataForm">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Select Years:</label>
                        <div class="checkbox-group">
                            <?php
                            for ($year = 2015; $year <= 2026; $year++) {
                                echo "<div class='form-check'>";
                                echo "<input class='form-check-input' type='checkbox' name='years[]' value='$year' id='year$year'>";
                                echo "<label class='form-check-label' for='year$year'>$year</label>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Select Companies:</label>
                        <div class="checkbox-group">
                            <?php
                            $companies = ['A', 'B', 'C', 'D', 'E', 'F'];
                            foreach ($companies as $company) {
                                echo "<div class='form-check'>";
                                echo "<input class='form-check-input' type='checkbox' name='companies[]' value='$company' id='company$company'>";
                                echo "<label class='form-check-label' for='company$company'>Company $company</label>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Select Brands:</label>
                        <div class="checkbox-group">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo "<div class='form-check'>";
                                echo "<input class='form-check-input' type='checkbox' name='brands[]' value='Brand $i' id='brand$i'>";
                                echo "<label class='form-check-label' for='brand$i'>Brand $i</label>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label>Select Product:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="product" id="wheat" value="Wheat">
                            <label class="form-check-label" for="wheat">
                                Wheat
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="product" id="oil" value="Oil">
                            <label class="form-check-label" for="oil">
                                Oil
                            </label>
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label>Select Type:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="type" id="import" value="Import">
                            <label class="form-check-label" for="import">
                                Import
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="type" id="production" value="Production">
                            <label class="form-check-label" for="production">
                                Production
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Select Currency:</label>
                        <div class="checkbox-group">
                            <?php
                            $currencies = ['USD', 'EUR', 'CAD', 'AUD', 'BDT', 'INR'];
                            foreach ($currencies as $currency) {
                                echo "<div class='form-check'>";
                                echo "<input class='form-check-input' type='radio' name='currency' value='$currency' id='currency$currency'>";
                                echo "<label class='form-check-label' for='currency$currency'>$currency</label>";
                                echo "</div>";
                            }
                            ?>






                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form>

        <div class="mt-4">
            <h2>Results</h2>
            <div class="table-container">
                <table class="table table-striped" id="resultsTable">
                    <thead class="table-fixed-header">
                        <tr>
                            <th>Year</th>
                            <th>Company</th>
                            <th>Brand</th>
                            <th>Product</th>
                            <th>Type</th>
                            <th>Amount (Tons)</th>
                            <th id="priceHeader">Price (USD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $data = [
                            ['2015', 'Company A', 'Brand 1', 'Wheat', 'Import', 10000, 5000],
                            ['2015', 'Company B', 'Brand 2', 'Oil', 'Production', 5000, 3000],
                            ['2015', 'Company C', 'Brand 3', 'Wheat', 'Production', 8000, 4000],
                            ['2015', 'Company D', 'Brand 1', 'Oil', 'Import', 6000, 3500],
                            ['2015', 'Company E', 'Brand 2', 'Wheat', 'Import', 9000, 4500],
                            ['2016', 'Company A', 'Brand 2', 'Wheat', 'Production', 12000, 6000],
                            ['2016', 'Company B', 'Brand 3', 'Oil', 'Import', 7000, 4200],
                            ['2016', 'Company C', 'Brand 1', 'Wheat', 'Import', 11000, 5500],
                            ['2016', 'Company D', 'Brand 2', 'Oil', 'Production', 5500, 3300],
                            ['2016', 'Company E', 'Brand 3', 'Wheat', 'Production', 9500, 4750],
                            ['2017', 'Company A', 'Brand 1', 'Oil', 'Import', 8500, 5100],
                            ['2017', 'Company B', 'Brand 2', 'Wheat', 'Production', 13000, 6500],
                            ['2017', 'Company C', 'Brand 3', 'Oil', 'Production', 6500, 3900],
                            ['2017', 'Company D', 'Brand 1', 'Wheat', 'Import', 10500, 5250],
                            ['2017', 'Company E', 'Brand 2', 'Oil', 'Import', 7500, 4500],
                            ['2018', 'Company A', 'Brand 3', 'Wheat', 'Production', 14000, 7000],
                            ['2018', 'Company B', 'Brand 1', 'Oil', 'Import', 9000, 5400],
                            ['2018', 'Company C', 'Brand 2', 'Wheat', 'Import', 12000, 6000],
                            ['2018', 'Company D', 'Brand 3', 'Oil', 'Production', 7000, 4200],
                            ['2018', 'Company E', 'Brand 1', 'Wheat', 'Production', 11000, 5500],
                            ['2019', 'Company A', 'Brand 2', 'Oil', 'Import', 9500, 5700],
                            ['2019', 'Company B', 'Brand 3', 'Wheat', 'Production', 15000, 7500],
                            ['2019', 'Company C', 'Brand 1', 'Oil', 'Production', 8000, 4800],
                            ['2019', 'Company D', 'Brand 2', 'Wheat', 'Import', 13000, 6500],
                            ['2019', 'Company E', 'Brand 3', 'Oil', 'Import', 8500, 5100],
                            ['2020', 'Company A', 'Brand 1', 'Wheat', 'Production', 16000, 8000],
                            ['2020', 'Company B', 'Brand 2', 'Oil', 'Import', 10000, 6000],
                            ['2020', 'Company C', 'Brand 3', 'Wheat', 'Import', 14000, 7000],
                            ['2020', 'Company D', 'Brand 1', 'Oil', 'Production', 8500, 5100],
                            ['2020', 'Company E', 'Brand 2', 'Wheat', 'Production', 12500, 6250],
                            ['2021', 'Company A', 'Brand 3', 'Oil', 'Import', 11000, 6600],
                            ['2021', 'Company B', 'Brand 1', 'Wheat', 'Production', 17000, 8500],
                            ['2021', 'Company C', 'Brand 2', 'Oil', 'Production', 9500, 5700],
                            ['2021', 'Company D', 'Brand 3', 'Wheat', 'Import', 15000, 7500],
                            ['2021', 'Company E', 'Brand 1', 'Oil', 'Import', 10000, 6000],
                            ['2022', 'Company A', 'Brand 2', 'Wheat', 'Production', 18000, 9000],
                            ['2022', 'Company B', 'Brand 3', 'Oil', 'Import', 11500, 6900],
                            ['2022', 'Company C', 'Brand 1', 'Wheat', 'Import', 16000, 8000],
                            ['2022', 'Company D', 'Brand 2', 'Oil', 'Production', 10000, 6000],
                            ['2022', 'Company E', 'Brand 3', 'Wheat', 'Production', 14000, 7000],
                            ['2023', 'Company A', 'Brand 1', 'Oil', 'Import', 12000, 7200],
                            ['2023', 'Company B', 'Brand 2', 'Wheat', 'Production', 19000, 9500],
                            ['2023', 'Company C', 'Brand 3', 'Oil', 'Production', 10500, 6300],
                            ['2023', 'Company D', 'Brand 1', 'Wheat', 'Import', 17000, 8500],
                            ['2023', 'Company E', 'Brand 2', 'Oil', 'Import', 11000, 6600],
                            ['2024', 'Company A', 'Brand 3', 'Wheat', 'Production', 20000, 10000],
                            ['2024', 'Company B', 'Brand 1', 'Oil', 'Import', 12500, 7500],
                            ['2024', 'Company C', 'Brand 2', 'Wheat', 'Import', 18000, 9000],
                            ['2024', 'Company D', 'Brand 3', 'Oil', 'Production', 11000, 6600],
                            ['2024', 'Company E', 'Brand 1', 'Wheat', 'Production', 15500, 7750],
                            ['2025', 'Company A', 'Brand 2', 'Oil', 'Import', 13000, 7800],
                            ['2025', 'Company B', 'Brand 3', 'Wheat', 'Production', 21000, 10500],
                            ['2025', 'Company C', 'Brand 1', 'Oil', 'Production', 11500, 6900],
                            ['2025', 'Company D', 'Brand 2', 'Wheat', 'Import', 19000, 9500],
                            ['2025', 'Company E', 'Brand 3', 'Oil', 'Import', 12000, 7200],
                            ['2026', 'Company A', 'Brand 1', 'Wheat', 'Production', 22000, 11000],
                            ['2026', 'Company B', 'Brand 2', 'Oil', 'Import', 13500, 8100],
                            ['2026', 'Company C', 'Brand 3', 'Wheat', 'Import', 20000, 10000],
                            ['2026', 'Company D', 'Brand 1', 'Oil', 'Production', 12000, 7200],
                            ['2026', 'Company E', 'Brand 2', 'Wheat', 'Production', 17000, 8500],
                        ];
                        

                        foreach ($data as $row) {
                        echo "<tr>";
                            echo "<td>{$row[0]}</td>"; // Year
                            echo "<td>{$row[1]}</td>"; // Company
                            echo "<td>{$row[2]}</td>"; // Brand
                            echo "<td>{$row[3]}</td>"; // Product
                            echo "<td>{$row[4]}</td>"; // Type
                            echo "<td>{$row[5]}</td>"; // Amount
                            // Add data-usd-price attribute for JavaScript
                            echo "<td data-usd-price='{$row[6]}'>{$row[6]} USD</td>"; // Price
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Admin Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Conversion rates relative to USD
        const conversionRates = {
            USD: 1,
            EUR: 0.95,
            CAD: 1.41,
            AUD: 1.54,
            BDT: 119.8,
            INR: 84.4,
        };

        // Function to update prices
        function updatePrices(currency) {
            // Get all rows in the table
            const rows = document.querySelectorAll("#resultsTable tbody tr");

            rows.forEach(row => {
                const priceCell = row.cells[6]; // Assuming the price is in the 7th column
                const basePriceUSD = priceCell.getAttribute("data-usd-price");

                if (basePriceUSD) {
                    // Convert and update the price
                    const convertedPrice = (basePriceUSD * conversionRates[currency]).toFixed(2);
                    priceCell.textContent = `${convertedPrice} ${currency}`;
                }
            });
        }

        // Add event listener for currency change
        document.querySelectorAll("input[name='currency']").forEach(input => {
            input.addEventListener("change", event => {
                const selectedCurrency = event.target.value;
                updatePrices(selectedCurrency);
            });
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('dataForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const years = Array.from(document.querySelectorAll('input[name="years[]"]:checked')).map(cb => cb.value);
            const companies = Array.from(document.querySelectorAll('input[name="companies[]"]:checked')).map(cb => cb.value);
            const brands = Array.from(document.querySelectorAll('input[name="brands[]"]:checked')).map(cb => cb.value);
            const products = Array.from(document.querySelectorAll('input[name="product"]:checked')).map(cb => cb.value);
            const types = Array.from(document.querySelectorAll('input[name="type"]:checked')).map(cb => cb.value);
            const selectedCurrency = document.querySelector('input[name="currency"]:checked')?.value || 'USD';

            const table = document.getElementById('resultsTable');
            const rows = table.getElementsByTagName('tr');
            const priceHeader = document.getElementById('priceHeader');

            // Update price header
            priceHeader.textContent = `Price (${selectedCurrency})`;

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const cells = row.getElementsByTagName('td');
                const rowYear = cells[0].innerText;
                const rowCompany = cells[1].innerText;
                const rowBrand = cells[2].innerText;
                const rowProduct = cells[3].innerText;
                const rowType = cells[4].innerText;
                const rowPrice = parseFloat(cells[6].innerText);

                const yearMatch = years.length === 0 || years.includes(rowYear);
                const companyMatch = companies.length === 0 || companies.includes(rowCompany.split(' ')[1]);
                const brandMatch = brands.length === 0 || brands.includes(rowBrand);
                const productMatch = products.length === 0 || products.includes(rowProduct);
                const typeMatch = types.length === 0 || types.includes(rowType);

                if (yearMatch && companyMatch && brandMatch && productMatch && typeMatch) {
                    row.style.display = '';

                    // Update price based on selected currency
                    const convertedPrice = convertPrice(rowPrice, 'USD', selectedCurrency);
                    cells[6].innerText = convertedPrice.toFixed(2);
                } else {
                    row.style.display = 'none';
                }
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Login functionality would be implemented here.');
        });

        function convertPrice(price, fromCurrency, toCurrency) {
            // Exchange rates as provided
            const rates = {
                'USD': 1,
                'EUR': 0.94,
                'CAD': 1.35,
                'AUD': 1.50,
                'BDT': 120,
                'INR': 83
            };

            // Convert to USD first (if not already in USD)
            const usdPrice = price / rates[fromCurrency];

            // Convert from USD to target currency
            return usdPrice * rates[toCurrency];
        }
    </script>
</body>

</html>