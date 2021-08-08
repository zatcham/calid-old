<?php
// TODO: this page
$document_root = $_SERVER['DOCUMENT_ROOT'];

require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Auth.php';
require $document_root . '\newdir\include\classes\Account.php';

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
                }
            } else {
                $errors = "Error: An unexpected error occured.";
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
