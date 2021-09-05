<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
require_once ($document_root . "/include/variables.php");

class Table {
    public static function getTemperatureTable($sensor_id, $start_date, $end_date) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `SensorName`, `Date/Time`, ROUND(`Temperature`) FROM sensor_data WHERE `Date/Time` >= ? and `Date/Time` <= ? and `SensorID`=? ORDER BY (`Date/Time`) DESC LIMIT 495";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("sss", $start_date, $end_date, $sensor_id); // TODO: error handling
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
//        $data = $stmt->get_result()->fetch_array();
        return $data;
    }

    public static function getHumidityTable($sensor_id, $start_date, $end_date) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `SensorName`, `Date/Time`, ROUND(`Humidity`) FROM sensor_data WHERE `Date/Time` >= ? and `Date/Time` <= ? and `SensorID`=? ORDER BY (`Date/Time`) DESC";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("sss", $start_date, $end_date, $sensor_id); // TODO: error handling
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

    public static function getUVTable($sensor_id, $start_date, $end_date) {
        $dbconn = Database::Connect();
        $sqlq = "SELECT `SensorName`, `Date/Time`, ROUND(`UV`) FROM sensor_data WHERE `Date/Time` >= ? and `Date/Time` <= ? and `SensorID`=? ORDER BY (`Date/Time`) DESC";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("sss", $start_date, $end_date, $sensor_id); // TODO: error handling
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        return $data;
    }
}