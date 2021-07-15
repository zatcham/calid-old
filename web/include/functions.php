<?php

// i think using concatenated statements is ok for anything not accepting a user inputted str as there is no risk of sql inj.
require("variables.php");
require("db_connect.php");

// TODO : Change some sql, maybe classes?
// TODO: build db names and more vars

function getAvgTempGraph($userid)
{
    global $dbconn;
    $sid = "1000";
    $i = "0";
//    $sqlq = "SELECT `SensorName`, `Date/Time`, `Temperature`, `Humidity` FROM sensor_data WHERE `SensorID`= $sid ORDER BY `Date/Time` desc limit 5000"; // TODO : Sort out query
    $sqlq = "SELECT `Date/Time`, HOUR(`Date/Time`) , ROUND(AVG(`Temperature`)) , ROUND(AVG(`Humidity`)) FROM sensor_data INNER JOIN `sensor_details` ON `sensor_data`.SensorID=`sensor_details`.SensorID WHERE `Date/Time` > DATE_SUB(NOW(), INTERVAL 24 HOUR) && `sensor_details`.`UserID`=$userid GROUP BY HOUR(`Date/Time`) ORDER BY (`Date/Time`) DESC";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $sensor_data[] = $data;
            }
            $temp = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Temperature`))')), JSON_NUMERIC_CHECK);
            $hum = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Humidity`))')), JSON_NUMERIC_CHECK);
            //$dt = json_encode(array_reverse(array_column($sensor_data, 'HOUR(`Date/Time`)')), JSON_NUMERIC_CHECK);
            $dt = json_encode(array_reverse(array_column($sensor_data, 'Date/Time')), JSON_NUMERIC_CHECK);
            $i += 1;
        } else {
//            echo("SensorData - No results");
        }
    } else {
        echo "Error - 1";
    }

    $sqlq2 = "SELECT `SensorName` FROM sensor_data WHERE `SensorID`= $sid ORDER BY `EventID` DESC LIMIT 1";
//    $result = mysqli_query($dbconn, $sqlq2);
//
//    if ($result != False) {
//        if (mysqli_num_rows($result) > 0) {
//            while ($data = mysqli_fetch_assoc($result)) {
//                $name = $data["SensorName"];
//            }
//            $name = ("\"" . $name . "\"");
//            $i += 1;
//        } else {
//            echo("<br> SensorName - No results");
//        }
//    } else {
//        echo "Error - 2";
//    }

    $temp = isset($temp) ? $temp : '';
    $hum = isset($hum) ? $hum : '';
    $dt = isset($dt) ? $dt : '';
//    $name = isset($name) ? $name : '';
    //mysqli_close($dbconn);
    if ($i == 1) {
        $jsout = <<<JSOUT
<script>

    var temp = $temp
    var hum = $hum
    var dt = $dt
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
            categories: dt
        },
        yAxis: {
            title: { text: 'Temperature (Celsius)' }
        },
        credits: { enabled: false }
    });

    var chartH = new Highcharts.Chart({
        chart:{ renderTo:'chart-humidity' },
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
            categories: dt
        },
        yAxis: {
            title: { text: 'Humidity (%)' }
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

function getAvgHumGraph($userid)
{
    global $dbconn;
    $sid = "1000";
    $i = "0";
//    $sqlq = "SELECT `SensorName`, `Date/Time`, `Temperature`, `Humidity` FROM sensor_data WHERE `SensorID`= $sid ORDER BY `Date/Time` desc limit 5000"; // TODO : Sort out query
    $sqlq = "SELECT `Date/Time`, HOUR(`Date/Time`) , ROUND(AVG(`Temperature`)) , ROUND(AVG(`Humidity`)) FROM sensor_data INNER JOIN `sensor_details` ON `sensor_data`.SensorID=`sensor_details`.SensorID WHERE `Date/Time` > DATE_SUB(NOW(), INTERVAL 24 HOUR) && `sensor_details`.`UserID`=$userid GROUP BY HOUR(`Date/Time`) ORDER BY (`Date/Time`) DESC";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $sensor_data[] = $data;
            }
            $temp = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Temperature`))')), JSON_NUMERIC_CHECK);
            $hum = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Humidity`))')), JSON_NUMERIC_CHECK);
            //$dt = json_encode(array_reverse(array_column($sensor_data, 'HOUR(`Date/Time`)')), JSON_NUMERIC_CHECK);
            $dt = json_encode(array_reverse(array_column($sensor_data, 'Date/Time')), JSON_NUMERIC_CHECK);
            $i += 1;
        } else {
//            echo("SensorData - No results");
        }
    } else {
        echo "Error - 1";
    }

    $sqlq2 = "SELECT `SensorName` FROM sensor_data WHERE `SensorID`= $sid ORDER BY `EventID` DESC LIMIT 1";
//    $result = mysqli_query($dbconn, $sqlq2);
//
//    if ($result != False) {
//        if (mysqli_num_rows($result) > 0) {
//            while ($data = mysqli_fetch_assoc($result)) {
//                $name = $data["SensorName"];
//            }
//            $name = ("\"" . $name . "\"");
//            $i += 1;
//        } else {
//            echo("<br> SensorName - No results");
//        }
//    } else {
//        echo "Error - 2";
//    }

    $temp = isset($temp) ? $temp : '';
    $hum = isset($hum) ? $hum : '';
    $dt = isset($dt) ? $dt : '';
//    $name = isset($name) ? $name : '';
    //mysqli_close($dbconn);
    if ($i == 1) {
        $jsout = <<<JSOUT
<script>

    var hum = $hum
    var dt = $dt
    var randomColorFactor = function() {
        return Math.round(Math.random() * 255);
    };
    var randomColor = function(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.9') + ')';
    };

    var chartH = new Highcharts.Chart({
        chart:{ renderTo:'chart-humidity' },
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
            categories: dt
        },
        yAxis: {
            title: { text: 'Humidity (%)' }
        },
        credits: { enabled: false }
    });

</script>

JSOUT;
        return ($jsout . "");
    } else {
        return ("There is either no humidity data available for the past 24 hours, or an error has occurred.");
    }

}

function getSensorsGraphs($sid)
{
    global $dbconn;
//    $sqlq = "SELECT `SensorName`, `Date/Time`, `Temperature`, `Humidity` FROM sensor_data WHERE `SensorID`= $sid ORDER BY `Date/Time` desc limit 5000"; // TODO : Sort out query
    $sqlq = "SELECT `Date/Time`, HOUR(`Date/Time`) , ROUND(AVG(`Temperature`)) , ROUND(AVG(`Humidity`)) FROM sensor_data WHERE DATE_SUB(`Date/Time` , INTERVAL 1 HOUR) && `SensorID`=$sid && `Date/Time` >= NOW() - INTERVAL 1 DAY GROUP BY HOUR(`Date/Time`) ORDER BY HOUR(`Date/Time`) DESC";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $sensor_data[] = $data;
            }
        } else {
            echo("SensorData - No results");
        }
    } else {
        echo "Error - 1";
    }

    $sqlq2 = "SELECT `SensorName` FROM sensor_data WHERE `SensorID`= $sid ORDER BY `EventID` DESC LIMIT 1";
    $result = mysqli_query($dbconn, $sqlq2);

    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $name = $data["SensorName"];
            }
        } else {
            echo("<br> SensorName - No results");
        }
    } else {
        echo "Error - 2";
    }

    $temp = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Temperature`))')), JSON_NUMERIC_CHECK);
    $hum = json_encode(array_reverse(array_column($sensor_data, 'ROUND(AVG(`Humidity`))')), JSON_NUMERIC_CHECK);
    //$dt = json_encode(array_reverse(array_column($sensor_data, 'HOUR(`Date/Time`)')), JSON_NUMERIC_CHECK);
    $dt = json_encode(array_reverse(array_column($sensor_data, 'Date/Time')), JSON_NUMERIC_CHECK);
    $name = ("\"" . $name . "\"");

    mysqli_close($dbconn);

    $jsout = <<<JSOUT
<script>

    var temp = $temp
    var hum = $hum
    var dt = $dt
    var sensor_name = $name + " Sensor";
    var randomColorFactor = function() {
        return Math.round(Math.random() * 255);
    };
    var randomColor = function(opacity) {
        return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',' + (opacity || '.9') + ')';
    };
    var chartT = new Highcharts.Chart({
        chart:{ renderTo : 'chart-temperature' },
        title: { text: sensor_name + " Temperature" },
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
            categories: dt
        },
        yAxis: {
            title: { text: 'Temperature (Celsius)' }
        },
        credits: { enabled: false }
    });

    var chartH = new Highcharts.Chart({
        chart:{ renderTo:'chart-humidity' },
        title: { text: sensor_name + " Humidity" },
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
            categories: dt
        },
        yAxis: {
            title: { text: 'Humidity (%)' }
        },
        credits: { enabled: false }
    });

</script>

JSOUT;

    return ($jsout . "");
}

function getAllSensors($userid) {
    global $dbconn;
    $id_array = array();
    $sqlq = "SELECT `SensorID`, `SensorName` FROM sensor_details WHERE `UserID` = $userid;";
    $result = mysqli_query($dbconn, $sqlq);

    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            echo "<select id=\"selectsid\" name=\"selectsid\">";
            while ($data = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $data['SensorID'] . "'>" . $data['SensorName'] . "</option>";
            }
            echo "</select>";
        } else {
            echo("<br> SensorID - No results");
        }
    } else {
        echo "Error - 3";
    }
//    mysqli_close($dbconn);
}

// ajax testing
function processDrpdown($selectedVal) {
    echo "Selected value in php ".$selectedVal;
}

if (isset($_POST['dropdownValue'])) {
    if ($_POST['dropdownValue']) {
        //call the function or execute the code
        processDrpdown($_POST['dropdownValue']);
        getSensorsGraphs($_POST['dropdownValue']);
    }
}

function getAssignedSensorCount($userid) {
    global $dbconn;
    $sqlq = "SELECT COUNT(`SensorID`) FROM sensor_details WHERE `UserID`=$userid;";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $amount = $data['COUNT(`SensorID`)'];
            }
            return $amount;
        } else {
            echo("Assigned count - No results");
        }
    } else {
        echo "Error - 4 (gASC)";
    }
}

function getLastSeenSCount($userid) {
    global $dbconn;
    $sqlq = "SELECT COUNT(`SensorID`) FROM sensor_details WHERE `UserID`=$userid && `LastSeen` > DATE_SUB(NOW(), INTERVAL 1 HOUR);";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $amount = $data['COUNT(`SensorID`)'];
            }
            return $amount;
        } else {
            echo("Online count - No results");
        }
    } else {
        echo "Error - 5 (gLSSC)";
    }
}

// offline count (l. seen 3 days)
function getOfflineSCount($userid) {
    global $dbconn;
    $sqlq = "SELECT COUNT(`SensorID`) FROM sensor_details WHERE `UserID`=$userid && `LastSeen` > DATE_SUB(NOW(), INTERVAL 3 DAY);";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $amount = $data['COUNT(`SensorID`)'];
            }
            return $amount;
        } else {
            echo("Offline count - No results");
        }
    } else {
        echo "Error - 6 (gLSSC)";
    }
}

function getAlertSCount($userid) {
    global $dbconn;
    $sqlq = "SELECT COUNT(`SensorID`) FROM sensor_details WHERE `UserID`=$userid && );";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $amount = $data['COUNT(`SensorID`)'];
            }
            return $amount;
        } else {
            echo("Offline count - No results");
        }
    } else {
        echo "Error - 6 (gLSSC)";
    }
}

function addNewSensor() {
    global $dbconn;
    $sql = <<<SQLStr
    INSERT INTO $tblname (`Date/Time`, `SensorID`, `SensorName`, `Temperature`, `Humidity`) 
    VALUES ('$sqltime', '$sensorid', '$sensorname', '$temp', '$hum');
    SQLStr;

    $sql2 = ("SELECT `SensorID`, `APIKey` FROM `sensor_details` WHERE `SensorID`=$sensorid");

    $result = (mysqli_query($dbconn, $sql2));

    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                if ($data['APIKey'] == $apikey) {
                    if (mysqli_query($dbconn, $sql)) {
                        echo "New record created successfully";
                    } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($dbconn);
                    }
                }
            }
        } else {
            echo ("Invalid API Key");
        }
    } else {
        echo ("Error - 7 (aNS)");
    }

}

function cleanInput($in) {
    $in = trim($in);
    $in = htmlspecialchars($in);
    $in = stripslashes($in);
    return $in;
}

function getAccCreateDate($userid) {
    global $dbconn;
    $sqlq = "SELECT `created_at` FROM users WHERE `id`=$userid;";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $date = $data['created_at'];
            }
            return $date;
        } else {
            echo("Offline count - No results");
        }
    } else {
        echo "Error - 8 (gACD)";
    }
}

function getAccEmailAd($userid) {
    global $dbconn;
    $sqlq = "SELECT `email` FROM users WHERE `id`=$userid;";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $add = $data['email'];
            }
            return $add;
        } else {
            echo("Offline count - No results");
        }
    } else {
        echo "Error - 9 (gAEA)";
    }
}

function resetPass($userid, $cpass, $npass) {

}


function changeEmail($userid, $email, $cpass) { // TODO : prepared statements
    global $dbconn;
    $sqlq = "SELECT `email`, `password` FROM users WHERE `id`=$userid;";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $dbemail = $data['email'];
                $hashedpwd = $data['password'];
            }
            if (!empty($emaiL) && $dbemail != $email) { // probs want email changed | not else if cos want to do both
                $sqlq3 = "UPDATE users SET `email`=$email` WHERE `id`=$userid`;";
                if (mysqli_query($dbconn, $sqlq3)) {
                    echo "Record updated! <br>";
                } else {
                    echo "Error: " . $sqlq3 . "<br>" . mysqli_error($dbconn);
                }
            }
        } else {
            echo("Updateacc - No results");
        }
    } else {
        echo "Error - 11 (cE)";
    }

}

function isUserAdmin($userid) {
    global $dbconn;
    $sqlq = "SELECT `UserRole` FROM users WHERE `id`=$userid;";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $role = $data["UserRole"];
            }
            if ($role == "1") {
                return "User";
            } else if ($role == "2") {
                return "Admin";
            }
        } else {
            echo("iUA - No results");
            return "Error";
        }
    } else {
        echo "Error - 12 (iUA)";
        return "Error";
    }
}

function getAccPwChange($userid) {
    global $dbconn;
    $sqlq = "SELECT `LastPWChange` FROM users WHERE `id`=$userid;";
    $result = mysqli_query($dbconn, $sqlq);
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                $add = $data['LastPWChange'];
            }
            return $add;
        } else {
            echo("Offline count - No results");
        }
    } else {
        echo "Error - 13 (gAPC)";
    }
}

function addLoginAttempt($userid, $ip, $attempt_type) { // this is qutie dodgy, any data can be sent to attempt type. lets hope it doesnt get broken
    global $dbconn;
    $sqlq = "INSERT INTO access_attempts (`UserID`, `ip_address`, `attempt_type`) VALUES ('$userid', '$ip', '$attempt_type')";
    if (mysqli_query($dbconn, $sqlq)) {
        return "Success"; // use return cos dont want to display this
    } else {
        return "Error: " . $sqlq . "<br>" . mysqli_error($dbconn);
    }
}

// don't put any html outside tag ffs
?>