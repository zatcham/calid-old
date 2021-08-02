<?php
$document_root = $_SERVER['DOCUMENT_ROOT']; // TODO : Change to conf file

require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Sensor.php';
require $document_root . '\newdir\include\classes\Auth.php';
require $document_root . '\newdir\include\classes\Account.php';

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
$reset_success = $reset_error = "";
$current_pwd_err = $new_pwd_err = $confnew_pwd_err = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        // Password checking
        if (empty(trim($_POST["inp-cpassword"]))) {
            $reset_error = "Current password field is empty.";
            $current_pwd_err = "Please enter your current password.";
        }
        if (empty(trim($_POST["inp-npassword"]))) {
            $reset_error = "New password field is empty.";
            $new_pwd_err = "Please enter your new password.";
        }
        if (empty(trim($_POST["inp-cnpassword"]))) {
            $reset_error = "Confirm password field is empty.";
            $confnew_pwd_err = "Please confirm your new password.";
        }
        if ($_POST["inp-cnpassword"] != $_POST["inp-npassword"]) {
            $reset_error = "Please ensure you have used the same password in both boxes";
//            $confnew_pwd_err = $new_pwd_err = "Make sure both passwords are the same";
        }
        if (Account::checkPassword($userid, $_POST["inp-cpassword"]) == False) {
            $reset_error = "Current password does not match";
            $current_pwd_err = "Please enter your current password.";
        }
        if (strlen($_POST["inp-cnpassword"]) < 7) {
            $reset_error = "New password must be at least 8 characters long";
        }
//         can now reset pwd
        if ($reset_error == "") {
                if (Account::resetPassword($userid, $_POST["inp-cnpassword"])) {
                    $reset_success = "Password has been reset successfully";
                } else {
                    $reset_error = "An unexpected error has occured. Please try again.";
                }

        }
    }
}

// render page from template
try {
    echo $twig->render('account_resetpass.html.twig',
        ['server_name' => $server_name,
            'page_title' => 'Account',
            'page_subtitle' => 'Reset Password',
            'user_isadmin' => Auth::isUserAdmin($userid), // TODO : user id stuff
            'current_user' => $username,
            'reset_success' => $reset_success,
            'reset_error' => $reset_error,
            'current_pwd_err' => $current_pwd_err,
            'new_pwd_err' => $new_pwd_err,
            'confnew_pwd_err' => $confnew_pwd_err,
        ]);
} catch (\Twig\Error\LoaderError $e) {
    echo ("Error loading page : Twig loader error");
} catch (\Twig\Error\RuntimeError $e) {
    echo ("Error loading page : Twig runtime error");
} catch (\Twig\Error\SyntaxError $e) {
    echo ("Error loading page : Twig syntax error");
}

?>
