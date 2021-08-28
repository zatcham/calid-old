<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require $document_root . '\vendor\autoload.php';
require $document_root . '\include\classes\Database.php';
require $document_root . '\include\classes\Account.php';
require $document_root . '\include\classes\Auth.php';
require $document_root . '\include\classes\Email.php';

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
                } else {
                    $errors = "Error: An unexpected error occured";
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
