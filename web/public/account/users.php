<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Sensor.php';
require $document_root . '\newdir\include\classes\Auth.php';
require $document_root . '\newdir\include\classes\Account.php';

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
$sqlq = "SELECT `id`, `username`, `email`, `UserRole`, `created_at`, `LastPWChange` FROM users";
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
    foreach($data as $key => $value) { // Replace user role ID with corespodning name
        $user_role = (intval($data[$key]['UserRole']) - 1);
        $user_roles = Account::getListOfUserRoles();
        $role_as_str = ($user_roles[$user_role]['Name']);
        $data[$key]['UserRole'] = $role_as_str;
    }
    $table_data = $data;
//    echo '<pre>'; print_r($data); echo '</pre>';
} else { // no data or error
    $no_data = True;
    $table_data = "none";
}


// render page from template
if (Auth::isUserAdmin($userid) == False) { // can only access as am admin
    // not allowed here - show error
    header("Location: ../error/403.html"); // TODO : sort out error pages
} else {
    try {
        echo $twig->render('account_users.html.twig',
            ['server_name' => $server_name,
                'page_title' => 'Account',
                'page_subtitle' => 'Users',
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
        echo ("Error loading page : Twig syntax error");
    }
}

?>
