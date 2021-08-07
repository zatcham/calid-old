<?php

class Account {

    // A lot of these functions are used for the account settings page

    // used to reset passwords, accepts plane text password and hashes it
    public static function resetPassword($userid, $newpass) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `users` SET `password`=?, `LastPWChange`=? WHERE `id`=?;"; // update query is used as we want to update the password in table
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) { // error checking
            return False;
        }
        $datetime = date("Y-m-d H:i:s"); // gets time to update the pw change date/time
        $new_hashed = password_hash($newpass, PASSWORD_DEFAULT); // hash new password ready for insert to table
        $stmt->bind_param("sss", $new_hashed, $datetime, $userid);
        $stmt->execute();
        if ($stmt == False) { // more error checking
            return False;
        } else {
            return True; // all is good
        }
    }

    // checks password sent to function against current password
    public static function checkPassword($userid, $password): bool { // accepts plane text pwd
        $dbconn = Database::Connect();
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) { // error checking
            return False;
        }
        $stmt->bind_param("s", $userid); // no error handling as cant be logged in w/ wrong id
        $stmt->execute();
        if ($stmt == False) {
            return False;
        }
        $stmt->store_result();
        $stmt->bind_result($hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) { // used to check plane text pwd against the hashed db equiv
            return True; // password correct
        } else {
            return False; // password incorrect
        }
    }

    // checks password strength
    public static function checkPWStrength($password) {
        // TODO : is this funct necessary
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

    // Change users email address
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
            return False;
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
        $stmt->bind_result($role);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            return $role;
        }
    }

    // gets the corresponding name for a user roles id from the table
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

    // used for edit user
    // gets a username from userid
    public static function getUsername($userid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `username` FROM users WHERE id = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $userid);
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

    // does user id exist
    public static function doesUserExist($userid) {
        $dbconn = Database::Connect();
        $sql = "SELECT COUNT(`id`) FROM users WHERE id = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($count);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            if ($count == 1) {
                return True;
            } else {
                return False;
            }
        }
    }

    // changes user role
    public static function changeUserRole($userid, $newrole) { // role is in id form
        $dbconn = Database::Connect();
        $sql = "UPDATE `users` SET `UserRole`=? WHERE `id`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("ss", $newrole, $userid);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        } else {
            return True;
        }
    }

    // generate key for sign up and adds to db
    public static function generateKey($id) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $len = 6; // length of key output
        $x = '';
        for ($i = 0; $i < $len; $i++) {
            $x .= $chars[mt_rand(0, strlen($chars)- 1)];
        }
        $x .= ("-" . $id); // suffixes user id

        $dbconn = Database::Connect();
        $sqlq = "INSERT INTO access_keys (`Key`, `Generated_By`) VALUES (?, ?);";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("ss", $x, $id);
        $stmt->execute();
        $stmt->close();
        if ($stmt == False) {
            return False;
        } else {
            return $x;
        }
    }

    // Create new user - used on new user screen
    public static function createNewUser($username, $email, $password, $role) {
        $dbconn = Database::Connect();
        $sqlq = "INSERT INTO users (`username`, `password`, `email`, `UserRole`) VALUES (?, ?, ?, ?)";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            return False;
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $username, $hashed_password, $email, $role);
        $stmt->execute();
        // now get the ID of the user
        $user_id = $dbconn->insert_id;
        $stmt->close();
        if ($stmt == False) {
            return False;
        } else {
            return $user_id;
        }
    }

}