<?php

require_once __DIR__  . '/../../vendor/autoload.php';
require_once __DIR__  . '/../../include/classes/Database.php';
require_once __DIR__  . '/../../include/classes/Sensor.php';
require_once __DIR__  . '/../../include/classes/Auth.php';
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

// varaibles for page
$data_types = Sensor::getListOfDataTypes(); // TODO : Convert to check boxes
$form_success = $form_error = "";

// when form submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) { // check if form has been submitted
        // dodgy code to work out data types selected - TODO: Tidy up and make dynamic
        if (!isset($_POST['dataType1'])) { // sets data type vars based of checkboxes to either 1 or 0
            $dataType1 = 0;
        } else {
            $dataType1 = 1;
        }
        if (!isset($_POST['dataType2'])) {
            $dataType2 = 0;
        } else {
            $dataType2 = 1;
        }
        if (!isset($_POST['dataType3'])) {
            $dataType3 = 0;
        } else {
            $dataType3 = 1;
        }
        $data_type = Sensor::getOverallDataType($dataType1, $dataType2, $dataType3); // gets overall type based off checkboxes
        // eror checking
         if ($data_type == 0) {
             $form_error = "You must select at least 1 data type!";
        } elseif (empty($_POST['sensor_name'])) {
            $form_error = "You must enter a sensor name!";
        } elseif (empty($_POST['sensor_location'])) {
            $form_error = "You must enter a sensor location!";
        }
        if (!isset($_POST['show_on_avg'])) {
            $show_on_avg = 0;
        } else {
            $show_on_avg = 1;
        }
        if ($form_error == "") {
            $x = Sensor::addNewSensor($userid, $_POST['sensor_name'], $_POST['sensor_location'], $data_type, $show_on_avg);
            if ($x !== False) {
                $form_success = "New sensor added successfully.. redirecting you";
                Logging::log("info", "New sensor created successfully in sensors/new_sensor. Details: User ID: $userid, Sensor ID: $x");
                header("refresh:3 url=edit_sensor.php?sensor=$x&page=new_sensor"); // waits 3s before redirect
            } else {
                $form_error = "Error encountered whilst trying to create a new sensor.";
                Logging::log("error", "Problem in sensors/new_sensor. Type: Error whilst creating new sensor. Details: User ID: $userid");
            }
        }
    }
}

// render page from template
try {
    echo $twig->render('sensor_new.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Sensor',
            'page_subtitle' => 'Add new sensor',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'data_types' => $data_types,
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
