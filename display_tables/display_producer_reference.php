<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
while ($fieldInfo = $result->fetch_field()) {
    if ($fieldInfo->name == 'CountryID') {
        echo "<th>CountryName</th>";
    } elseif ($fieldInfo->name == 'CompanyID') {
        echo "<th>CompanyName</th>";
    } else {
        echo "<th>" . htmlspecialchars($fieldInfo->name) . "</th>";
    }
}
echo "</tr></thead><tbody>";
// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $key => $cell) {
        if ($key == 'CountryID') {
            // Fetch the CountryName based on CountryID
            $countryResult = $conn->query("SELECT CountryName FROM country WHERE CountryID = $cell");
            if ($countryRow = $countryResult->fetch_assoc()) {
                echo "<td>" . htmlspecialchars($countryRow['CountryName']) . "</td>";
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
        } else {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
    }
    echo "</tr>";
}
echo "</tbody></table></div>";
?>
