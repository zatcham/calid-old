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
//$twig->addGlobal('session', $_SESSION);
$form_error = $form_success = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        if (empty($_POST['username'])) {
            $form_error = "Error: You must enter a username.";
        }
        if (empty($_POST['email'])) {
            $form_error = "Error: You must enter a email address.";
        }
        if (empty($_POST['password'])) {
            $form_error = "Error: You must enter a password.";
        }
        if (empty($_POST['confirm-password'])) {
            $form_error = "Error: You must confirm the password."; // TODO: wording?
        }
        if (empty($_POST['access-code'])) {
            $form_error = "Error: You must enter an access code.";
        }
        if ($_POST['password'] != $_POST['confirm-password']) {
            $form_error = "Error: Both passwords entered must match";
        }
        if (strlen($_POST['password']) < 7) {
            $form_error = "Error: Your password must be at least 8 characters";
        }
        if (!isset($_POST['tc-check'])) {
            $form_error = "Error: You must accept the T&Cs";
        }
        if (empty($form_error)) { // empty means no errors, proceed
            if (Auth::checkAccessKey($_POST['access-code'])) {
                // access code is ok, proceed
                if ($x = Account::createNewUser($_POST['username'], $_POST['email'], $_POST['password'], "3")) {
                    // account creation sucesfful, set access key to used
                    if (Auth::useAccessKey($_POST['access-code'])) {
                        // sucess
                        if (Account::sendVerifyEmail($x, $_POST['email'], $_POST['username'])) {
                            $form_success = "Account created successfully. Redirecting you to the login screen...";
                            $email = $_POST['email'];
                            $username = $_POST['username'];
                            Logging::log("info", "Account created successfully in auth/register. Details: User ID: $x, Username: $username, Email: $email");
                            header("refresh:3 url=login.php");

                        } else {
                            $form_error = "An unexpected error occured when sending the verification email.";
                            $email = $_POST['email'];
                            $username = $_POST['username'];
                            Logging::log("error", "Error occured in auth/register. Type: Verification email sending, Details: User ID: $x, Username: $username, Email: $email");
                        }
                    } else {
                        $form_error = "An unexpected error occured.";
                        $email = $_POST['email'];
                        $username = $_POST['username'];
                        Logging::log("error", "Error occured in auth/register. Type: Access key verification, Details: User ID: $x, Username: $username, Email: $email");
                    }
                } else {
                    $form_error = "An unexpected error occured whilst attempting to create your account.";
                    $email = $_POST['email'];
                    $username = $_POST['username'];
                    Logging::log("error", "Error occured in auth/register. Type: Account Creation, Details: Username: $username, Email: $email");
                }
            } else {
                $form_error = "Error: The access code entered is incorrect.";
            }
        }
    }
}


// render page from template
try {
    echo $twig->render('auth/register.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Register',
            'form_error' => $form_error,
            'form_success' => $form_success,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
