<?php
session_start();
$document_root = $_SERVER['DOCUMENT_ROOT'];
require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Sensor.php';
require $document_root . '\newdir\include\classes\Auth.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

//

// twig init
$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);
//$twig->addGlobal('session', $_SESSION);
$username = $password = "";
$username_error = $password_error = $login_error = "";
$login_success = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        if (empty(trim($_POST["username"]))) {
            $username_error = "Please enter a username.";
        } else {
            $username = trim($_POST["username"]);
        }

        // Check if password is empty
        if (empty(trim($_POST["password"]))) {
            $password_error = "Please enter your password.";
        } else {
            $password = trim($_POST["password"]);
        }

        if (empty($username_err) && empty($password_err)) {
            $dbconn = Database::Connect();
            $sql = "SELECT id, username, password FROM users WHERE username = ?";
            if ($stmt = $dbconn->prepare($sql)) {
                $stmt->bind_param("s", $param_username);
                // Set parameters
                $param_username = $username;
                if ($stmt->execute()) {
                    $stmt->store_result();
                    if ($stmt->num_rows == 1) { // username exists
                        $stmt->bind_result( $id, $username, $hashed_password); // bind sql result to vars
                        if ($stmt->fetch()) {
                            if (password_verify($password, $hashed_password)) {
                                $_SESSION["loggedin"] = true; // password is correct
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;
                                Auth::addLoginAttempt($id, ($_SERVER['REMOTE_ADDR']), "Success");
                                $login_success = "Login success, redirecting...";
                                $stmt->close();
                                // Redirect user to welcome page
                                header("location: home.php");
                            } else {
                                // Password is incorrect
                                $login_error = "Invalid username or password.";
                                Auth::addLoginAttempt($id, ($_SERVER['REMOTE_ADDR']), "Fail");
                                echo ("Error 1");
                            }
                        }
                    } else {
                        // Username doesn't exist
                        $login_error = "Invalid username or password."; // we cant log this as no usr name | well we could i guess....
                        echo ("Error 2");
                    }
                } else {
                    $login_error = "Oops! Something went wrong. Please try again later.";
                    echo ("Error 3");
                }
            }
        }
    }
}


// varaibles used for functs

// render page from template
try {
    echo $twig->render('auth/login.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Login',
            'login_error' => $login_error,
            'login_success' => $login_success,
            'username_error' => $username_error,
            'password_error' => $password_error
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
