<?php

require_once __DIR__  . '/../../vendor/autoload.php';
require_once __DIR__  . '/../../include/classes/Database.php';
require_once __DIR__  . '/../../include/classes/Table.php';
require_once __DIR__  . '/../../include/classes/Auth.php';
require_once __DIR__  . '/../../include/classes/Sensor.php';
require_once __DIR__  . '/../../include/variables.php';

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
$errors = "";
$selected_sensor = $selected_datatype = "";
$t_data = [];
$t_data_show = "0"; // issue with boolean, use int instead
$data_types = Sensor::getListOfDataTypes(); // todo: error handling
$json = $value_col = "";

// get data for dropdown selector
$dbconn = Database::Connect();
$sqlq = "SELECT `SensorID`, `SensorName` FROM sensor_details WHERE `UserID`=$userid;"; // should be no risk of sql injection
$stmt = $dbconn->prepare($sqlq);
if ($stmt == False) {
    $errors = "Error encountered whilst trying to query database";
}
$stmt->execute();
if ($stmt == False) {
    $errors = "Error encountered whilst trying to query database";
}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
if ($data) { // should mean some data exists
    $no_data = False;
    $select_data = $data;
//    print_r($data);
//    echo '<pre>'; print_r($data); echo '</pre>';
} else { // no data or error
    $no_data = True;
    $select_data = "none";
}

// when selected
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // TODO: Check sensor belongs to user
    // TODO: Allow multiple selection
    if (!empty($_POST)) {
        if (empty($_POST['start'])) {
            $errors = "You must select a start date for the query!";
        }
        if (empty($_POST['end'])) {
            $errors = "You must select a end date for the query!";
        }
        if (empty($_POST['sensor'])) {
            $errors = "You must select a sensor!";
        }
        if (empty($_POST['data-select'])) {
            $errors = "You must select a data type!";
        }
        // Add select for data type
        if ($errors == "") {
            $selected_sensor = $_POST['sensor'];
            $selected_datatype = $_POST['data-select'];
            // TODO: Add data types selection
            if ($selected_datatype == 1) { // This should be dynamic 🤷‍♀
                $t_data = $temp_table = Table::getTemperatureTable($_POST['sensor'], $_POST['start'], $_POST['end']);
                $t_data = array_map(function($t) {
                    return array(
                        $t['SensorName'],
                        $t['Date/Time'],
                        $t['ROUND(`Temperature`)'],
                    );
                }, $t_data);
                $json = json_encode($t_data);
                $value_col = "Temperature (°C)"; // TODO : Add conversion
            } elseif ($selected_datatype == 2) {
                $t_data = $temp_table = Table::getHumidityTable($_POST['sensor'], $_POST['start'], $_POST['end']);
                $t_data = array_map(function($t) {
                    return array(
                        $t['SensorName'],
                        $t['Date/Time'],
                        $t['ROUND(`Humidity`)'],
                    );
                }, $t_data);
                $json = json_encode($t_data);
                $value_col = "Humidity (%)";
            } elseif ($selected_datatype == 3) {
                $t_data = $temp_table = Table::getUVTable($_POST['sensor'], $_POST['start'], $_POST['end']);
                $t_data = array_map(function($t) {
                    return array(
                        $t['SensorName'],
                        $t['Date/Time'],
                        $t['ROUND(`UV`)'],
                    );
                }, $t_data);
                $json = json_encode($t_data);
                $value_col = "UV (Index)";
            }

            if ($t_data) {
                $t_data_show = "1";
            } else {
                $t_data_show = "0";
            }
        }
    }
}

// to implement get later - will allow for easy access
//if (!$_GET) {
//    // do nothing
//} else {
//    if (!empty($_GET['sensor'])) {
//
//    }
//}

// render page from template
try {
    echo $twig->render('visualise_table.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Visualise',
            'page_subtitle' => 'Data tables',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'errors' => $errors,
            'sensors' => $select_data,
            'selected_sensor' => $selected_sensor,
            't_data' => $t_data,
            't_data_show' => $t_data_show,
            'data_types' => $data_types,
            'json' => $json,
            'selected_datatype' => $selected_datatype,
            'value_col' => $value_col,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error " . $e);
}

?>
