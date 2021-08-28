<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];

require $document_root . '\vendor\autoload.php';
require $document_root . '\include\classes\Database.php';
require $document_root . '\include\classes\Auth.php';
require $document_root . '\include\classes\Account.php';

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

// varaibles used for current usr functs - not used for main here
$userid = $_SESSION["id"];
$username = $_SESSION["username"];

// default vars
$form_success_list = $user_roles = [];
$create_date = $pw_change_date = $user_role = $user_email = $user = "";
$errors = $form_success = $no_user = $no_data = "";
$is_user_me = $selected_id = "";

// get user specified in get
if (!$_GET) {
    $no_user = True;
    $errors = "Error: A user has not been selected. Please return to the user list and try again.";
} else {
    if (!empty($_GET['user'])) { // userid sent
        $no_user = False;
        $selected_id = $_GET['user'];
        if (Account::doesUserExist($selected_id)) {
            $user = Account::getUsername($selected_id);
            $pw_change_date = Account::getLastPWChangeDate($selected_id); // runs functions to get data to dusplay
            $create_date = Account::getDateCreated($selected_id);
            $user_email = Account::getEmailAddress($selected_id);
            $user_role = Account::getUserRole($selected_id);
            if ($user == $username) {
                $is_user_me = True;
            } else {
                $is_user_me = False;
            }
            $user_roles = Account::getListOfUserRoles();
        } else {
            $no_user = True;
            $errors = "Error: The user selected does not exist. Please return to the user list and try again";
        }
    }
}

// form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST)) {
        $count = 0;
        $form_success_list = array();
        if (Auth::isUserAdmin($userid) == True) { // just check in case of bad requesets
            if ($_POST['role-select'] != $user_role) { // not equal, change it
                if (Account::changeUserRole($selected_id, $_POST['role-select'])) {
                    $form_success_list[] = "User Role";
                    $count += 1;
                } else {
                    $errors = "Error encountered whilst changing 'User Role'. Please try again";
                }
            }
            if ($_POST['inp-email'] != $user_email) { // not equal, change it
                if (Account::changeEmail($selected_id, $_POST['inp-email'])) {
                    $form_success_list[] = "Email Address";
                    $count += 1;
                } else {
                    $errors = "Error encountered whilst changing 'Email Address'. Please try again";
                }
            }
            if (!empty($_POST['inp-npassword'])) {
                if (!empty($_POST['inp-cnpassword'])) {
                    if ($_POST['inp-npassword'] == $_POST['inp-cnpassword']) {
                        if (strlen($_POST['inp-npassword']) >= 8) {
                            if (Account::resetPassword($selected_id, $_POST['inp-npassword'])) {
                                $form_success_list[] = "Password";
                                $count += 1;
                            } else {
                                $errors = "Error encountered whilst changing 'Password'. Please try again";
                            }
                        } else {
                            $errors = "Error: The password entered must be a minimum of 8 characters.";
                        }
                    }
                } else {
                    $errors = "Error: please check that both passwords entered match.";
                }
            }
            if ($count > 0) {
                $form_success = "The following options were updated successfully: ";
            }
        } else {
            $errors = "Unauthroised: You must be an administrator to make use of this page";
        }
    }
}


// render page from template
if (Auth::isUserAdmin($userid) == False) { // can only access as am admin
    // not allowed here - show error
    header("Location: ../error/403.html"); // TODO : sort out error pages
} else {
    try {
        echo $twig->render('account_edituser.html.twig',
            ['server_name' => $server_name,
                'page_title' => 'Account',
                'page_subtitle' => 'Edit user',
                'user_isadmin' => Auth::isUserAdmin($userid),
                'current_user' => $username,
                'create_date' => $create_date,
                'pw_change_date' => $pw_change_date,
                'user_role' => $user_role,
                'user_email' => $user_email,
                'errors' => $errors,
                'form_success' => $form_success,
                'form_success_list' => $form_success_list,
                'user' => $user,
                'no_user' => $no_user,
                'no_data' => $no_data,
                'is_user_me' => $is_user_me,
                'user_roles' => $user_roles,
                'selected_id' => $selected_id,
            ]);
    } catch (\Twig\Error\LoaderError $e) {
        echo("Error loading page : Twig loader error");
    } catch (\Twig\Error\RuntimeError $e) {
        echo("Error loading page : Twig runtime error");
    } catch (\Twig\Error\SyntaxError $e) {
        echo("Error loading page : Twig syntax error");
    }
}

?>
