<?php

require_once __DIR__  . '\..\variables.php';
require_once ("Logging.php");

class Table {
    // All limited to 1000 due to "issues"
    public static function getTemperatureTable($sensor_id, $start_date, $end_date) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `SensorName`, `Date/Time`, ROUND(`Temperature`) FROM sensor_data WHERE `Date/Time` >= ? and `Date/Time` <= ? and `SensorID`=? ORDER BY (`Date/Time`) DESC LIMIT 1000;";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            Logging::log("error", "Error occured whilst attempting to prepare query in Table - getTemperatureTable");
            return False;
        }
        $stmt->bind_param("sss", $start_date, $end_date, $sensor_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if ($stmt == False) {
            Logging::log("error", "Error occured whilst attempting to execute query in Table - getTemperatureTable");
            return False;
        } else {
            return $data;
        }
    }

    public static function getHumidityTable($sensor_id, $start_date, $end_date) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `SensorName`, `Date/Time`, ROUND(`Humidity`) FROM sensor_data WHERE `Date/Time` >= ? and `Date/Time` <= ? and `SensorID`=? ORDER BY (`Date/Time`) DESC LIMIT 1000";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            Logging::log("error", "Error occured whilst attempting to prepare query in Table - getHumidityTable");
            return False;
        }
        $stmt->bind_param("sss", $start_date, $end_date, $sensor_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if ($stmt == False) {
            Logging::log("error", "Error occured whilst attempting to execute query in Table - getHumidityTable");
            return False;
        } else {
            return $data;
        }
    }

    public static function getUVTable($sensor_id, $start_date, $end_date) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `SensorName`, `Date/Time`, ROUND(`UV`) FROM sensor_data WHERE `Date/Time` >= ? and `Date/Time` <= ? and `SensorID`=? ORDER BY (`Date/Time`) DESC LIMIT 1000";
        $stmt = $dbconn->prepare($sqlq);
        if ($stmt == False) {
            Logging::log("error", "Error occured whilst attempting to prepare query in Table - getUVTable");
            return False;
        }
        $stmt->bind_param("sss", $start_date, $end_date, $sensor_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        if ($stmt == False) {
            Logging::log("error", "Error occured whilst attempting to execute query in Table - getUVTable");
            return False;
        } else {
            return $data;
        }
    }
}