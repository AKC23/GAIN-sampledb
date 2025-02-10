<?php
// Include the database connection
include('db_connect.php');

// Disable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop table if exists
$dropTableSQL = "DROP TABLE IF EXISTS country";
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'country' dropped successfully.<br>";
} else {
    echo "Error dropping table: " . $conn->error . "<br>";
}

// Enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// SQL query to create the 'country' table
$createTableSQL = "
    CREATE TABLE country (
        Country_ID INT(11) AUTO_INCREMENT PRIMARY KEY,
        Country_Name VARCHAR(100) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

// Execute the query to create the table
if ($conn->query($createTableSQL) === TRUE) {
    echo "Table 'country' created successfully.<br>";
} else {
    echo "Error creating table 'country': " . $conn->error . "<br>";
}

// Path to your uploaded CSV file for country data
$csvFile = 'data/country.csv';  // Update with the correct path of your CSV file

// Open the CSV file for reading
if (($handle = fopen($csvFile, "r")) !== FALSE) {

    // Skip the header row
    fgetcsv($handle);

    // Read through each line of the CSV file
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        // CSV data: country_ID is in the first column (index 0), and country_Name is in the second column (index 1)
        $countryID = mysqli_real_escape_string($conn, trim($data[0]));
        $countryName = mysqli_real_escape_string($conn, trim($data[1]));

        // Ensure the country_Name is not empty
        if (!empty($countryName)) {
            // Prepare SQL query to insert the data into the 'country' table
            $sql = "INSERT INTO country (Country_ID, Country_Name) VALUES ('$countryID', '$countryName')";

            // Execute the query
            if ($conn->query($sql) === TRUE) {
                echo "country '$countryName' with ID '$countryID' inserted successfully.<br>";
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
