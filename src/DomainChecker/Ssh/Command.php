<?php

namespace DomainChecker\Ssh;

use Symfony\Component\Process\ProcessBuilder;

class Command
{
    private $private_key    = null;
    private $options        = array();
    private $server         = null;
    private $username       = null;

    private $return_cmd     = null;

    private $builder        = null;

    public function __construct($private_key, $options = array(), $username = null, $server = null)
    {
        $this->setPrivateKey($private_key);
        $this->setOptions($options);
        $this->setUsername($username);
        $this->setServer($server);
    }


    public function exec($command)
    {
        $this->return_cmd = null;
        $this->buildCommand($command);

        $process = $this->builder->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException($process->getErrorOutput());
        }

        $this->return_cmd = $process->getOutput();

        return $this;
    }


    public function getOutput()
    {
        return $this->return_cmd;
    }


    public function setPrivateKey($private_key)
    {
        if (file_exists($private_key) === false) {
            throw new \InvalidArgumentException('The private key doesn\'t exist');
        }

        $this->private_key = $private_key;
        return $this;
    }


    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }


    public function setServer($server)
    {
        $this->server = $server;
        return $this;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    private function buildCommand($command)
    {
        $options = array(
            '-i',
            $this->private_key,
        );
        $options = array_merge($options, $this->options);

        $arguments = array_merge($options, array($this->getUsernameServer(), $command));

        $this
            ->getBuilder()
            ->setArguments($arguments);
    }

    private function getBuilder()
    {
        if ($this->builder === null) {
            $this->builder = new ProcessBuilder();
            $this->builder->setPrefix('ssh');
        }

        return $this->builder;
    }


    private function getUsernameServer()
    {
        return $this->username.'@'.$this->server;
    }
}