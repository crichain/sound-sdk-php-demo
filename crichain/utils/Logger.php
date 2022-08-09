<?php
namespace crichain\utils;


use Monolog\Logger as Monolog;
use Monolog\Handler\StreamHandler;

class Logger {
    static public function getLogger() {
        $logger = new Monolog('main');
        $logger->pushHandler(new StreamHandler('/tmp/crichain.log', Monolog::INFO));
        return $logger;
    }
}