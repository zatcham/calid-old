<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];
require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Auth.php';
require $document_root . '\newdir\include\classes\Account.php';

session_start();
// Check login status
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../error/403.html");
    exit;
}

// Assign users varaibles
$userid = $_SESSION["id"];
$username = $_SESSION["username"];

// Check user role - can only delete if admin
if (Auth::isUserAdmin($userid) == False) { // can only access as am admin
    // not allowed here - show error
    header("Location: ../error/403.html");
} else {
    if ($_POST) { // If post request has been sent from edit_user
        if (isset($_POST['delete_user'])) { // Delete
            if (isset($_POST['user_id'])) { // Check if user id has been sent
                if ($userid == $_POST['user_id']) {
                    echo ("You cannot delete your own user account");
                } else {
                    // User to delete isn't themselves
                    if (Account::deleteUser($_POST['user_id'])) {
                        // Success
                        echo ("User deleted successfully, redirecting you...");
//                        header("refresh:3 url=users.php"); // waits 3s before redirect // Causes error due to echo.. use JS to redirect isntead
                    } else {
                        echo ("An unexpected error occured when deleting this user");
                    }
                }
            } else {
                // No user id
                echo ("Error: Request did not contain a user id");
            }
        } else {
            // Delete not requested
            echo ("Error: Request did not contain delete property");
        }
    } else {
        // No psot request
        echo ("Error: Not a POST request");
    }
}

?>