<?php

require_once __DIR__  . '/../../vendor/autoload.php';
require_once __DIR__  . '/../../include/classes/Database.php';
require_once __DIR__  . '/../../include/classes/Account.php';
require_once __DIR__  . '/../../include/classes/Auth.php';
require_once __DIR__  . '/../../include/classes/Email.php';
require_once __DIR__  . '/../../include/classes/Logging.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// check session exists
session_start();
// twig init
$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);
$errors = $success = "";

// if form submitted, run this code
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        if (empty(trim($_POST["username"]))) {
            $errors = "You must enter a username";
        } else { // validation met
            if (Account::doesUsernameExist($_POST["username"])) { // check if user exists
                if (Account::forgotPassword($_POST["username"])) {
                    $success = "If this user exists, a password reset email has been sent. Please check your junk folder if you haven't recieved it";
                    $usern = $_POST["username"];
                    Logging::log("info", "Forgot password email sent for user $usern");
                } else {
                    $errors = "Error: An unexpected error occured";
                    $usern = $_POST["username"];
                    Logging::log("error", "Error occured in auth/forgot_pass. Type: Forgot Password Function, Details: Username to reset: $usern");
                }
            } else {
                $success = "If this user exists, a password reset email has been sent. Please check your junk folder if you haven't recieved it";
            }
        }
    }
}

// render page from template
try {
    echo $twig->render('auth/forgot_pass.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Forgotten Password',
            'error' => $errors,
            'success' => $success,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
