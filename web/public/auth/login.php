<?php
session_start();
$document_root = $_SERVER['DOCUMENT_ROOT'];
require $document_root . '\vendor\autoload.php';
//echo ($document_root . '\include\classes\Database.php');
require $document_root . '\include\classes\Database.php';
require $document_root . '\include\classes\Sensor.php';
require $document_root . '\include\classes\Auth.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use UAParser\Parser;

// twig init
$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);
//$twig->addGlobal('session', $_SESSION);
$username = $password = "";
$username_error = $password_error = $login_error = "";
$login_success = "";

// gets page to redirect to, if none go to dashboard
if (isset($_GET)) {
    if (!empty($_GET)) {
        if (isset($_GET['msg'])) {
            if (!empty(trim($_GET['msg']))) {
                if (trim($_GET['msg']) == "autologout") {
                    $login_success = "You were logged out due to inactivity.";
                } elseif (trim($_GET['msg']) == "logout") {
                    $login_success = "You were logged out successfully!";
                }
            }
        }
    } else {
        $page_redirect = "";
    }
} else {
    $page_redirect = "";
}

// if form submitted, run this code
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        // cehcks if username is empty
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

        // if both error felids are empty, must be ok
        if (empty($username_err) && empty($password_err)) {
            $dbconn = Database::Connect();
            $sql = "SELECT id, username, password FROM users WHERE username = ?"; // prepared query to stop sql injection
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
                                $parser = Parser::create();
                                $user_agent = $parser->parse($_SERVER['HTTP_USER_AGENT'])->toString(); // TODO: error handling if null
                                Auth::addLoginAttempt($id, ($_SERVER['REMOTE_ADDR']), "Success", $user_agent); // adds login attempt to db
                                $login_success = "Login success, redirecting...";
                                $stmt->close();
                                // Redirect user to specified page, or dashboard
                                if ($page_redirect == "") {
                                    header("location: ../home.php");
                                } else { // no page, go to dashboard
                                    header("location: ../$page_redirect");
                                }

                            } else {
                                // Password is incorrect
                                $login_error = "Invalid username or password.";
                                $parser = Parser::create();
                                $user_agent = $parser->parse($_SERVER['HTTP_USER_AGENT'])->toString(); // TODO: error handling if null
                                Auth::addLoginAttempt($id, ($_SERVER['REMOTE_ADDR']), "Fail", $user_agent);
                            }
                        }
                    } else {
                        // Username doesn't exist
                        $login_error = "Invalid username or password."; // we cant log this as no usr name | well we could i guess....
                    }
                } else {
                    $login_error = "Oops! Something went wrong. Please try again later.";
                }
            }
        }
    }
}

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
