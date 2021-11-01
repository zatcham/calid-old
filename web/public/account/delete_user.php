<?php
require_once __DIR__  . '/../../vendor/autoload.php';
require_once __DIR__  . '/../../include/classes/Database.php';
require_once __DIR__  . '/../../include/classes/Auth.php';
require_once __DIR__  . '/../../include/classes/Account.php';
require_once __DIR__  . '/../../include/classes/Logging.php';

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
    Logging::log("warning", "Problem in account/delete_user. Type: User is unauthorised. Details: User ID: $userid");
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
                        $to = $_POST['user_id'];
                        Logging::log("info", "User deleted successfully in account/delete_user. Details: User ID: $userid, User deleted: $to");
                        echo ("User deleted successfully, redirecting you...");
//                        header("refresh:3 url=users.php"); // waits 3s before redirect // Causes error due to echo.. use JS to redirect isntead
                    } else {
                        echo ("An unexpected error occured when deleting this user");
                        $to = $_POST['user_id'];
                        Logging::log("error", "Problem in account/delete_user. Type: Couldn't delete user. Details: User ID: $userid, User to delete: $to");
                    }
                }
            } else {
                // No user id
                echo ("Error: Request did not contain a user id");
                Logging::log("warning", "Problem in account/delete_user. Type: No User ID in request. Details: User ID: $userid");
            }
        } else {
            // Delete not requested
            echo ("Error: Request did not contain delete property");
            Logging::log("warning", "Problem in account/delete_user. Type: Request didn't include the delete property. Details: User ID: $userid");
        }
    } else {
        // No psot request
        echo ("Error: Not a POST request");
        Logging::log("warning", "Problem in account/delete_user. Type: Request wasn't a POST request. Details: User ID: $userid");
    }
}

?>