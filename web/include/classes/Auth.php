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
}