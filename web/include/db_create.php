<?php
require_once ("variables.php");
require_once ("classes/Database.php");
//require ("classes/Logging.php");

// Add any new tables to the table list
$table_list = ["sensor_data", "sensor_details",
    "users", "access_attempts", "user_roles", "access_keys",
    "sign_up_verification", "password_resets", "ui_settings",
    "sensor_alerts", "sensor_datatypes", "sensor_statustypes",
    "sensor_units",];

// Tables with prefilled data must have a second query executed
$tables_with_data = ["sensor_datatypes", "sensor_statustypes", "sensor_units", "user_roles",];

// Then create a heredoc variable with the same name as in the list, prefixed with "tbl_", containing the sql query to create
// TODO : add strings
/// Sensor related tables
$tbl_sensor_data = <<<TBL
CREATE TABLE `sensor_data` (
	`EventID` INT(11) NOT NULL AUTO_INCREMENT,
	`Date/Time` DATETIME NOT NULL DEFAULT current_timestamp(),
	`SensorID` INT(11) NOT NULL,
	`SensorName` VARCHAR(11) NOT NULL COLLATE 'utf8mb4_general_ci',
	`Temperature` FLOAT NOT NULL,
	`Humidity` FLOAT NOT NULL,
	`UV` FLOAT NULL DEFAULT NULL,
	PRIMARY KEY (`EventID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

$tbl_sensor_details = <<<TBL
CREATE TABLE `sensor_details` (
	`SensorID` INT(11) NOT NULL AUTO_INCREMENT,
	`UserID` INT(11) NOT NULL,
	`SensorName` VARCHAR(64) NOT NULL COLLATE 'utf8mb4_general_ci',
	`SensorType` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`SensorLoc` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`DataTypes` TINYTEXT NOT NULL COLLATE 'utf8mb4_general_ci',
	`LastSeen` DATETIME NULL DEFAULT NULL,
	`APIKey` VARCHAR(11) NOT NULL COLLATE 'utf8mb4_general_ci',
	`SharedWith` INT(11) NULL DEFAULT NULL,
	`SWVersion` FLOAT NULL DEFAULT NULL,
	`show_on_avg` INT(11) NULL DEFAULT NULL,
	`Status` INT(11) NULL DEFAULT '5',
	PRIMARY KEY (`SensorID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

$tbl_sensor_alerts = <<<TBL
CREATE TABLE `sensor_alerts` (
	`AlertID` INT(11) NOT NULL AUTO_INCREMENT,
	`SensorID` INT(11) NOT NULL DEFAULT '0',
	`Date/Time` DATETIME NOT NULL DEFAULT current_timestamp(),
	`AlertType` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_general_ci',
	`AlertReason` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_general_ci',
	`LinksTo` INT(11) NOT NULL DEFAULT '0',
	`Checked` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`AlertID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

// Contains prefilled data - TODO
$tbl_sensor_datatypes = <<<TBL
CREATE TABLE `sensor_datatypes` (
	`ID` INT(11) NOT NULL AUTO_INCREMENT,
	`Name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`ID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

// Contains prefilled data - TODO
$tbl_sensor_statustypes = <<<TBL
CREATE TABLE `sensor_statustypes` (
	`ID` INT(11) NOT NULL AUTO_INCREMENT,
	`Name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`ID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

$tbl_sensor_units = <<<TBL
CREATE TABLE `sensor_units` (
	`ID` INT(11) NOT NULL AUTO_INCREMENT,
	`Name` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_general_ci',
	`Unit` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`ID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

/// User related tables
$tbl_users = <<<TBL
CREATE TABLE `users` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
	`password` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci',
	`email` VARCHAR(64) NOT NULL COLLATE 'utf8mb4_general_ci',
	`UserRole` INT(64) NOT NULL,
	`created_at` DATETIME NULL DEFAULT current_timestamp(),
	`LastPWChange` DATETIME NOT NULL DEFAULT current_timestamp(),
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `username` (`username`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

$tbl_access_attempts = <<<TBL
CREATE TABLE `access_attempts` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NOT NULL,
	`ip_address` VARCHAR(64) NOT NULL COLLATE 'utf8mb4_general_ci',
	`date_time` DATETIME NOT NULL DEFAULT current_timestamp(),
	`attempt_type` VARCHAR(64) NOT NULL COLLATE 'utf8mb4_general_ci',
	`user_agent` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`id`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

// Contains prefilled data
$tbl_user_roles = <<<TBL
CREATE TABLE `user_roles` (
	`ID` INT(11) NOT NULL AUTO_INCREMENT,
	`Name` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	PRIMARY KEY (`ID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

$tbl_access_keys = <<<TBL
CREATE TABLE `access_keys` (
	`ID` INT(11) NOT NULL AUTO_INCREMENT,
	`Key` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_general_ci',
	`Generated_By` INT(11) NOT NULL DEFAULT '0',
	`Timestamp` DATETIME NOT NULL DEFAULT current_timestamp(),
	`Used` TINYINT(4) NOT NULL DEFAULT '0',
	PRIMARY KEY (`ID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

$tbl_sign_up_verification = <<<TBL
CREATE TABLE `sign_up_verification` (
	`ID` INT(11) NOT NULL AUTO_INCREMENT,
	`UserID` INT(11) NOT NULL DEFAULT '0',
	`Key` VARCHAR(50) NOT NULL DEFAULT '0' COLLATE 'utf8mb4_general_ci',
	`Used` INT(11) NOT NULL DEFAULT '0',
	`EmailSent` INT(11) NOT NULL DEFAULT '0',
	`Timestamp` DATETIME NOT NULL DEFAULT current_timestamp(),
	PRIMARY KEY (`ID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

$tbl_password_resets = <<<TBL
CREATE TABLE `password_resets` (
	`ID` INT(11) NOT NULL AUTO_INCREMENT,
	`UserID` INT(11) NULL DEFAULT NULL,
	`Key` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
	`Timestamp` DATETIME NULL DEFAULT current_timestamp(),
	`Used` INT(11) NULL DEFAULT '0',
	`EmailSent` INT(11) NULL DEFAULT '0',
	PRIMARY KEY (`ID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

$tbl_ui_settings = <<<TBL
CREATE TABLE `ui_settings` (
	`ID` INT(11) NOT NULL AUTO_INCREMENT,
	`UserID` INT(11) NOT NULL,
	`LightModeNav` INT(11) NOT NULL DEFAULT '0',
	`DarkModePage` INT(11) NOT NULL DEFAULT '0',
	`ShowNavToggleAlways` INT(11) NOT NULL DEFAULT '0',
	`TempUnit` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`ID`) USING BTREE
)
ENGINE=InnoDB
;
TBL;

// Heredoc variables for the tables with prefilled data, these will be executed after the initial create
$data_sensor_datatypes = <<<DATA
INSERT IGNORE INTO `sensor_datatypes` (`ID`, `Name`) VALUES
	(1, 'Temperature'),
	(2, 'Humidity'),
	(3, 'UV'),
	(4, 'Temperature, Humidity'),
	(5, 'Humidity, UV'),
	(6, 'Temperature, Humidity and UV'),
	(7, 'Temperature, UV')
;
DATA;

$data_sensor_statustypes = <<<DATA
INSERT IGNORE INTO `sensor_statustypes` (`ID`, `Name`) VALUES
	(1, 'Online'),
	(2, 'Offline'),
	(3, 'Error'),
	(4, 'Alert'),
	(5, 'New')
;
DATA;

$data_sensor_units = <<<DATA
INSERT IGNORE INTO `sensor_units` (`ID`, `Name`, `Unit`) VALUES
	(1, 'Temperature', 'C'),
	(2, 'Humidity', '%'),
	(3, 'UV', 'Index')
;
DATA;

$data_user_roles = <<<DATA
INSERT IGNORE INTO `user_roles` (`ID`, `Name`) VALUES
	(1, 'Standard User'),
	(2, 'Administrator'),
	(3, 'Unverified')
;
DATA;

// Coennect to database
$dbconn = Database::Connect();
// Check for errors during connect
if ($dbconn->connect_error) {
    die ("Connection failed: " . $dbconn->connect_error);
}

// Counts
$errors = $success = 0;

// If no error, iterate through the array
foreach ($table_list as $i => $item) {
    echo "<br>"; // New line for each item
    $prefix = "tbl_"; // Prefix for variable
    $var_name = $prefix . $item; // concatenate varialbe name
    if (isset($var_name)) { // cehck variable exists
        echo ("$var_name variable exists, proceeding...");
        if (!empty(${$var_name})) { // check the var isnt empty too
            // if not empty
            $query = ${$var_name}; // Concatenated variable from prefix
            if ($dbconn->query($query)) {
                // If query is successfull
                echo ("<br>");
                echo ("Table $var_name created successfully.");
                echo ("<br>");
                $success += 1;
            } else {
                echo ("<br>");
                echo ("Error creating the table $var_name.");
                echo ("<br>");
                $errors += 1;
            }
        } else {
            // var is empty
            echo ("<br>");
            echo ("$var_name variable is empty, skipping...");
            echo ("<br>");
            $errors += 1;
        }
    }
}

// Now do the same for the tables that need data

// Counts
$data_success = $data_errors = 0;

foreach ($tables_with_data as $i => $item) {
    echo "<br>"; // New line for each item
    $prefix = "data_"; // Prefix for variable
    $var_name = $prefix . $item; // concatenate varialbe name
    if (isset($var_name)) { // cehck variable exists
        echo ("$var_name variable exists, proceeding...");
        if (!empty(${$var_name})) { // check the var isnt empty too
            // if not empty
            $query = ${$var_name}; // Concatenated variable from prefix
            if ($dbconn->query($query)) {
                // If query is successfull
                echo ("<br>");
                echo ("Data added to table $var_name successfully.");
                echo ("<br>");
                $data_success += 1;
            } else {
                echo ("<br>");
                echo ("Error adding data to table $var_name.");
                echo ("<br>");
                $data_errors += 1;
            }
        } else {
            // var is empty
            echo ("<br>");
            echo ("$var_name variable is empty, skipping...");
            echo ("<br>");
            $data_errors += 1;
        }
    }
}

// Close the connection once all is done - should do this more often in the codebase
$dbconn->close();

// Output the results
echo ("<hr>");
echo ("$success table(s) created successfully");
echo ("<br>");
echo ("$errors table(s) were not created due to errors");
echo ("<br>");
echo ("$data_success table(s) had data added successfully");
echo ("<br>");
echo ("$data_errors table(s) did not have data added due to errors");
?>