<?php

namespace DomainChecker\Log;

interface ILog
{
    public function setOutput($output);
    public function log($message);
    public function logInfo($message);
    public function logComment($message);
    public function logError($message);
    public function writeLog($message, $type);
}