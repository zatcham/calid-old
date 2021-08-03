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

    // following functs are used for the edit sensor page

    public static function doesSensorBelongTo($sensor_id, $userid) {
        $dbconn = Database::Connect();
        $sql = "SELECT COUNT(`SensorName`) FROM sensor_details WHERE `SensorID` = ? AND `UserID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("ss", $sensor_id, $userid); // no error handling as cant be logged in w/ wrong id
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($val);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            if ($val >= 1) { // could use bool but error is a string
                return "Yes";
            } else {
                return "No";
            }
        }
    }

    public static function getSensorName($sensorid) { // a lot of these types of functs are copied ,, we assume the sensor velongs to user
        $dbconn = Database::Connect();
        $sql = "SELECT `SensorName` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);  // functs wont run if invalid id based on prev. valdation
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

    public static function getSensorType($sensorid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `SensorType` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function getSensorLocation($sensorid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `SensorLoc` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function getSensorDataTypes($sensorid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `DataTypes` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function getSensorLastSeen($sensorid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `LastSeen` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function getSensorAPIKey($sensorid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `APIKey` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function getSensorSharedWith($sensorid) { // TODO : Shared with functionalty
        $dbconn = Database::Connect();
        $sql = "SELECT `LastSeen` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function getSensorSWVersion($sensorid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `SWVersion` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function getSensorShowOnAvg($sensorid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `show_on_avg` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function getSensorStatus($sensorid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `Status` FROM sensor_details WHERE `SensorID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("s", $sensorid);
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

    public static function updateSensorStatus($sensorid, $statusid) {
        $dbconn = Database::Connect();
        $sqlq = ("UPDATE sensor_details SET `Status`=? WHERE `SensorID`=?;");
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("ss", $statusid, $sensorid);
        $stmt->execute(); // we run the func once per sensor, no bulk yet
        log("uSS - updated succesfuly. Sensor ID : " . $sensorid . " Status ID : " . $statusid);
        $stmt->close();
        $dbconn->close();
    }

    public static function getStatusName($statusid) {
        $dbconn = Database::Connect();
        $sql = "SELECT `Name` FROM sensor_statustypes WHERE `ID` = ?";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return "Error";
        }
        $stmt->bind_param("i", $statusid);
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

    public static function getListOfDataTypes() {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `ID`, `Name` FROM sensor_datatypes;";
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

    public static function getListOfStatusTypes() {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `ID`, `Name` FROM sensor_statustypes;";
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


}