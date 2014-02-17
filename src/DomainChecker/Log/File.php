<?php

namespace DomainChecker\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class File
    extends ALog
    implements ILog
{
    private $logger = null;

    public function writeLog($message, $type)
    {
        $logger_type = Logger::INFO;

        switch ($type) {
            case 'info':
                $logger_type = Logger::INFO;
                break;
            case 'comment':
                $logger_type = Logger::NOTICE;
                break;
            case 'error':
                $logger_type = Logger::ERROR;
        }

        $this->getLogger()->addRecord($logger_type, $message);
    }

    private function getLogger()
    {
        if (null === $this->logger) {
            if (null === $this->output) {
                throw new \LogicException('filename must be define');
            }

            $this->logger = new Logger('domain_checker');
            $this->logger->pushHandler(new StreamHandler($this->output));
        }

        return $this->logger;
    }
}