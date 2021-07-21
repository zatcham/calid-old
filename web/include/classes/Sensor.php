<?php

class Sensor {

    // gets sensors assigned to user
    public static function getAssignedSensorCount($userid) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT COUNT(`SensorID`) FROM sensor_details WHERE `UserID`=?;";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt -> store_result();
        $stmt -> bind_result($amount);
        $stmt -> fetch();
        return ($amount);
    }

    // gets sensirs last seen in 24 hrs, we call that online
    public static function getOnlineSensorCount($userid) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT COUNT(`SensorID`) FROM sensor_details WHERE `UserID`=? && `LastSeen` > DATE_SUB(CURDATE(), INTERVAL 1 DAY);";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt -> store_result();
        $stmt -> bind_result($amount);
        $stmt -> fetch();
        return ($amount);
    }

    // opposite of previous function, literally just inverted sign
    public static function getOfflineSensorCount($userid) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT COUNT(`SensorID`) FROM sensor_details WHERE `UserID`=? && `LastSeen` < DATE_SUB(CURDATE(), INTERVAL 1 DAY);";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt -> store_result();
        $stmt -> bind_result($amount);
        $stmt -> fetch();
        return ($amount);
    }

    // gets sensors with alerts. TODO : make alert system / table
    public static function getAlertedSensorCount($userid) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT COUNT(`sensor_alerts`.`SensorID`) FROM sensor_alerts INNER JOIN `sensor_details` ON `sensor_alerts`.SensorID =`sensor_details`.SensorID WHERE `sensor_details`.`UserID`=? && `sensor_alerts`.`Date/Time` > DATE_SUB(CURDATE(), INTERVAL 1 DAY);";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt -> store_result();
        $stmt -> bind_result($amount);
        $stmt -> fetch();
        return ($amount);
    }

    // TODO
    public static function getAllSensors($userid) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `SensorID`, `SensorName` FROM sensor_details WHERE `UserID` = `UserID`=?;";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $stmt -> store_result();
        $stmt -> bind_result($amount);
        $stmt -> fetch();
        return ($amount);
    }

    // TODO : this ufnc and change table row names
    public static function addNewSensor($userid, $sensor_name, $sensor_type, $sensor_loc, $data_types, $api_key, $shared_with) {
        $dbconn = Database::Connect();
        $sqlq = ("INSERT INTO sensor_details (UserID, SensorName, SensorType, SensorLoc, DataTypes, APIKey, SharedWith) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("sssssss", $userid, $sensor_name, $sensor_type, $sensor_loc, $data_types, $api_key, $shared_with);
        $stmt->execute(); // we run the func once per sensor, no bulk yet
        log("aNS - added succesfuly");
        $stmt->close();
        $dbconn->close();
    }

}