<?php

namespace DomainChecker\Log;

abstract class ALog
{
    protected $output = null;

    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Output an info log
     *
     * @param   string  $message
     * @alias   self::logInfo
     */
    public function log($message)
    {
        $this->logInfo($message);
    }

    /**
     * Output an info log
     *
     * @param   string  $message
     */
    public function logInfo($message)
    {
        $this->writeLog($message, 'info');
    }

    /**
     * Output an error log
     *
     * @param   string  $message
     */
    public function logError($message)
    {
        $this->writeLog($message, 'error');
    }

    /**
     * Output a comment log
     *
     * @param   string  $message
     */
    public function logComment($message)
    {
        $this->writeLog($message, 'comment');
    }

    abstract public function writeLog($message, $type);
}