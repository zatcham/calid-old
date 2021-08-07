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
$no_data = False; // false means data, true means none
$errors = $form_success = "";
$form_success_list = [];
$sensor_name = $sensor_id = $sensor_type = $sensor_location = "";
$data_types = $last_seen = $api_key = $sw_version = $show_on_avg = $status = $current_data_type = "";
$from_page = "";

if (!$_GET) {
    $no_sensor = True;
    $errors = "Error: A sensor has not been selected. Please return to the sensor list and try again.";
} else {
    if (!empty($_GET['page'])) { // used to set go back button desitnation
        $x = $_GET['page'];
        if ($x == "new_sensor") {
            $from_page = "new_sensor";
        } elseif ($x == "list_sensors") {
            $from_page = "list_sensors";
        }
    }
    if (!empty($_GET['sensor'])) {
        $no_sensor = False;
        $sensor_id = $_GET['sensor'];
        if (Sensor::doesSensorBelongTo($sensor_id, $userid) == "Yes") { // if sensor belongs to user, do below
            // gets all data for sensors from functs
            $sensor_name = Sensor::getSensorName($sensor_id);
            $sensor_type = Sensor::getSensorType($sensor_id);
            $sensor_location = Sensor::getSensorLocation($sensor_id);
            $current_data_type = Sensor::getSensorDataTypes($sensor_id);
            $last_seen = Sensor::getSensorLastSeen($sensor_id);
            $api_key = Sensor::getSensorAPIKey($sensor_id);
            $sw_version = Sensor::getSensorSWVersion($sensor_id);
            $show_on_avg = Sensor::getSensorShowOnAvg($sensor_id);
            $status_id = Sensor::getSensorStatus($sensor_id);
            $status = Sensor::getStatusName($status_id); // We need to convert the status from its id to its name and im a bit stupid
            $data_types = Sensor::getListOfDataTypes(); // TODO : Convert to check boxes
        } else {
            $errors = "Error: The sensor specified does not belong to this user";
        }
    } else {
        $errors = "Error: A sensor has not been selected. Please return to the sensor list and try again.";
        $no_sensor = True;
    }
}

// when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//    print_r($_POST);
//    echo (Sensor::getSensorShowOnAvg($sensor_id));
    if (!empty($_POST)) {
        $form_success_list = array(); // array used to show multiple items
//        $sensor_id = $_POST['sensor_id'];
        if (Sensor::doesSensorBelongTo($sensor_id, $userid) == "Yes") { // only allow submission is sensor id is still valid
            $count = 0; // checks if any items have been updates or not
            if ($_POST['sensor_name'] != $sensor_name) {
                if (Sensor::changeSensorName($sensor_id, $_POST['sensor_name'])) {
                    $form_success_list[] = "Sensor Name";
                    $count += 1;
                } else {
                    $errors = "Error encountered whilst changing 'Sensor Name'. Please try again";
                }
            }
            if ($_POST['sensor_location'] != $sensor_location) {
                if (Sensor::changeSensorLocation($sensor_id, $_POST['sensor_location'])) {
                    $form_success_list[] = "Sensor Location";
                    $count += 1;
                } else {
                    $errors = "Error encountered whilst changing 'Sensor Location'. Please try again";
                }
            }
            if ($_POST['data-select'] != $current_data_type) {
                if (Sensor::changeSensorDataTypes($sensor_id, $_POST['data-select'])) {
                    $form_success_list[] = "Data Types";
                    $count += 1;
                } else {
                    $errors = "Error encountered whilst changing 'Data Types'. Please try again";
                }
            }
            if (isset($_POST['show_on_avg'])) {
                if ($_POST['show_on_avg'] != $show_on_avg) {
                    if (Sensor::changeSensorAverage($sensor_id, "1")) {
                        $form_success_list[] = "Show On Average";
                        $count += 1;
                    } else {
                        $errors = "Error encountered whilst changing 'Show On Average'. Please try again";
                    }
                }
            } else {
                $avg_box = (isset($_POST['show_on_avg'])) ? 1 : 0; // checkbox - doesnt report a value if not ticked
                if (Sensor::changeSensorAverage($sensor_id, "0")) {
                    $count += 1;
                } else {
                    $errors = "Error encountered whilst changing 'Show On Average'. Please try again";
                }
            }
            if ($count > 0) {
                $form_success = "The following options were updated successfully: ";
            }
        } else {
            $errors = "Error: The sensor specified does not belong to this user";
        }
    }
}

// render page from template
try {
    echo $twig->render('sensor_edit.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Sensor',
            'page_subtitle' => 'Edit sensor',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'no_data' => $no_data,
            'errors' => $errors,
            'no_sensor' => $no_sensor,
            // used if correct sensor submitted, blank vairalbe otherwise
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
            'current_data_type' => $current_data_type,
            'form_success' => $form_success,
            'form_success_list' => $form_success_list,
            'from_page' => $from_page,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
