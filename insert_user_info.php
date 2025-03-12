<?php
include('db_connect.php');


// SQL query to drop the 'userinfo' table if it exists
$dropTableSQL = "DROP TABLE IF EXISTS userinfo";

// Execute the query to drop the table
if ($conn->query($dropTableSQL) === TRUE) {
    echo "Table 'userinfo' dropped successfully.<br>";
} else {
    echo "Error dropping table 'userinfo': " . $conn->error . "<br>";
}


// Create the userinfo table if it doesn't exist
$tableCreationQuery = "
CREATE TABLE IF NOT EXISTS userinfo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";
if ($conn->query($tableCreationQuery) === TRUE) {
    echo "Table userinfo created successfully or already exists.<br>";
} else {
    die("Error creating table: " . $conn->error);
}

// Insert default admin credentials if not already present
$adminEmail = "admin@gmail.com";
$adminPassword = "admin1234";
$checkAdminQuery = "SELECT * FROM userinfo WHERE email = '$adminEmail'";
$checkAdminResult = $conn->query($checkAdminQuery);

if ($checkAdminResult->num_rows == 0) {
    $insertAdminQuery = "INSERT INTO userinfo (username, email, password) VALUES ('admin', '$adminEmail', '$adminPassword')";
    if ($conn->query($insertAdminQuery) === TRUE) {
        echo "Admin credentials inserted successfully.<br>";
    } else {
        die("Error inserting admin credentials: " . $conn->error);
    }
} else {
    echo "Admin credentials already exist.<br>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["registerUsername"];
    $email = $_POST["registerEmail"];
    $password = $_POST["registerPassword"]; // In real-world, hash this password
    $repeatPassword = $_POST["registerRepeatPassword"];

    // Validate email domain
    if (!preg_match("/@(gmail\.com|yahoo\.com)$/", $email)) {
        echo "<script>
                alert('Email must be a valid gmail.com or yahoo.com address.');
                window.location.href = 'login_register.php';
              </script>";
        exit();
    }

    // Validate password length
    if (strlen($password) < 8) {
        echo "<script>
                alert('Password must be at least 8 characters long.');
                window.location.href = 'login_register.php';
              </script>";
        exit();
    }

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
        $sql = "INSERT INTO userinfo (username, email, password) VALUES ('$username', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Your profile is created! Please login.');</script>";
            header("Location: login_register.php"); // Redirect to login page after successful registration
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert User Info</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Register</h2>
        <form method="post" action="">
            <!-- Username input -->
            <div class="form-group">
                <label for="registerUsername">Username</label>
                <input type="text" id="registerUsername" name="registerUsername" class="form-control" required />
            </div>

            <!-- Email input -->
            <div class="form-group">
                <label for="registerEmail">Email</label>
                <input type="email" id="registerEmail" name="registerEmail" class="form-control" required />
            </div>

            <!-- Password input -->
            <div class="form-group">
                <label for="registerPassword">Password</label>
                <input type="password" id="registerPassword" name="registerPassword" class="form-control" required />
            </div>

            <!-- Repeat Password input -->
            <div class="form-group">
                <label for="registerRepeatPassword">Repeat password</label>
                <input type="password" id="registerRepeatPassword" name="registerRepeatPassword" class="form-control" required />
            </div>

            <!-- Submit button -->
            <button type="submit" class="btn btn-primary">Sign in</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
