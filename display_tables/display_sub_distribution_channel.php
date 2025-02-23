<?php
echo "<div class='table-responsive'><table class='table table-bordered'><thead><tr>";
// Fetch and display table headers
echo "<th>Sub Distribution Channel ID</th>";
echo "<th>Sub Distribution Channel Name</th>";
echo "<th>Distribution Channel Name</th>";
echo "</tr></thead><tbody>";

// Fetch and display table rows
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['SubDistributionChannelID']) . "</td>";
    echo "<td>" . htmlspecialchars($row['SubDistributionChannelName']) . "</td>";

    // Fetch Distribution Channel Name from distributionchannel table
    $distributionChannelID = htmlspecialchars($row['DistributionChannelID']);
    $distributionChannelQuery = $conn->query("SELECT DistributionChannelName FROM distributionchannel WHERE DistributionChannelID = $distributionChannelID");
    if ($distributionChannelRow = $distributionChannelQuery->fetch_assoc()) {
        echo "<td>" . htmlspecialchars($distributionChannelRow['DistributionChannelName']) . "</td>";
    } else {
        echo "<td>N/A</td>";
    }

    echo "</tr>";
}
echo "</tbody></table></div>";
?>
