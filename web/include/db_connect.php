<?php // use this after db create
include("variables.php"); // vars imported here so dont need to do in other fiels

$dbconn = mysqli_connect($db_host, $db_username, $db_password, $db_name);
// Check conenction
if (!$dbconn) {
    die("DB connection failed: " . mysqli_connect_error());
}

?>