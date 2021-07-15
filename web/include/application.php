<?php

if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    // this means im on dev
    $site_url = "http://127.0.0.1:81/sensor";

    $db_host = "localhost";
    $db_name = "sensor";
    $db_username = "root";
    $db_password = "";
    $db_prefix = "sensor";

    $show_errors = true;
} elseif ($_SERVER['SERVER_NAME'] == 'sense-stg.matcham.org.uk')  {
    // stageing

} elseif ($_SERVER['SERVER_NAME'] == 'sense.matcham.org.uk')  {

} else {
    echo ("Error when assigning variables - File : application.php");
}

// Enable error reporting
if ($show_errors) {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
} else {
    error_reporting(0);
    ini_set("display_errors", 0);
}