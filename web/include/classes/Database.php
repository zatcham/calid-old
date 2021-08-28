<?php

$document_root = $_SERVER['DOCUMENT_ROOT'];
require ($document_root . "/include/variables.php");
require ("Logging.php");

class Database {
    // Connects to the database. Used everywhere in Calid's functs
    public static function Connect(): mysqli {
        global $db_name, $db_host, $db_username, $db_password;
        $dbconn = new mysqli($db_host, $db_username, $db_password, $db_name);
        // Check conenction
        if ($dbconn->connect_error) {
            Logging::log('error', "SQL Error occured in Database/Connect. Details: $dbconn->connect_error");
            die("DB connection failed: " . $dbconn->connect_error);
        } else {
            return $dbconn;
        }
    }

    // Creates the tables, if i ever get round to making this funct
    public function createTables() {
        // TODO : Maybe this
    }

}