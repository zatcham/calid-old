<?php

class Account {

    // used to reset passwords, accepts plane text password and hashes it
    public static function resetPassword($userid, $newpass) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `users` SET `password`=?, `LastPWChange`=? WHERE `id`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return False;
        }
        $datetime = date("Y-m-d H:i:s");
        $new_hashed = password_hash($newpass, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $new_hashed, $datetime, $userid);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        } else {
            return True;
        }
    }

    // checks password sent to function against current password
    public static function checkPassword($userid, $password): bool { // accepts plane text pwd
        $dbconn = Database::Connect();
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $dbconn->prepare($sql);
        $stmt->bind_param("s", $userid); // no error handling as cant be logged in w/ wrong id
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            return True;
        } else {
            return False;
        }
    }

    // checks password strength
    public static function checkPWStrength($password) {
        // TODO
    }

    // gets last password change date for user
    public static function getLastPWChangeDate($userid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `LastPWChange` FROM users WHERE id = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $userid); // no error handling as cant be logged in w/ wrong id
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($date);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            return $date;
        }
    }

    // gets date user account was created
    public static function getDateCreated($userid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `created_at` FROM users WHERE id = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $userid); // no error handling as cant be logged in w/ wrong id
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($date);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            return $date;
        }
    }

    // gets users email address
    public static function getEmailAddress($userid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `email` FROM users WHERE id = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $userid); // no error handling as cant be logged in w/ wrong id
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($email);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            return $email;
        }
    }

    public static function changeEmail($userid, $new_email) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `users` SET `email`=? WHERE `id`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("ss", $new_email, $userid);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        } else {
            return True;
        }
    }

    // Gets array of user roles
    public static function getListOfUserRoles() {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `ID`, `Name` FROM user_roles;";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->execute();
        if ($stmt == False) {
            return "Error";
        }
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if ($data) { // should mean some data exists
            return ($data);
        } else { // no data or error
            return ($data);
        }
    }

    // gets user role from db
    public static function getUserRole($userid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `UserRole` FROM users WHERE `id` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($date);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            return $date;
        }
    }

    public static function getUserRoleName($userrole) {
        $dbconn = Database::Connect();
        $sql = "SELECT `Name` FROM user_roles WHERE `ID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("i", $userrole);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($name);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            return $name;
        }
    }

}