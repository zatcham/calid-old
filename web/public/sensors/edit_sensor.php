<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Sensor.php';
require $document_root . '\newdir\include\classes\Auth.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// check session exists
session_start();
//if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
//    header("location: auth/login.php");
//    exit;
//}

// twig init
$loader = new FilesystemLoader('../../templates');
$twig = new Environment($loader);
$twig->addGlobal('session', $_SESSION);
$twig->addGlobal('file_path', $directory_path);

// varaibles used for functs
$userid = $_SESSION["id"];
$username = $_SESSION["username"];
// false means data, true means none
$no_data = False;
$errors = "";
$sensor_name = $sensor_id = $sensor_type = $sensor_location = "";
$data_types = $last_seen = $api_key = $sw_version = $show_on_avg = $status = "";

if (!$_GET) {
    $no_sensor = True;
} else {
    $no_sensor = False;
    $sensor_id = $_GET['sensor'];
    if (Sensor::doesSensorBelongTo($sensor_id, $userid) == "Yes") {
        $sensor_name = Sensor::getSensorName($sensor_id);
        $sensor_type = Sensor::getSensorType($sensor_id);
        $sensor_location = Sensor::getSensorLocation($sensor_id);
        $data_types = Sensor::getSensorDataTypes($sensor_id);
        $last_seen = Sensor::getSensorLastSeen($sensor_id);
        $api_key = Sensor::getSensorAPIKey($sensor_id);
        $sw_version = Sensor::getSensorSWVersion($sensor_id);
        $show_on_avg = Sensor::getSensorShowOnAvg($sensor_id);
        $status = Sensor::getSensorStatus($sensor_id);
    } else {
        $errors = "Error: The sensor specified does not belong to this user";
    }

}

// render page from template
try {
    echo $twig->render('sensor_edit.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Sensor',
            'page_subtitle' => 'Edit sensor',
            'user_isadmin' => Auth::isUserAdmin($userid), // TODO : user id stuff
            'current_user' => $username,
            'no_data' => $no_data,
            'errors' => $errors,
            'no_sensor' => $no_sensor,
            // used if correct sensor submitted
            'sensor_name' => $sensor_name,
            'sensor_id' => $sensor_id,
            'sensor_type' => $sensor_type,
            'sensor_location' => $sensor_location,
            'data_types' => $data_types,
            'last_seen' => $last_seen,
            'api_key' => $api_key,
            'sw_ver' => $sw_version,
            'show_avg' => $show_on_avg,
            'status' => $status,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
