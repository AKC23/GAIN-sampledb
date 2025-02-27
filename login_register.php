<?php
include('db_connect.php');

$loginError = ""; // Initialize login error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['loginEmail']) && isset($_POST['loginPassword'])) {
        $email = $_POST["loginEmail"];
        $password = $_POST["loginPassword"];

        // Default admin credentials
        $adminEmail = "admin@gmail.com";
        $adminPassword = "admin1234";

        // Check if the entered credentials match the default admin credentials
        if ($email === $adminEmail && $password === $adminPassword) {
            // Redirect to admin dashboard or desired page
            echo "<script>
                    alert('Admin Login successful! Moving to Admin view of the database.');
                    window.location.href = 'index_admin.php';
                  </script>";
            exit();
        } else {
            // Check if the entered credentials match any user in the database
            $sql = "SELECT * FROM userinfo WHERE email = '$email' AND password = '$password'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Redirect to user dashboard or desired page
                echo "<script>
                        alert('User Login successful! Moving to User view of the database.');
                        window.location.href = 'index_user.php';
                      </script>";
                exit();
            } else {
                // Display an error message for incorrect credentials
                $loginError = "Incorrect email or password.";
                echo "<script>
                        alert('Email/Password is wrong. Please try again.');
                        window.location.href = 'login_register.php';
                      </script>";
                exit();
            }
        }
    } elseif (isset($_POST['registerName']) && isset($_POST['registerUsername']) && isset($_POST['registerEmail']) && isset($_POST['registerPassword']) && isset($_POST['registerRepeatPassword']) && isset($_POST['registerBirthday'])) {
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
                echo "<script>alert('Registration successful!');</script>";
                header("Location: login_register.php"); // Redirect to login page after successful registration
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
        }

        .center-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-register-card {
            width: 500px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 1px solid #c8e5bf;
            border-radius: 5px;
            padding: 20px;
            background-color: #fff;
        }
    </style>
</head>

<body>
    <div class="center-container">
        <div class="login-register-card">
            <!-- Pills navs -->
            <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="tab-login" data-mdb-pill-init href="#pills-login" role="tab" aria-controls="pills-login" aria-selected="true">Login</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="tab-register" data-mdb-pill-init href="#pills-register" role="tab" aria-controls="pills-register" aria-selected="false">Register</a>
                </li>
            </ul>
            <!-- Pills navs -->

            <!-- Pills content -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="pills-login" role="tabpanel" aria-labelledby="tab-login">
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="loginEmail">Email address</label>
                            <input type="text" class="form-control" id="loginEmail" name="loginEmail" aria-describedby="emailHelp" placeholder="Enter email">
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                        </div>
                        <div class="form-group">
                            <label for="loginPassword">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="loginPassword" placeholder="Password">
                        </div>
                        <?php if (!empty($loginError)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $loginError; ?>
                            </div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
                <div class="tab-pane fade" id="pills-register" role="tabpanel" aria-labelledby="tab-register">
                    <form method="post" action="">
                        <div class="text-center mb-3">
                            <p>Sign up with:</p>
                            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-facebook-f"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-google"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                                <i class="fab fa-twitter"></i>
                            </button>

                            <button type="button" data-mdb-button-init data-mdb-ripple-init class="btn btn-link btn-floating mx-1">
                                <i class "fab fa-github"></i>
                            </button>
                        </div>

                        <p class="text-center">or:</p>

                        <!-- Name input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="text" id="registerName" name="registerName" class="form-control" required />
                            <label class="form-label" for="registerName">Name</label>
                        </div>

                        <!-- Username input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="text" id="registerUsername" name="registerUsername" class="form-control" required />
                            <label class="form-label" for="registerUsername">Username</label>
                        </div>

                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="email" id="registerEmail" name="registerEmail" class="form-control" required />
                            <label class="form-label" for="registerEmail">Email</label>
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="password" id="registerPassword" name="registerPassword" class="form-control" required />
                            <label class="form-label" for="registerPassword">Password</label>
                        </div>

                        <!-- Repeat Password input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="password" id="registerRepeatPassword" name="registerRepeatPassword" class="form-control" required />
                            <label class="form-label" for="registerRepeatPassword">Repeat password</label>
                        </div>

                        <!-- Date of Birth input -->
                        <div data-mdb-input-init class="form-outline mb-4">
                            <input type="date" id="registerBirthday" name="registerBirthday" class="form-control" required />
                            <label class="form-label" for="registerBirthday">Date of Birth</label>
                        </div>

                        <!-- Checkbox -->
                        <div class="form-check d-flex justify-content-center mb-4">
                            <input class="form-check-input me-2" type="checkbox" value="" id="registerCheck" checked aria-describedby="registerCheckHelpText" />
                            <label class="form-check-label" for="registerCheck">
                                I have read and agree to the terms
                            </label>
                        </div>

                        <!-- Submit button -->
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-3">Sign in</button>
                    </form>
                </div>
            </div>
            <!-- Pills content -->
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- MDB -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.1.0/mdb.umd.min.js"></script>
</body>

</html>
