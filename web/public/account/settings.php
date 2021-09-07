<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require_once $document_root . '\vendor\autoload.php';
require_once $document_root . '\include\classes\Database.php';
require_once $document_root . '\include\classes\Auth.php';
require_once $document_root . '\include\classes\Account.php';
require_once $document_root . '\include\classes\Logging.php';

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
$pw_change_date = Account::getLastPWChangeDate($userid); // runs functions to get data to dusplay
$create_date = Account::getDateCreated($userid);
$user_email = Account::getEmailAddress($userid);
$form_error = $form_success = "";
$user_role = Account::getUserRoleName(Account::getUserRole($userid)); // replaced old method, now is dynamic from db

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        if ($_POST['inp-email'] != $user_email) { // if input is not equal to current, then change
            if (Account::checkPassword($userid, $_POST['inp-cpassword'])) { // is password valid
                if (Account::changeEmail($userid, $_POST['inp-email'])) { // run change email funct
                    $form_success = "Account updated successfully! Please refresh this page for the changes to appear.";
                    Logging::log("info", "Account updated successfully in account/settings. Details: User ID: $userid");
                } else {
                    $form_error = "An unexpected error has occured. Please try again.";
                    Logging::log("error", "Problem occured in account/settings, Type: Issue changing email, Details: User ID: $userid");
                }
            } else {
                $form_error = "The password entered is incorrect.";
            }
        } else {
            $form_success = "No changes to make.";
        }
    }
}

// render page from template
try {
    echo $twig->render('account_settings.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Account',
            'page_subtitle' => 'Settings',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'create_date' => $create_date,
            'pw_change_date' => $pw_change_date,
            'user_role' => $user_role,
            'user_email' => $user_email,
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
