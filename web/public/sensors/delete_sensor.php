<?php

require_once __DIR__  . '\..\..\vendor\autoload.php';
require_once __DIR__  . '\..\..\include\classes\Database.php';
require_once __DIR__  . '\..\..\include\classes\Auth.php';
require_once __DIR__  . '\..\..\include\classes\Sensor.php';
require_once __DIR__  . '\..\..\include\classes\Logging.php';

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
                $sensor = $_POST['sensor_id'];
                Logging::log("error", "Problem in sensors/delete_sensor. Type: Deleting sensor, Details: User ID: $userid, Sensor: $sensor");
            }
        } else {
            // No sensor id
            echo("Error: Request did not contain a sensor id");
            Logging::log("warning", "Problem in sensors/delete_sensor. Type: No Sensor ID in request. Details: User ID: $userid");
        }
    } else {
        // Delete not requested
        echo("Error: Request did not contain delete property");
        Logging::log("warning", "Problem in sensors/delete_sensor. Type: Request didn't include the delete property. Details: User ID: $userid");
    }
} else {
    // No psot request
    echo("Error: Not a POST request");
    Logging::log("warning", "Problem in sensors/delete_sensor. Type: Request wasn't a POST request. Details: User ID: $userid");
}

?>