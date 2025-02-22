<?php

// SQL query to fetch data from producersku and related tables
$sql = "
    SELECT 
        ps.SKUID,
        p.ProductName,
        c.CompanyName,
        ps.SKU,
        ps.Unit,
        pt.PackagingTypeName,
        ps.Price,
        mc.CurrencyName,
        r.ReferenceNumber,
        r.Source,
        r.Link,
        r.ProcessToObtainData,
        r.AccessDate
    FROM 
        producersku ps
    JOIN 
        product p ON ps.ProductID = p.ProductID
    JOIN 
        company c ON ps.CompanyID = c.CompanyID
    JOIN 
        packagingtype pt ON ps.PackagingTypeID = pt.PackagingTypeID
    JOIN 
        measurecurrency mc ON ps.CurrencyID = mc.MCID
    JOIN 
        reference r ON ps.ReferenceID = r.ReferenceID
    ORDER BY 
        ps.SKUID";

// Execute the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    echo "<th>SKUID</th>";
    echo "<th>ProductName</th>";
    echo "<th>CompanyName</th>";
    echo "<th>SKU</th>";
    echo "<th>Unit</th>";
    echo "<th>PackagingTypeName</th>";
    echo "<th>Price</th>";
    echo "<th>CurrencyName</th>";
    echo "<th>ReferenceNumber</th>";
    echo "<th>Source</th>";
    echo "<th>Link</th>";
    echo "<th>ProcessToObtainData</th>";
    echo "<th>AccessDate</th>";
    echo "</tr></thead><tbody>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['SKUID']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProductName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CompanyName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['SKU']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Unit']) . "</td>";
        echo "<td>" . htmlspecialchars($row['PackagingTypeName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Price']) . "</td>";
        echo "<td>" . htmlspecialchars($row['CurrencyName']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ReferenceNumber']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Source']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Link']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ProcessToObtainData']) . "</td>";
        echo "<td>" . htmlspecialchars($row['AccessDate']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo "0 results";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
