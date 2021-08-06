<?php
$document_root = $_SERVER['DOCUMENT_ROOT'];
require ($document_root . "/newdir/include/variables.php");
//require("Database.php"); // We assume this is already added

class Graph {

    // function to get the average temperature across all sensors for a specific user over the past 24 hours
    public static function getAvgTempGraph($userid): string
    {
        $dbconn = Database::Connect();
        $i = "0";
        // no nned for hum reading
        $sqlq = "SELECT `Date/Time`, HOUR(`Date/Time`) , ROUND(AVG(`Temperature`)) FROM sensor_data INNER JOIN `sensor_details` ON `sensor_data`.SensorID=`sensor_details`.SensorID WHERE `Date/Time` > DATE_SUB(NOW(), INTERVAL 24 HOUR) && `sensor_details`.`UserID`=? && `sensor_details`.`show_on_avg`=1 GROUP BY HOUR(`Date/Time`) ORDER BY (`Date/Time`) DESC";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $sensor_data = $result->fetch_all(MYSQLI_ASSOC);
        if ($sensor_data) {
            $temp = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Temperature`))')), JSON_NUMERIC_CHECK);
            $dt = json_encode(array_reverse(array_column($sensor_data, 'Date/Time')), JSON_NUMERIC_CHECK);
            $i += 1; // we only proceed with output if this = 1
        } else {
            echo ("No results");
        }

        $temp = $temp ?? '';
        $dt = $dt ?? '';
        if ($i == 1) {
            $jsout = <<<JSOUT
    var temp = $temp
    var dtt = $dt
    var randomColorFactor = function() {
        return Math.round(Math.random() * 255);
    };
    var randomColor = function(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.9') + ')';
    };
    var chartT = new Highcharts.Chart({
        chart:{ renderTo : 'chart-temperature' },
        title: { text: "Average temperature for all sensors (24 hours)" },
        series: [{
            showInLegend: false,
            data: temp
        }],
        plotOptions: {
            line: { animation: true,
                dataLabels: { enabled: true }
            },
            series: { color: randomColor(1.0) }
        },
        xAxis: {
            type: 'datetime',
            categories: dtt
        },
        yAxis: {
            title: { text: 'Temperature (°C)' }
        },
        credits: { enabled: false }
    });

JSOUT;
            return ($jsout . "");
        } else {
            return ("");
        }
    }

    // function to get the average temperature across all sensors for a specific user over the past 24 hours
    public static function getAvgHumGraph($userid): string
    {
        $dbconn = Database::Connect();
        $i = "0";
        // no nned for hum reading
        $sqlq = "SELECT `Date/Time`, HOUR(`Date/Time`) , ROUND(AVG(`Humidity`)) FROM sensor_data INNER JOIN `sensor_details` ON `sensor_data`.SensorID=`sensor_details`.SensorID WHERE `Date/Time` > DATE_SUB(NOW(), INTERVAL 24 HOUR) && `sensor_details`.`UserID`=? && `sensor_details`.`show_on_avg`=1 GROUP BY HOUR(`Date/Time`) ORDER BY (`Date/Time`) DESC";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $sensor_data = $result->fetch_all(MYSQLI_ASSOC);
        if ($sensor_data) {
            $hum = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Humidity`))')), JSON_NUMERIC_CHECK);
            $dt = json_encode(array_reverse(array_column($sensor_data, 'Date/Time')), JSON_NUMERIC_CHECK);
            $i += 1; // we only proceed with output if this = 1
        } else {
            echo ("No results");
        }

        $hum = $hum ?? '';
        $dt = $dt ?? '';
        if ($i == 1) {
            $jsout = <<<JSOUT

    var hum = $hum
    var dth = $dt
    var randomColorFactor = function() {
        return Math.round(Math.random() * 255);
    };
    var randomColor = function(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.9') + ')';
    };
    var chartH = new Highcharts.Chart({
        chart:{ renderTo : 'chart-humidity' },
        title: { text: "Average humidity for all sensors (24 hours)" },
        series: [{
            showInLegend: false,
            data: hum
        }],
        plotOptions: {
            line: { animation: true,
                dataLabels: { enabled: true }
            },
            series: { color: randomColor(1.0) }
        },
        xAxis: {
            type: 'datetime',
            categories: dth
        },
        yAxis: {
            title: { text: 'Humidity (%)' }
        },
        credits: { enabled: false }
    });

JSOUT;
            return ($jsout . "");
        } else {
            return ("");
        }
    }

    // Get hum graph for 1 sensor
    public static function getHGraphForOneSensor($sensorid, $timeframe): string {
        $dbconn = Database::Connect();
        $i = "0";
        // no nned for hum reading
        $sqlq = "SELECT `Date/Time`, HOUR(`Date/Time`) , ROUND(AVG(`Humidity`)) FROM sensor_data WHERE `Date/Time` > DATE_SUB(NOW(), INTERVAL 24 HOUR) && `SensorID`=? GROUP BY HOUR(`Date/Time`) ORDER BY (`Date/Time`) DESC";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $sensorid);
        $stmt->execute();
        $result = $stmt->get_result();
        $sensor_data = $result->fetch_all(MYSQLI_ASSOC);
        if ($sensor_data) {
            $hum = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Humidity`))')), JSON_NUMERIC_CHECK);
            $dt = json_encode(array_reverse(array_column($sensor_data, 'Date/Time')), JSON_NUMERIC_CHECK);
            $i += 1; // we only proceed with output if this = 1
        } else {
            echo ("No results");
        }

        $hum = $hum ?? '';
        $dt = $dt ?? '';
        if ($i == 1) {
            $jsout = <<<JSOUT

    var hum = $hum
    var dth = $dt
    var randomColorFactor = function() {
        return Math.round(Math.random() * 255);
    };
    var randomColor = function(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.9') + ')';
    };
    var chartH = new Highcharts.Chart({
        chart:{ renderTo : 'chart-humidity' },
        title: { text: "Average humidity for all sensors (24 hours)" },
        series: [{
            showInLegend: false,
            data: hum
        }],
        plotOptions: {
            line: { animation: true,
                dataLabels: { enabled: true }
            },
            series: { color: randomColor(1.0) }
        },
        xAxis: {
            type: 'datetime',
            categories: dth
        },
        yAxis: {
            title: { text: 'Humidity (%)' }
        },
        credits: { enabled: false }
    });

JSOUT;
            return ($jsout . "");
        } else {
            return ("There is either no humidity data available for the past 24 hours, or an error has occurred.");
        }
    }

    // Get temp graph for 1 sensor
    public static function getTGraphForOneSensor($sensorid, $timeframe): string
    {
        $dbconn = Database::Connect();
        $i = "0";
        // no nned for hum reading
        $sqlq = "SELECT `Date/Time`, HOUR(`Date/Time`) , ROUND(AVG(`Temperature`)) FROM sensor_data WHERE `Date/Time` > DATE_SUB(NOW(), INTERVAL 24 HOUR) && `SensorID`=? GROUP BY HOUR(`Date/Time`) ORDER BY (`Date/Time`) DESC";
        $stmt = $dbconn->prepare($sqlq);
        $stmt->bind_param("s", $sensorid);
        $stmt->execute();
        $result = $stmt->get_result();
        $sensor_data = $result->fetch_all(MYSQLI_ASSOC);
        if ($sensor_data) {
            $temp = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Temperature`))')), JSON_NUMERIC_CHECK);
            $dt = json_encode(array_reverse(array_column($sensor_data, 'Date/Time')), JSON_NUMERIC_CHECK);
            $i += 1; // we only proceed with output if this = 1
        } else {
            echo("No results");
        }

        $temp = $temp ?? '';
        $dt = $dt ?? '';
        if ($i == 1) {
            $jsout = <<<JSOUT
<script>

    var temp = $temp
    var dtt = $dt
    var randomColorFactor = function() {
        return Math.round(Math.random() * 255);
    };
    var randomColor = function(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.9') + ')';
    };
    var chartT = new Highcharts.Chart({
        chart:{ renderTo : 'chart-temperature' },
        title: { text: "Average temperature for all sensors (24 hours)" },
        series: [{
            showInLegend: false,
            data: temp
        }],
        plotOptions: {
            line: { animation: true,
                dataLabels: { enabled: true }
            },
            series: { color: randomColor(1.0) }
        },
        xAxis: {
            type: 'datetime',
            categories: dtt
        },
        yAxis: {
            title: { text: 'Temperature (°C)' }
        },
        credits: { enabled: false }
    });


</script>

JSOUT;
            return ($jsout . "");
        } else {
            return ("There is either no temperature data available for the past 24 hours, or an error has occurred.");
        }
    }
}