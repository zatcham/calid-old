<?php

require ($document_root . "/newdir/include/variables.php");

class Database {

    public static function Connect(): mysqli {
        global $db_name, $db_host, $db_username, $db_password;
        $dbconn = new mysqli($db_host, $db_username, $db_password, $db_name);
        // Check conenction
        if ($dbconn->connect_error) {
            die("DB connection failed: " . $dbconn->connect_error);
        } else {
            return $dbconn;
        }
    }

    public function createTables() {
        // TODO : Maybe this
    }



}