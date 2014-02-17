<?php

namespace DomainChecker\Log;

class Multi
    extends ALog
    implements ILog
{
    private $loggers = array();

    public function setOutput($output)
    {
        throw new \LogicException('You must not set output with Multi class : see addLogger');
    }


    public function addLogger(ILog $logger)
    {
        $this->loggers[] = $logger;
    }

    public function writeLog($message, $type)
    {
        foreach ($this->loggers as $logger) {
            $logger->writeLog($message, $type);
        }
    }
}