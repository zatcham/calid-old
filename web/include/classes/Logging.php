<?php

require_once __DIR__  . '/../../vendor/autoload.php'; // hopefully importing on other pages works too???

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Logging {
    // Contains the functions for logging
    private static function checkDebug() {
        // TODO
        return True;
    }

    public static function log($type, $message) {
        $log = new Logger('Calid');
        $log->setTimezone(new \DateTimeZone('UTC'));
//        $path = $_SERVER['DOCUMENT_ROOT'] . '\calid.log';
        $path = __DIR__ . '..\..\calid.log';
        if (self::checkDebug()) {
            $log->pushHandler(new StreamHandler($path, Logger::DEBUG));
        } else {
            $log->pushHandler(new StreamHandler($path, Logger::WARNING));
        }

        if (strtolower($type) == "warning") {
            $log->warning($message);
        } elseif (strtolower($type) == "error") {
            $log->error($message);
        } elseif (strtolower($type) == "info") {
            $log->info($message);
        } elseif (strtolower($type) == "debug") {
            $log->debug($message);
        } elseif (strtolower($type) == "critical") {
            $log->critical($message);
        } else {
            return False;
        }

        return True;
    }
}