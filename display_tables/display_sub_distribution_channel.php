<?php

$sql = "
SELECT 
    sdc.SubDistributionChannelID,
    sdc.SubDistributionChannelName,
    dc.DistributionChannelName
FROM 
    subdistributionchannel sdc
JOIN 
    distributionchannel dc ON sdc.DistributionChannelID = dc.DistributionChannelID
ORDER BY 
    sdc.SubDistributionChannelID
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
