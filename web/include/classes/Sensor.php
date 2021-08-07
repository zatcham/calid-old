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

    // some of these to be replaced
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
    public static function addNewSensorFull($userid, $sensor_name, $sensor_type, $sensor_loc, $data_types, $api_key, $shared_with) {
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

    // checks who the sensor belongs to
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

    // gets the sensors frindly name
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
        $stmt->bind_result($date); // date is used acorss a lot of tehse where it was copied from elsewhere
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            return $date;
        }
    }

    // gets the sensor type
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

    // gets the sensor location
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

    // gets the sensors data type (as ID, has to be converted)
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

    // gets sensors last seen date
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

    // gets sensors api key
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

    // gets who the sensor is shared with, not yet built
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

    // gets the sensors softawre version
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

    // gets the sensors show on avg status
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
        $stmt->bind_result($data);
        $stmt->fetch();
        if ($stmt == False) {
            return "Error";
        } else {
            return $data;
        }
    }

    // gets sensor status (as id, must be covnered)
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

    // updates sensors status
    public static function updateSensorStatus($sensorid, $statusid) {
        $dbconn = Database::Connect();
        $sqlq = ("UPDATE sensor_details SET `Status`=? WHERE `SensorID`=?;");
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("ss", $statusid, $sensorid);
        $stmt->execute(); // we run the func once per sensor, no bulk yet
        error_log("uSS - updated succesfuly. Sensor ID : " . $sensorid . " Status ID : " . $statusid); // TODO: should use log more
        $stmt->close();
        $dbconn->close();
    }

    // gets the statuses friendly name
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

    // gets a list tof data types
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

    // gets a list of status types
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

    // Edit sensor form submission functs

    // changes sensor friendly name
    public static function changeSensorName($sensor_id, $sensor_name) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `sensor_details` SET `SensorName`=? WHERE `SensorID`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("ss", $sensor_name, $sensor_id);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        } else {
            return True;
        }
    }

    // cahnes sensor location
    public static function changeSensorLocation($sensor_id, $sensor_location) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `sensor_details` SET `SensorLoc`=? WHERE `SensorID`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("ss", $sensor_location, $sensor_id);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        } else {
            return True;
        }
    }

    // cahnges sensor data type
    public static function changeSensorDataTypes($sensor_id, $sensor_datatypes) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `sensor_details` SET `DataTypes`=? WHERE `SensorID`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("ss", $sensor_datatypes, $sensor_id);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        } else {
            return True;
        }
    }

    // changes sensors show on avg
    public static function changeSensorAverage($sensor_id, $sensor_avetage) {
        $dbconn = Database::Connect();
        $sql = "UPDATE `sensor_details` SET `show_on_avg`=? WHERE `SensorID`=?;";
        $stmt = $dbconn->prepare($sql);
        if ($stmt == False) {
            return False;
        }
        $stmt->bind_param("ss", $sensor_avetage, $sensor_id);
        $stmt->execute();
        if ($stmt == False) {
            return False;
        } else {
            return True;
        }
    }

    // New sensor page functs

    // generates api key for sensor
    protected static function generateAPIKey($id) { // Generates API key for sensor - appends user id to output string
        $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $len = strlen($chars);
        $x = '';
        for ($i = 0; $i < $len; $i++) {
            $x .= $chars[mt_rand(0, $len - 1)];
        }
        $x .= $id;
        return $x;
    }

    // adds a new sensor to db
    public static function addNewSensor($userid, $sensor_name, $sensor_location, $data_type, $show_on_avg) {
        $dbconn = Database::Connect();
        $sqlq = "INSERT INTO sensor_details (`UserID`, `SensorName`, `SensorLoc`, `DataTypes`, `APIKey`, `show_on_avg`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            return False;
        }
        $api_key = self::generateAPIKey($userid);
        $stmt->bind_param("ssssss", $userid, $sensor_name, $sensor_location, $data_type, $api_key, $show_on_avg);
        $stmt->execute();
        // now get the ID of the sensor
        $sensor_id = $dbconn->insert_id;
        $stmt->close();
        if ($stmt == False) {
            return False;
        } else {
            return $sensor_id;
        }
    }

    // gets data type based on check boxes clicked, worst code ever written in the world
    public static function getOverallDataType($type1, $type2, $type3) { // I need to work out from the tick boxes what overall type is selected
        switch ($type1) {
            case 0:
                switch ($type2) { // - h
                    case 0: // no h
                        switch ($type3) { // - u
                            case 0: // no uv
                                return 0; // error, no t/h/u
                                break;
                            case 1: // yes uv
                                return 3; // u only
                        }
                    case 1: // yes h
                        switch ($type3) { // - u
                            case 0: // no uv
                                return 2; // h
                            case 1: // yes uv
                                return 5; // h + u
                        }
                }
            case 1:
                switch ($type2) { // - h
                    case 0: // no h
                        switch ($type3) { // - u
                            case 0: // no uv
                                return 1; // t
                            case 1: // yes uv
                                return 7; // t + u
                        }
                    case 1: // yes h
                        switch ($type3) { // - u
                            case 0: // no uv
                                return 4; // t + h
                            case 1: // yes uv
                                return 6; // t + h + u
                        }
                }
        }
        return 0;
    }
}