<?php

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
            return False;
        }
    }

    // Adds a login attempt to the database
    public static function addLoginAttempt($userid, $ip, $attempt_type) {
        $dbconn = Database::Connect();
        $sqlq = "INSERT INTO access_attempts (`user_id`, `ip_address`, `attempt_type`) VALUES (?, ?, ?)";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("sss", $userid, $ip, $attempt_type);
        $stmt->execute();
        $stmt->close();
    }

    // Following functions are used for sign up
    // Checks whether an access code exists in the database
    public static function checkAccessKey($key) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT COUNT(`ID`) FROM access_keys WHERE `Key`=? and `Used`=0;";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("s", $key);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        }
        $stmt->store_result();
        $stmt->bind_result($x);
        $stmt->fetch();
        if ($x == 1) {
            return True;
        } else {
            return False;
        }
    }

    // Marks a access key as used
    public static function useAccessKey($key) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `access_keys` SET `Used`=1 WHERE `Key`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("s", $key);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        } else {
            return True;
        }
    }


}