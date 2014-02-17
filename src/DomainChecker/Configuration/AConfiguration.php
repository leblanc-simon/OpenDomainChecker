<?php

namespace DomainChecker\Configuration;

use DomainChecker\Ssh\Command;
use DomainChecker\Filesystem\Directory;

abstract class AConfiguration
{
    protected $ssh = null;
    protected $directory = null;
    protected $ip = null;


    public function __construct(Command $ssh, $ip, Directory $directory)
    {
        $this->ssh = $ssh;
        $this->ip = $ip;
        $this->setDirectory($directory);

        // Init server connection
        $this->ssh->setServer($this->ip);
    }


    private function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }
}