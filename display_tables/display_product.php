<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
while ($fieldInfo = $result->fetch_field()) {
    if ($fieldInfo->name == 'BrandID') {
        echo "<th>BrandName</th>";
    } elseif ($fieldInfo->name == 'CompanyID') {
        echo "<th>CompanyName</th>";
    } elseif ($fieldInfo->name == 'FoodTypeID') {
        echo "<th>FoodTypeName</th>";
    } else {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
}
echo "</tr></thead><tbody>";
// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $key => $cell) {
        if ($key == 'BrandID') {
            // Fetch the BrandName based on BrandID
            $brandResult = $conn->query("SELECT BrandName FROM brand WHERE BrandID = $cell");
            if ($brandRow = $brandResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($brandRow['BrandName']) . "</td>";
            } else {
                echo "<td>N/A</td>";
            }
        } elseif ($key == 'CompanyID') {
            // Fetch the CompanyName based on CompanyID
            $companyResult = $conn->query("SELECT CompanyName FROM company WHERE CompanyID = $cell");
            if ($companyRow = $companyResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($companyRow['CompanyName']) . "</td>";
            } else {
                echo "<td>N/A</td>";
            }
        } elseif ($key == 'FoodTypeID') {
            // Fetch the FoodTypeName based on FoodTypeID
            $foodTypeResult = $conn->query("SELECT FoodTypeName FROM foodtype WHERE FoodTypeID = $cell");
            if ($foodTypeRow = $foodTypeResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($foodTypeRow['FoodTypeName']) . "</td>";
            } else {
                echo "<td>N/A</td>";
            }
        } else {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
    }
    echo "</tr>";
}
echo "</tbody></table></div>";
?>
