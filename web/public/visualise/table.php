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
$errors = "";
$selected_sensor = "";

// get data
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
    print_r($data);
//    echo '<pre>'; print_r($data); echo '</pre>';
} else { // no data or error
    $no_data = True;
    $select_data = "none";
}

// when selected
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        if (!empty($_POST['sensor-select'])) {
            $errors = $_POST['sensor-select'];
            $selected_sensor = $_POST['sensor-select'];
        }
    }
}

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
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error " . $e);
}

?>
