<?php
class Logger {
    private static $logFile = '../logs/app.log';
    
    public static function init() {
        $logDir = dirname(self::$logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }
    
    public static function log($message, $level = 'INFO') {
        self::init();
        
        $date = date('Y-m-d H:i:s');
        $logMessage = "[$date][$level] $message" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }
    
    public static function error($message) {
        self::log($message, 'ERROR');
    }
    
    public static function info($message) {
        self::log($message, 'INFO');
    }
    
    public static function debug($message) {
        self::log($message, 'DEBUG');
    }
} 