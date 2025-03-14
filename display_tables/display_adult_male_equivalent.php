<?php

$sql = "
SELECT 
    ame.AMEID,
    ame.AME,
    g.GenderName,
    a.AgeRange
FROM 
    adultmaleequivalent ame
JOIN 
    gender g ON ame.GenderID = g.GenderID
JOIN 
    age a ON ame.AgeID = a.AgeID
ORDER BY 
    ame.AMEID
";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>AME ID</th>';
    echo '<th>AME</th>';
    echo '<th>Gender</th>';
    echo '<th>Age Range</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row['AMEID']) . '</td>';
        echo '<td>' . htmlspecialchars($row['AME']) . '</td>';
        echo '<td>' . htmlspecialchars($row['GenderName']) . '</td>';
        echo '<td>' . htmlspecialchars($row['AgeRange']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo 'No records found';
}

$conn->close();
?>
