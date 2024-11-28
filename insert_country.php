<?php
// Include the database connection
include('db_connect.php');

// SQL query to create the 'Country' table
$createTableSQL = "
    CREATE TABLE Country (
        Country_ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        Country_Name VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'Country' created successfully.<br>";
} else {
    echo "Error creating table 'Country': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file for Country data
$csvFile = 'country.csv';  // Update with the correct path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row
    fgetcsv($handle);

    // Read through each line of the CSV file
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        // CSV data: Country_ID is in the first column (index 0), and Country_Name is in the second column (index 1)
        $countryID = mysqli_real_escape_string($conn, trim($data[0]));
        $countryName = mysqli_real_escape_string($conn, trim($data[1]));

        // Ensure the Country_Name is not empty
        if (!empty($countryName)) {
            // Prepare SQL query to insert the data into the 'Country' table
            $sql = "INSERT INTO Country (Country_ID, Country_Name) VALUES ('$countryID', '$countryName')";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                echo "Country '$countryName' with ID '$countryID' inserted successfully.<br>";
            } else {
                echo "Error inserting '$countryName': " . $conn->error . "<br>";
            }
        } else {
            echo "Skipping empty row or missing data.<br>";
        }
    }

    // Close the file after reading
    fclose($handle);
} else {
    echo "Error: Could not open CSV file.<br>";
}

// Note: Do not close the database connection here
// The connection will be closed by index.php
?>
