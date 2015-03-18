<?php

class Logger {

    private $filehandle;

    const DATE_FORMAT = 'D M d H:i:s Y';
    const DEF_LOG_LEVEL = 'ALL';
    const DEF_LOG_LOCATION = '/home/jvillegas/www/cis444/log/app_log';

    public function __construct(
        $dest = Logger::DEF_LOG_LOCATION, 
        $lev = Logger::DEF_LOG_LEVEL, 
        $dfmt = Logger::DATE_FORMAT) {

        $this->filehandle = fopen($dest, 'a');
    }

    public function __destruct() {
        fclose($this->filehandle);
    }

    private function convertLogLevel($level) {
        $level = strtoupper($level);
        switch ($level) {
            case "OFF":
                return(255);
            case "ALL":
                return(32);
            case "TRACE":
                return(16);
            case "DEBUG":
                return(8);
            case "INFO":
                return(4);
            case "WARN":
                return(2);
        }
        // Default is errors only
        return(1);
    }

    public function log($error = '', $client = 'unspecified', $loglevel = 1) {
        if (empty($error) || empty($client)) {
            return(false);
        }

        $level = $this->convertLogLevel($loglevel);
        if ($level <= $this->convertLogLevel(Logger::DEF_LOG_LEVEL)) {
            fwrite($this->filehandle, "[" . date(Logger::DATE_FORMAT) . "] [" . $loglevel . "] [client " . $client . "] " . $error . "\n");
        }
    }

}

