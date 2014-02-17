<?php

namespace DomainChecker\Process;

use Symfony\Component\Process\ProcessBuilder;

class Ping
{
    private $server = null;

    public function __construct($server = null)
    {
        if (null !== $server) {
            $this->setServer(domain);
        }
    }


    public function setServer($domain)
    {
        $this->server = $domain;

        return $this;
    }

    public function check()
    {
        $builder = new ProcessBuilder(array(
            'ping',
            '-c', '1',
            '-w', '2',
            $this->server
        ));

        $process = $builder->getProcess();
        $process->run();

        return $process->isSuccessful();
    }
}