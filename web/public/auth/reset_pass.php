<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require_once $document_root . '\vendor\autoload.php';
require_once $document_root . '\include\classes\Database.php';
require_once $document_root . '\include\classes\Account.php';
require_once $document_root . '\include\classes\Auth.php';
require_once $document_root . '\include\classes\Logging.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// check session exists
session_start();
// twig init
$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);
$errors = $success = $no_get = "";

if (!$_GET) {
    $errors = "Error: Invalid request.";
    $no_get = True;
} else {
    if (!empty($_GET['key'])) {
        // now check if form submitted, cant submit w.out key
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!empty($_POST)) {
                // validation
                if (empty(trim($_POST['password']))) {
                    $errors = "You must enter a new password.";
                } else {
                    $password = trim($_POST['password']);
                }
                if (empty(trim($_POST['confirm_password']))) {
                    $errors = "You must confirm the new password.";
                } else {
                    $confirm_password = trim($_POST['confirm_password']);
                }
                if ($password != $confirm_password) {
                    $errors = "Both passwords must match";
                }
                if (strlen($password) <= 7) {
                    $errors = "The password must be at least 8 characters in length";
                }
                if (empty($errors)) { // no errors, continue
                    // check key
                    if ($x = Account::checkResetKey($_GET['key'])) {
                        // key is ok, now reset
                        if ($x != False) {
                            if (Account::resetPassword($x, $password)) {
                                Account::useResetKey($_GET['key']);
                                Logging::log("info", "Password reset successfully in auth/reset_pass. Details: User ID: $x");
                                $success = "Password reset successfully! Redirecting you to the login page...";
                                header("refresh:3 url=login.php");
                            } else {
                                $errors = "An unexpected error occured whilst attempting to reset the password";
                                Logging::log("error", "Error occured in auth/reset_pass. Type: Resetting password, Details: User ID: $x");
                            }
                        }
                    } else {
                        $errors = "Error: the reset key specified is invalid.";
                    }
                }
            } else {
                $errors = "Error: Invalid POST request.";
                Logging::log("error", "Error occured in auth/reset_pass. Type: Invalid POST request");
            }
        }
    } else {
        $errors = "Error: Invalid GET request.";
        $no_get = True;
        Logging::log("error", "Error occured in auth/reset_pass. Type: Invalid GET request");
    }
}


// render page from template
try {
    echo $twig->render('auth/reset_pass.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Reset Password',
            'error' => $errors,
            'success' => $success,
            'no_get' => $no_get,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
