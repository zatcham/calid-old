<?php
$doc_root = $_SERVER['DOCUMENT_ROOT'];
require '..\vendor\autoload.php';
require '..\include\classes\Database.php';
require '..\include\classes\Sensor.php';
require '..\include\classes\Auth.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// check session exists
session_start();
//if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
//    header("location: auth/login.php");
//    exit;
//}

// twig init
$loader = new FilesystemLoader('../templates');
$twig = new Environment($loader);
$twig->addGlobal('session', $_SESSION);

// varaibles used for functs
$userid = $_SESSION["id"];
$username = $_SESSION["username"];

// render page from template
try {
    echo $twig->render('home.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Test',
            'page_subtitle' => 'Sub test',
            'user_isadmin' => Auth::isUserAdmin($userid), // TODO : user id stuff
            'current_user' => $username,
            'sensors_assigned' => Sensor::getAssignedSensorCount($userid),
            'sensors_online' => Sensor::getOnlineSensorCount($userid),
            'sensors_offline' => Sensor::getOfflineSensorCount($userid),
            'sensors_alerts' => Sensor::getAlertedSensorCount($userid)
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
