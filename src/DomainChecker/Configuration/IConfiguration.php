<?php

namespace DomainChecker\Configuration;

use DomainChecker\Ssh\Command;
use DomainChecker\Filesystem\Directory;

interface IConfiguration
{
    public function __construct(Command $ssh, $ip, Directory $directory);
    public function is();
    public function save();
}