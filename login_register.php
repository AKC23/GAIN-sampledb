<?php
include('db_connect.php');

$loginError = ""; // Initialize login error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['loginEmail']) && isset($_POST['loginPassword'])) {
        $email = $_POST["loginEmail"];
        $password = $_POST["loginPassword"];

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
            $sql = "SELECT * FROM userinfo WHERE email = ? AND password = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Redirect to user dashboard or desired page
                echo "<script>
                    alert('Admin Login successful! Moving to Admin view of the database.');
                    window.location.href = 'index_admin.php';
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
                    <form class="form-horizontal" action="register_user_info.php" method="POST" onsubmit="return validateRegisterForm()">
                        <fieldset>
                            <div id="legend">
                                <legend class="">Register</legend>
                            </div>
                            <div class="control-group">
                                <!-- Username -->
                                <label class="control-label" for="registerUsername">Username</label>
                                <div class="controls">
                                    <input type="text" id="registerUsername" name="registerUsername" placeholder="" class="input-xlarge form-control">
                                    <p class="help-block">Username can contain any letters or numbers, without spaces</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <!-- E-mail -->
                                <label class="control-label" for="registerEmail">E-mail</label>
                                <div class="controls">
                                    <input type="text" id="registerEmail" name="registerEmail" placeholder="" class="input-xlarge form-control">
                                    <p class="help-block">Please provide your E-mail</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <!-- Password-->
                                <label class="control-label" for="registerPassword">Password</label>
                                <div class="controls">
                                    <input type="password" id="registerPassword" name="registerPassword" placeholder="" class="input-xlarge form-control">
                                    <p class="help-block">Password should be at least 8 characters</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <!-- Password -->
                                <label class="control-label" for="registerRepeatPassword">Password (Confirm)</label>
                                <div class="controls">
                                    <input type="password" id="registerRepeatPassword" name="registerRepeatPassword" placeholder="" class="input-xlarge form-control">
                                    <p class="help-block">Please confirm password</p>
                                </div>
                            </div>

                            <div class="control-group">
                                <!-- Button -->
                                <div class="controls">
                                    <button class="btn btn-success">Register</button>
                                </div>
                            </div>
                        </fieldset>
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
    <script>
        function validateRegisterForm() {
            var email = document.getElementById("registerEmail").value;
            var password = document.getElementById("registerPassword").value;
            var repeatPassword = document.getElementById("registerRepeatPassword").value;

            // Validate email domain
            if (!email.match(/@(gmail\.com|yahoo\.com)$/)) {
                alert('Email must be a valid address (like gmail.com or yahoo.com).');
                return false;
            }

            // Validate password length
            if (password.length < 8) {
                alert('Password must be at least 8 characters long.');
                return false;
            }

            // Validate password match
            if (password !== repeatPassword) {
                alert('Passwords do not match.');
                return false;
            }

            return true;
        }
    </script>
</body>

</html>