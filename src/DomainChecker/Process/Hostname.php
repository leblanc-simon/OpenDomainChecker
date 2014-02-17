<?php

namespace DomainChecker\Process;

use DomainChecker\Ssh\Command;

class Hostname
{
    private $server = null;
    private $ssh = null;

    public function __construct(Command $ssh, $server = null)
    {
        $this->ssh = $ssh;
        if (null !== $server) {
            $this->setServer($server);
        }
    }

    public function setServer($domain)
    {
        $this->server = $domain;
        return $this;
    }

    public function getHostname()
    {
        try {
            return trim($this->ssh
                ->setServer($this->server)
                ->exec('hostname')
                ->getOutput());
        } catch (\RuntimeException $e) {
            return null;
        }
    }
}