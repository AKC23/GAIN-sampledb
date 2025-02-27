<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["registerName"];
    $username = $_POST["registerUsername"];
    $email = $_POST["registerEmail"];
    $password = $_POST["registerPassword"]; // In real-world, hash this password
    $repeatPassword = $_POST["registerRepeatPassword"];
    $birthday = $_POST["registerBirthday"];

    // Validate password match
    if ($password != $repeatPassword) {
        die("Passwords do not match.");
    }

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM userinfo WHERE email = '$email'";
    $checkEmailResult = $conn->query($checkEmailQuery);

    if ($checkEmailResult->num_rows > 0) {
        die("Email already exists. Please use a different email.");
    } else {
        // If the email is unique, proceed with registration

        // Prepare SQL
        $sql = "INSERT INTO userinfo (name, username, email, password, birthday) VALUES ('$name', '$username', '$email', '$password', '$birthday')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
            header("Location: login_register.php"); // Redirect to login page after successful registration
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
}
?>
