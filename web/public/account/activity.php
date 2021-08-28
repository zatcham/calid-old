<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require $document_root . '\vendor\autoload.php';
require $document_root . '\include\classes\Database.php';
require $document_root . '\include\classes\Sensor.php';
require $document_root . '\include\classes\Auth.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// check session exists
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../error/403.html");
    exit;
}

// twig init
$loader = new FilesystemLoader('../../templates'); // relative path, maybe to change this
$twig = new Environment($loader);
$twig->addGlobal('session', $_SESSION); // main varaibles used
$twig->addGlobal('file_path', $directory_path);

// Add cookie for redirect
//setcookie("last_page", "account/activity", time() + 3600,  "/");

// varaibles used for functs
$userid = $_SESSION["id"];
$username = $_SESSION["username"];
$errors = "";

// get data
// activity solely logs logins, maybe we should track every page
$dbconn = Database::Connect();
$sqlq = "SELECT `date_time`, `ip_address`, `user_agent`, `attempt_type`  FROM access_attempts WHERE `user_id`=? ORDER BY `date_time` DESC LIMIT 100;"; // only allows last 100 to be got from db
$stmt = $dbconn->prepare($sqlq);
if ($stmt == False) {
    $errors = "Error encountered whilst trying to query database";
}
$stmt->bind_param("s", $userid);
$stmt->execute();
if ($stmt == False) {
    $errors = "Error encountered whilst trying to query database";
}
$data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
if ($data) { // should mean some data exists
    $no_data = False;
    $table_data = $data;
//    echo '<pre>'; print_r($data); echo '</pre>';
} else { // no data or error
    $no_data = True;
    $table_data = "none";
}

// render page from template
try {
    echo $twig->render('account_activity.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Account',
            'page_subtitle' => 'Activity',
            'user_isadmin' => Auth::isUserAdmin($userid),
            'current_user' => $username,
            'no_data' => $no_data,
            'table_data' => $table_data,
            'errors' => $errors,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error. ");
}
?>
