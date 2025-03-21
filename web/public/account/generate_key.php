<?php

require_once __DIR__  . '/../../vendor/autoload.php'; // necesary classes imported
require_once __DIR__  . '/../../include/classes/Database.php';
require_once __DIR__  . '/../../include/classes/Sensor.php';
require_once __DIR__  . '/../../include/classes/Auth.php';
require_once __DIR__  . '/../../include/classes/Account.php';
require_once __DIR__  . '/../../include/classes/Logging.php';

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
$form_success = $form_error = $key = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['hide'])) {
        $key = Account::generateKey($userid); // TODO : turn to if
        $form_success = "Key generated successfully!"; // TODO: maybe show list of keys?
        Logging::log("info", "Key generated successfullyy in account/generate_key. Details: User ID: $userid");
    }
}

// render page from template
if (Auth::isUserAdmin($userid) == False) { // can only access as am admin
    // not allowed here - show error
    header("Location: ../error/403.html");
} else {
    try {
        echo $twig->render('account_generatekey.html.twig',
            ['server_name' => $server_name,
                'page_title' => 'Account',
                'page_subtitle' => 'Create sign-up key',
                'user_isadmin' => Auth::isUserAdmin($userid),
                'current_user' => $username,
                'form_success' => $form_success,
                'form_error' => $form_error,
                'key' => $key,
            ]);
    } catch (\Twig\Error\LoaderError $e) {
        echo ("Error loading page : Twig loader error");
    } catch (\Twig\Error\RuntimeError $e) {
        echo ("Error loading page : Twig runtime error");
    } catch (\Twig\Error\SyntaxError $e) {
        echo ("Error loading page : Twig syntax error");
    }
}
?>