<?php
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["registerUsername"];
    $email = $_POST["registerEmail"];
    $password = $_POST["registerPassword"]; // In real-world, hash this password
    $repeatPassword = $_POST["registerRepeatPassword"];
    $birthday = $_POST["registerBirthday"];

    // Validate password match
    if ($password != $repeatPassword) {
        echo "<script>alert('Passwords do not match.'); window.location.href = 'login_register.php';</script>";
        exit();
    }

    // Check if the email already exists
    $checkEmailQuery = "SELECT * FROM userinfo WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('User profile already exists.'); window.location.href = 'login_register.php';</script>";
    } else {
        // If the email is unique, proceed with registration

        // Prepare SQL
        $sql = "INSERT INTO userinfo (username, email, password, birthday) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password, $birthday);

        if ($stmt->execute()) {
            echo "<script>alert('Your profile is created! Please login.'); window.location.href = 'login_register.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
