<?php

require_once __DIR__  . '\..\..\vendor\autoload.php';
require_once __DIR__  . '\..\..\include\classes\Database.php';
require_once __DIR__  . '\..\..\include\classes\Auth.php';
require_once __DIR__  . '\..\..\include\classes\Account.php';
require_once __DIR__  . '\..\..\include\classes\Logging.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// check session exists
session_start();
// twig init
$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);
//$twig->addGlobal('session', $_SESSION);

$errors = $success = "";

if ($_GET) {
    if (!empty($_GET['key'])) { // validation
        if (Account::checkVerificationCode($_GET['key'])) { // cehcks verif. code
            if (Account::verifyUser($_GET['key'])) { // updates db
                if (Account::useVerificationCode($_GET['key'])) {
                    $success = "Account verified successfully!";
                } else {
                    $errors = "Error: An unexpected error occured.";
                    Logging::log("error", "Error occured in auth/verify_email. Type: Using verificaion code");
                }
            } else {
                $errors = "Error: An unexpected error occured.";
                Logging::log("error", "Error occured in auth/verify_email. Type: Verifying user");
            }
        } else {
            $errors = "The verification code used is invalid.";
        }
    } else {
        $errors = "Error: The URL used is invalid.";
    }
} else {
    $errors = "Error: The URL used is invalid.";
}

// render page from template
try {
    echo $twig->render('auth/verify_email.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Email Verification',
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
