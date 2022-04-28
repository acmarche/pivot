<?php
use Symfony\Config\MonologConfig;

return static function (MonologConfig $monolog) {
    // this "file_log" key could be anything
    $monolog->handler('file_log')
        ->type('stream')
        // log to var/logs/(environment).log
        ->path('%kernel.logs_dir%/%kernel.environment%.log')
        // log *all* messages (debug is lowest level)
        ->level('debug');

    $monolog->handler('syslog_handler')
        ->type('syslog')
        // log error-level messages and higher
        ->level('error');
};