<?php
include("variables.php");
// TODO : Turn all into function
$dbconn = mysqli_connect($db_host, $db_username, $db_password);
// Check conenction
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (mysqli_select_db($dbconn, $db_name)) {
    echo "Database exists <br>";
    $tbl_name = ($db_prefix . "_data"); // TODO : Check string is ok
    $sql = <<<SQLstr
    CREATE TABLE `$tbl_name` (
      `EventID` int NOT NULL AUTO_INCREMENT,
      `Date/Time` datetime NOT NULL,
      `SensorID` int NOT NULL,
      `SensorName` varchar(11) NOT NULL,
      `Temperature` float NOT NULL,
      `Humidity` float NOT NULL,
      PRIMARY KEY (`EventID`)
    ) ENGINE=InnoDB
  SQLstr;
    if (mysqli_query($dbconn, $sql)) {
        echo ("Table created successfully with the name " . $tbl_name);
    } else {
        echo ("Error creating table: " . mysqli_error($dbconn));
    }
} else {
    echo ("Database does not exist, creating " . $db_name);
    $sql = ("CREATE DATABASE " . $db_name);
    if ($dbconn->query($sql)) {
        echo "Database created successfully";
        mysqli_select_db($dbconn, $db_name);
        $tbl_name = ($db_prefix . "_data"); // TODO : Check string is ok
        $sql = <<<SQLstr
    CREATE TABLE `$tbl_name` (
      `EventID` int NOT NULL AUTO_INCREMENT,
      `Date/Time` datetime NOT NULL,
      `SensorID` int NOT NULL,
      `SensorName` varchar(11) NOT NULL,
      `Temperature` float NOT NULL,
      `Humidity` float NOT NULL,
      PRIMARY KEY (`EventID`)
    ) ENGINE=InnoDB
  SQLstr;
        if (mysqli_query($dbconn, $sql)) {
            echo ("Table created successfully with the name " . $tbl_name);
        } else {
            echo ("Error creating table: " . mysqli_error($dbconn));
        }
    } else {
        echo "Error creating database: " . $conn->error;
    }
}

// TODO : Create table relations

// closing connection
mysqli_close($dbconn);
?>