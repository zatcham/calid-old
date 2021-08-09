<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Auth.php';
require $document_root . '\newdir\include\classes\Account.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// check session exists
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../error/403.html");
    exit;
}

// twig init
$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);
$twig->addGlobal('session', $_SESSION);
$twig->addGlobal('file_path', $directory_path);

// varaibles used for functs
$userid = $_SESSION["id"];
$username = $_SESSION["username"];
$user_roles = Account::getListOfUserRoles();
$form_error = $form_success = "";

// form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        if (empty($_POST['uname'])) {
            $form_error = "Error: you must enter a username.";
        }
        if (empty($_POST['inp-email'])) {
            $form_error = "Error: you must enter an email address.";
        }
        if (empty($_POST['inp-cpassword'])) {
            $form_error = "Error: you must enter a password.";
        }
        if (empty($_POST['inp-cnpassword'])) {
            $form_error = "Error: you must confirm the password.";
        }
        if ($_POST['inp-cpassword'] != $_POST['inp-cnpassword']) {
            $form_error = "Error: the passwords entered must match.";
        }
        if (strlen($_POST['inp-cpassword']) < 8) {
            $form_error = "Error: the passwords entered must be a minimum of 8 characters.";
        }
        if ($form_error == "") { // only continue if no errors
            if ($x = Account::createNewUser($_POST['uname'], $_POST['inp-email'], $_POST['inp-cpassword'], $_POST['role-select'])) {
                if ($x != False) {
                    $form_success = "User created successfully... redirecting you";
                    header("refresh:3 url=edit_user.php?user=$x"); // waits 3s before redirect
                } else {
                    $form_error = "An unexpected error occured whilst trying to create the user";
                }
            } else {
                $form_error = "An unexpected error occured whilst trying to create the user";
            }
        }
    }
}

// render page from template
try {
    echo $twig->render('account_adduser.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Account',
            'page_subtitle' => 'Add new user',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'form_error' => $form_error,
            'form_success' => $form_success,
            'user_roles' => $user_roles,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
