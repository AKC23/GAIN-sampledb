<?php

$sql = "
SELECT 
    p.ProductID,
    p.ProductName,
    b.BrandName,
    c.CompanyName,
    ft.FoodTypeName
FROM 
    product p
JOIN 
    brand b ON p.BrandID = b.BrandID
JOIN 
    company c ON p.CompanyID = c.CompanyID
JOIN 
    foodtype ft ON p.FoodTypeID = ft.FoodTypeID
ORDER BY 
    p.ProductID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
    // Fetch and display table headers
    while ($fieldInfo = $result->fetch_field()) {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
    echo "</tr></thead><tbody>";
    // Fetch and display table rows
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
} else {
    echo 'No records found';
}

$conn->close();
?>
