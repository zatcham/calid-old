<?php
// start session
session_start();

// If usr already logged in, redir to page | comment out for testing
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
//    header("location: welcome.php");
//    exit;
}

// Include db files
require("include/variables.php");
require("include/db_connect.php");
require("include/functions.php");

$username = $password = "";
$username_err = $password_err = $login_err = "";
$login_suc = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement | seeing as the input is user-facing, best to use prep. statements
        $sql = "SELECT id, username, password FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($dbconn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            // session_start();
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            addLoginAttempt($id, ($_SERVER['REMOTE_ADDR']), "Success");
                            $login_suc = "Login success, redirecting...";
                            // Redirect user to welcome page
                            header("location: home.php");
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                            addLoginAttempt($id, ($_SERVER['REMOTE_ADDR']), "Fail");
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password."; // we cant log this as no usr name | well we could i guess....
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($dbconn);

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="public/assets/favicons/favicon.ico">

    <title>Login - DEV1 - Sensor System</title> <!-- TODO : Dynamic Title -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link href="../css/login.css" rel="stylesheet">
</head>
<body class="text-center">
<div class="wrapper border rounded" style="background-color: #f5f5f5; padding: 40px;">
    <h2>Sensor Login</h2>
    <p>Please fill in your credentials to login.</p>

    <?php
    if (!empty($login_err)) {
        echo '<div class="alert alert-danger">' . $login_err . '</div>';
    }
    if (!empty($login_suc)) {
        echo '<div class="alert alert-success">' . $login_suc . '</div>';
    }
    ?>

    <form style="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username"
                   class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                   placeholder="Username" <?php echo $username; ?>" required autofocus>
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password"
                   class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                   placeholder="Password" required autofocus>
            <span class="invalid-feedback"><?php echo $password_err; ?></span>
        </div>
        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-lg btn-primary btn-block" value="Login">
            <p class="mt-5 mb-3 text-muted">&copy; 2021 Zach Matcham</p>
        </div>
    </form>
</div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
        crossorigin="anonymous"></script>

</html>