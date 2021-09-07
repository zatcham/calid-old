<?php

require_once ("Logging.php");

class Auth {

    // Checks whether a user is an admin
    public static function isUserAdmin($userid) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `UserRole` FROM users WHERE `id`=?;";
        $stmt = $dbconn->prepare($sqlq); // TODO: error handling?
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt -> store_result();
        $stmt -> bind_result($r);
        $stmt -> fetch();
        if ($r == "2") { // 2 = admin, 1 = user, 3 = not verified | could do db lookup but why bother
            return True;
        } else {
            $error = $stmt->error;
            \Logging::log('error', "SQL Error occured in Auth/isUserAdmin. Details: $error");
            return False;
        }
    }

    // Adds a login attempt to the database
    public static function addLoginAttempt($userid, $ip, $attempt_type, $user_agent) {
        $dbconn = Database::Connect();
        $sqlq = "INSERT INTO access_attempts (`user_id`, `ip_address`, `attempt_type`, `user_agent`) VALUES (?, ?, ?, ?)";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("ssss", $userid, $ip, $attempt_type, $user_agent); // error handling TODO
        $stmt->execute();
        $stmt->close();
        \Logging::log('info', "New access attempt: User ID $userid, IP Address $ip, User Agent $user_agent, Attempt Type: $attempt_type");
    }

    // Following functions are used for sign up
    // Checks whether an access code exists in the database
    public static function checkAccessKey($key) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT COUNT(`ID`) FROM access_keys WHERE `Key`=? and `Used`=0;";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            $error = $stmt->error();
            \Logging::log('error', "SQL Error occured in Auth/checkAccessKey. Details: $error");
            return False;
        }
        $stmt->bind_param("s", $key);
        $stmt->execute();
        if ($stmt == False) {
            $error = $stmt->error();
            \Logging::log('error', "SQL Error occured in Auth/checkAccessKey. Details: $error");
            return False;
        }
        $stmt->store_result();
        $stmt->bind_result($x);
        $stmt->fetch();
        if ($x == 1) {
            return True;
        } else {
            $error = $stmt->error();
            \Logging::log('error', "SQL Error occured in Auth/checkAccessKey. Details: $error");
            return False;
        }
    }

    // Marks a access key as used
    public static function useAccessKey($key) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `access_keys` SET `Used`=1 WHERE `Key`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            $error = $stmt->error();
            \Logging::log('error', "SQL Error occured in Auth/useAccessKey. Details: $error");
            return False;
        }
        $stmt->bind_param("s", $key);
        $stmt->execute();
        if ($stmt == False) {
            $error = $stmt->error();
            \Logging::log('error', "SQL Error occured in Auth/useAccessKey. Details: $error");
            return False;
        } else {
            \Logging::log('info', "Access key used: $key");
            return True;
        }
    }

    // UI Settings retrieval - in auth because each page needs it
    // Gets nav dark mode setting
    public static function getNavDarkMode($userid) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `LightModeNav` FROM ui_settings WHERE `UserID`=?;";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            $error = $stmt->error();
            \Logging::log('error', "SQL Error occured in Auth/getNavDarkMode. Details: $error");
            return "dark";
        }
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($val);
        $stmt->fetch();
        if ($stmt == False) {
            $error = $stmt->error();
            \Logging::log('error', "SQL Error occured in Auth/getNavDarkMode. Details: $error");
            return "dark";
        } else {
            if ($val == 1) {
                return "light";
            } else {
                return "dark";
            }
        }
        return "dark"; // always return dark
    }

    // Twig global variables
    public static function getTwigGlobals($userid) {
        $twig->addGlobal('nav_colour', Auth::getNavDarkMode($userid));
        // TODO
    }


}