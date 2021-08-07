<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];
require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Sensor.php';
require $document_root . '\newdir\include\classes\Auth.php';
require $document_root . '\newdir\include\classes\Graph.php';

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
$twig->addGlobal('file_path', $directory_path);

// varaibles used for functs
$userid = $_SESSION["id"];
$username = $_SESSION["username"];

// render page from template
try {
    echo $twig->render('home.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Dashboard',
            'page_subtitle' => '',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'sensors_assigned' => Sensor::getAssignedSensorCount($userid), // gets metrics
            'sensors_online' => Sensor::getOnlineSensorCount($userid),
            'sensors_offline' => Sensor::getOfflineSensorCount($userid),
            'sensors_alerts' => Sensor::getAlertedSensorCount($userid),
            'hum_graph' => Graph::getAvgHumGraph($userid), // gets average graphs
            'temp_graph' => Graph::getAvgTempGraph($userid),
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
