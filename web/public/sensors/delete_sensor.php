<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];
require $document_root . '\newdir\vendor\autoload.php';
require $document_root . '\newdir\include\classes\Database.php';
require $document_root . '\newdir\include\classes\Auth.php';
require $document_root . '\newdir\include\classes\Sensor.php';

session_start();

// Check login status
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../error/403.html");
    exit;
}

// Assign users varaibles
$userid = $_SESSION["id"];
$username = $_SESSION["username"];

// Same code as delete user
if ($_POST) { // If post request has been sent from edit_sensor
    if (isset($_POST['delete_sensor'])) { // Delete
        if (isset($_POST['sensor_id'])) { // Check if sensor id has been sent
            if (Sensor::deleteSensor($_POST['sensor_id'])) {
                // Success
                echo("Sensor deleted successfully, redirecting you...");
            } else {
                echo("An unexpected error occured when deleting this sensor");
            }
        } else {
            // No sensor id
            echo("Error: Request did not contain a sensor id");
        }
    } else {
        // Delete not requested
        echo("Error: Request did not contain delete property");
    }
} else {
    // No psot request
    echo("Error: Not a POST request");
}

?>