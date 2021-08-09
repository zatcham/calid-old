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


// get data
$dbconn = Database::Connect();
$sqlq = "SELECT `SensorID`,`SensorName`, `SensorType`, `SensorLoc`, `DataTypes`, `LastSeen`, `APIKey`, `SWVersion`, `show_on_avg`, `Status` FROM sensor_details WHERE `UserID`=$userid"; // TODO : no prepared statment ....
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
    foreach($data as $key => $value) { // Replaces the Status and type IDs with their names
        // data types
        $type = (intval($data[$key]['DataTypes']) - 1); // array starts at 0, whilst sql starts at 1
        $types = Sensor::getListOfDataTypes();
        $type_as_str = ($types[$type]['Name']); // if you get errors from here, its a db issue.
        $data[$key]['DataTypes'] = $type_as_str;
        // status types
        $status = (intval($data[$key]['Status']) - 1);
        $statuses = Sensor::getListOfStatusTypes();
        $status_as_str = ($statuses[$status]['Name']);
        $data[$key]['Status'] = $status_as_str;
    }
    $table_data = $data;

    // sort the data
    if (!empty($_GET)) {
        if (!empty($_GET['view'])) {
            if ($_GET['view'] == "offline") {
                $sorting_js = "data_table.columns(8).search('Offline', true, false).draw();";
            } elseif ($_GET['view'] == "online") {
                $sorting_js = "data_table.columns(8).search('Online', true, false).draw();";
            } elseif ($_GET['view'] == "assigned") {
                $sorting_js = "data_table.columns(8).search('', true, false).draw();";
            } elseif ($_GET['view'] == "alerts") {
                $sorting_js = "data_table.columns(8).search('Alert', true, false).draw();";
            } else {
                $sorting_js = "data_table.columns(8).search('', true, false).draw();"; // default
            }
        } else {
            $sorting_js = "data_table.columns(8).search('', true, false).draw();"; // defualt
        }
    } else {
        $sorting_js = "data_table.columns(8).search('', true, false).draw();"; // defialt
    }

//    echo '<pre>'; print_r($data); echo '</pre>'; // debuging the array
} else { // no data or error
    $no_data = True;
    $table_data = "none";
}

// render page from template
try {
    echo $twig->render('sensor_list.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Sensor',
            'page_subtitle' => 'List sensors',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'no_data' => $no_data,
            'table_data' => $table_data,
            'errors' => $errors,
            'sorting_js' => $sorting_js,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
