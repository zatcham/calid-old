<?php
// Initialize the session
session_start();

// Get page to redirect to
//if (isset($_COOKIE['last_page'])) {
//    $last_page = $_COOKIE['last_page'];
//}

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

if (isset($_GET)) {
    if (!empty($_GET['msg'])) {
        if (trim($_GET['msg']) == "autologout") {
            header("location: login.php?msg=autologout");
        }
    }
}

// If nothing, go to login and show logout message
header("location: login.php?msg=logout");
exit;
?>
