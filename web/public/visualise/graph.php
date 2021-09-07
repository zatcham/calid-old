<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require_once $document_root . '\vendor\autoload.php';
require_once $document_root . '\include\classes\Database.php';
require_once $document_root . '\include\classes\Sensor.php';
require_once $document_root . '\include\classes\Auth.php';
require_once $document_root . '\include\classes\Logging.php';
require_once $document_root . '\include\classes\Graph.php';

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
$data_types = Sensor::getListOfDataTypes();
$json = $value_col = $dt_json = "";
$chart_title = $y_axis = "";

// get data for dropdown selector
$dbconn = Database::Connect();
$sqlq = "SELECT `SensorID`, `SensorName` FROM sensor_details WHERE `UserID`=$userid;"; // should be no risk of sql injection
$stmt = $dbconn->prepare($sqlq);
if ($stmt == False) {
    Logging::log("error", "Error whilst attempting to prepare query in visualise/graph");
    $errors = "Error encountered whilst trying to query database";
}
$stmt->execute();
if ($stmt == False) {
    Logging::log("error", "Error whilst attempting to execute query in visualise/graph");
    $errors = "Error encountered whilst trying to query database";
}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
if ($data) { // should mean some data exists
    $no_data = False;
    $select_data = $data;
} else { // no data or error
    $no_data = True;
    $select_data = "none";
    Logging::log("warning", "Either no data found or an error occured whilst getting list of sensors in visualise/graph");
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
                $t_data = $temp_table = Graph::getTemperatureGraph($_POST['sensor'], $_POST['start'], $_POST['end']);
                $t_data = array_map(function($t) {
                    return array(
                        $t['Date/Time'],
                        $t['ROUND(`Temperature`)'],
                    );
                }, $t_data);
//                echo ('<pre>');
//                print_r ($t_data);
//                echo ('<post>');
//                $json = json_encode($t_data);
                $json = json_encode(array_reverse(array_column($t_data, '1')), JSON_NUMERIC_CHECK);
                $dt_json = json_encode(array_reverse(array_column($t_data, '0')), JSON_NUMERIC_CHECK);
                $value_col = "Temperature (°C)"; // TODO : Add conversion
                $chart_title = "Temperature data for " . Sensor::getSensorName($selected_sensor);
                $y_axis = "Temperature (°C)";
            } elseif ($selected_datatype == 2) {
                $t_data = $temp_table = Graph::getHumidityGraph($_POST['sensor'], $_POST['start'], $_POST['end']);
                $t_data = array_map(function($t) {
                    return array(
                        $t['Date/Time'],
                        $t['ROUND(`Humidity`)'],
                    );
                }, $t_data);
                $json = json_encode(array_reverse(array_column($t_data, '1')), JSON_NUMERIC_CHECK);
                $dt_json = json_encode(array_reverse(array_column($t_data, '0')), JSON_NUMERIC_CHECK);
                $chart_title = "Humidity data for " . Sensor::getSensorName($selected_sensor);
                $y_axis = "Humidity (%)";
            } elseif ($selected_datatype == 3) {
                $t_data = $temp_table = Graph::getUVGraph($_POST['sensor'], $_POST['start'], $_POST['end']);
                $t_data = array_map(function($t) {
                    return array(
                        $t['Date/Time'],
                        $t['ROUND(`UV`)'],
                    );
                }, $t_data);
                $json = json_encode(array_reverse(array_column($t_data, '1')), JSON_NUMERIC_CHECK);
                $dt_json = json_encode(array_reverse(array_column($t_data, '0')), JSON_NUMERIC_CHECK);
                $chart_title = "UV data for " . Sensor::getSensorName($selected_sensor);
                $y_axis = "UV (Index)";
            }

            if ($t_data) {
                $t_data_show = "1";
            } else {
                $t_data_show = "0";
            }
        }
    }
}

// render page from template
try {
    echo $twig->render('visualise_graph.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Visualise',
            'page_subtitle' => 'Graphing',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'errors' => $errors,
            'sensors' => $select_data,
            'selected_sensor' => $selected_sensor,
            't_data' => $t_data,
            'g_data_show' => $t_data_show,
            'data_types' => $data_types,
            'json' => $json,
            'selected_datatype' => $selected_datatype,
            'value_col' => $value_col,
            'dt' => $dt_json,
            'chart_title' => $chart_title,
            'y_axis' => $y_axis,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
