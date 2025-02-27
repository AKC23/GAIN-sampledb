<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["loginEmail"];
    $password = $_POST["loginPassword"];

    // Default admin credentials
    $adminEmail = "admin";
    $adminPassword = "admin";

    echo "Entered Email: " . $email . "<br>";
    echo "Entered Password: " . $password . "<br>";
    echo "Admin Email: " . $adminEmail . "<br>";
    echo "Admin Password: " . $adminPassword . "<br>";

    // Check if the entered credentials match the default admin credentials
    if ($email === $adminEmail && $password === $adminPassword) {
        // Redirect to admin dashboard or desired page
        echo "Admin Login successful!";
        header("Location: index_admin.php"); // Redirect to index_admin.php
        exit();
    } else {
        // Check if the entered credentials match any user in the database
        $sql = "SELECT * FROM userinfo WHERE email = '$email' AND password = '$password'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Redirect to user dashboard or desired page
            echo "User Login successful!";
            header("Location: index_user.php"); // Redirect to index_user.php
            exit();
        } else {
            // Display an error message for incorrect credentials
            echo "Incorrect email or password.";
        }
    }

    $conn->close();
} else {
    echo "No POST request received.";
}
?>
