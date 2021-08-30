<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require_once $document_root . '\vendor\autoload.php';
require_once $document_root . '\include\classes\Database.php';
require_once $document_root . '\include\classes\Table.php';
require_once $document_root . '\include\classes\Auth.php';
require_once $document_root . '\include\classes\Sensor.php';

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
$selected_sensor = "";
$t_data = [];
$t_data_show = "0"; // issue with boolean, use int instead
$data_types = Sensor::getListOfDataTypes(); // todo: error handling

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
        // Add select for data type
        if ($errors == "") {
            $t_data = $temp_table = Table::getTemperatureTable($_POST['sensor'], $_POST['start'], $_POST['end']);
            if ($t_data) {
                $t_data_show = "1"; // TODO : seperate temp and hum
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
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error " . $e);
}

?>
