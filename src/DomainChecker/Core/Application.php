<?php

namespace DomainChecker\Core;

use DomainChecker\Command\Domain;
use DomainChecker\Command\Server;
use DomainChecker\Controler\Controler;
use Symfony\Component\Console\Application as ConsoleApplication;

class Application
{
    static public function run()
    {
        $application = new ConsoleApplication();
        $application->add(new Server());
        $application->add(new Domain());

        $application->run();
    }


    static public function web()
    {
        $controler = new Controler();
        $controler->init();
        $controler->dispatch();
        $controler->run();
    }
}