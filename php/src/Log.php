<?php

namespace Jubayed\A2;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FirePHPHandler;

class Log
{
    private $logger;

    public function __construct($file_action = "")
    {
        if ($this->logger == null) {

            // the default date format is "Y-m-d\TH:i:sP"
            $dateFormat = "Y-m-d H:i:s";

            // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
            // we now change the default output format according to our needs.
            $output = "[%datetime%] [file_{$file_action}] %message%".PHP_EOL;

            // finally, create a formatter

            // finally, create a formatter
            $formatter = new LineFormatter($output, $dateFormat);

            // Create a handler
            $stream = new StreamHandler($this->get_path(), Level::Debug);
            $stream->setFormatter($formatter);


            // Create the logger
            $this->logger = new Logger($file_action);
            $this->logger->pushHandler(new FirePHPHandler());
            $this->logger->pushHandler($stream);
        }
    }


    private function get_path()
    {
        return a2_path('log/template.log');
    }

    // /**
    //  * System is unusable.
    //  *
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  */
    // public function emergency(string|\Stringable $message, array $context = []){
    //     $this->logger->emergency( $message, $context);
    // }

    // /**
    //  * Action must be taken immediately.
    //  *
    //  * Example: Entire website down, database unavailable, etc. This should
    //  * trigger the SMS alerts and wake you up.
    //  *
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  */
    // public function alert(string|\Stringable $message, array $context = []){
    //     $this->logger->alert($message, $context);
    // }

    // /**
    //  * Critical conditions.
    //  *
    //  * Example: Application component unavailable, unexpected exception.
    //  *
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  */
    // public function critical(string|\Stringable $message, array $context = []){
    //     $this->logger->critical($message, $context);
    // }

    // /**
    //  * Runtime errors that do not require immediate action but should typically
    //  * be logged and monitored.
    //  *
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  */
    // public function error(string|\Stringable $message, array $context = []){
    //     $this->logger->error($message, $context);
    // }

    // /**
    //  * Exceptional occurrences that are not errors.
    //  *
    //  * Example: Use of deprecated APIs, poor use of an API, undesirable things
    //  * that are not necessarily wrong.
    //  *
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  */
    // public function warning(string|\Stringable $message, array $context = []){
    //     $this->logger->warning($message, $context);
    // }

    // /**
    //  * Normal but significant events.
    //  *
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  */
    // public function notice(string|\Stringable $message, array $context = []){
    //     $this->logger->notice($message, $context);
    // }

    // /**
    //  * Interesting events.
    //  *
    //  * Example: User logs in, SQL logs.
    //  *
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  */
    private function info(string $file_action, string $path)
    {

        $this->logger->info($path);
    }

    // /**
    //  * Detailed debug information.
    //  *
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  */
    // public function debug(string|\Stringable $message, array $context = []){
    //     $this->logger->debug($message, $context);
    // }

    // /**
    //  * Logs with an arbitrary level.
    //  *
    //  * @param mixed   $level
    //  * @param string|\Stringable $message
    //  * @param mixed[] $context
    //  *
    //  * @return void
    //  *
    //  * @throws \Psr\Log\InvalidArgumentException
    //  */
    // public function log($level, string|\Stringable $message, array $context = []){


    //     $this->info($message);
    // }

    // /**
    //  * Dynamically proxy method calls to the underlying logger.
    //  *
    //  * @param  string  $method
    //  * @param  array  $parameters
    //  * @return mixed
    //  */
    // public function __call($method, $parameters)
    // {
    //     echo "test";
    //     exit("Ok");
    //     return $this->logger->{$method}(...$parameters);
    // }

    public static function newAction($label)
    {
        return new static($label);
    }


    public static function __callStatic($method, array $arguments)
    {
        if (count($arguments) != 2) {
            return '';
        }

        return static::newAction($arguments[0])->logger->{$method}(...[$arguments[1]]);
    }
}
