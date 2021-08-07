<?php
// TODO : this
include("include/variables.php");
include("include/db_connect.php");

$sensorid = cleaninput($_GET["devid"]);
$sensorname = cleaninput($_GET["devname"]);
$temp = cleaninput($_GET["temp"]);
$hum = cleaninput($_GET["hum"]);
$uv = cleaninput($_GET["uv"]);
$sqltime = (date("Y-m-d H:i:s")); // time is utc i think | get our own time cos cant trust esp
$tblname = ($db_prefix . "_data");
// new stuff
$apikey = cleaninput($_GET["apikey"]);


// testing str : http://localhost:63343/sensor_logger/newdir/data_upload.php?devid=1001&devname=test1&temp=20&hum=20&uv=3&apikey=Testing2

//echo ($sqltime);
//echo ($tblname);
// TODO : SQL injection - move to prepared
$sql = <<<SQLStr
    INSERT INTO $tblname (`Date/Time`, `SensorID`, `SensorName`, `Temperature`, `Humidity`)
    VALUES ('$sqltime', '$sensorid', '$sensorname', '$temp', '$hum');
    SQLStr;

$sqli = "INSERT INTO $tblname (`SensorID`, `SensorName`, `Temperature`, `Humidity`, `UV`) VALUES (?, ?, ?, ?, ?)";

$sqlq = "SELECT `SensorID`, `APIKey` FROM `sensor_details` WHERE `SensorID`=?;";
$result = (mysqli_query($dbconn, $sqlq));



if (!empty($sensorid) & !empty($sensorname) & !empty($temp) & !empty($hum)) { // TODO: conv to prepared
    if ($stmt = mysqli_prepare($dbconn, $sqlq)) {
        mysqli_stmt_bind_param($stmt, "s", $sensorid);
        if (mysqli_stmt_execute($stmt)) {

        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    if ($result != False) {
        if (mysqli_num_rows($result) > 0) {
            while ($data = mysqli_fetch_assoc($result)) {
                if ($data['APIKey'] == $apikey) {
                    echo ("API key ok! <br>");
                    if ($stmt = mysqli_prepare($dbconn, $sqli)) {
                        mysqli_stmt_bind_param($stmt, "sssss", $sensorid, $sensorname, $temp, $hum, $uv);
                        if (mysqli_stmt_execute($stmt)) {

                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }
                    mysqli_stmt_close($stmt);
                }
            }
        } else {
            echo ("Invalid API Key");
        }
    } else {
//    echo ("Error - 1");
        echo "Error: " . $sqlq . "<br>" . mysqli_error($dbconn);
    }
} else {
    echo ("Not all required GET attributes set!");
}

mysqli_close($dbconn);

function cleanInput($in) {
    $in = trim($in);
    $in = htmlspecialchars($in);
    $in = stripslashes($in);
    return $in;
}

?>